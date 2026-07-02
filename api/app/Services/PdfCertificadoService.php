<?php

namespace App\Services;

use App\Models\CertLayout;
use App\Models\Certificado;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class PdfCertificadoService
{
    public function generate(Certificado $cert): string
    {
        $cert->loadMissing(['norma.parent']);

        // Use custom layout if one exists for this norma
        if ($cert->norma && $layout = CertLayout::resolveForNorma($cert->norma)) {
            return app(PdfFromLayoutService::class)->generate($cert, $layout);
        }

        $cert->loadMissing([
            'soldador', 'empresa', 'norma', 'inspector',
            'jointDesign', 'tests.testType', 'passes.proceso', 'ranges',
        ]);

        $qrDataUri           = $this->qrDataUri($cert->qr_token);
        $kosoldarLogoDataUri = $this->imageDataUri(public_path('images/logos/kosoldar.png'));
        $normaLogoDataUri    = $this->normaLogoDataUri($cert->norma?->nombre ?? '');
        $fotoDataUri         = $cert->soldador?->foto_path
            ? $this->imageDataUri(storage_path('app/' . $cert->soldador->foto_path))
            : null;
        $firmaDataUri        = $cert->inspector?->firma_path
            ? $this->imageDataUri(storage_path('app/' . $cert->inspector->firma_path))
            : null;

        $template = $this->resolveTemplate($cert->norma?->nombre ?? '');
        $html = view($template, compact(
            'cert', 'qrDataUri', 'kosoldarLogoDataUri', 'normaLogoDataUri',
            'fotoDataUri', 'firmaDataUri',
        ))->render();

        $mpdf = $this->makeMpdf();
        $mpdf->WriteHTML($html);

        $filename = $this->buildFilename($cert);
        $outputDir = config('mpdf.output_dir');
        $mpdf->Output($outputDir . DIRECTORY_SEPARATOR . $filename, \Mpdf\Output\Destination::FILE);

        $relativePath = 'pdfs/' . $filename;
        $cert->updateQuietly(['pdf_path' => $relativePath]);

        return $relativePath;
    }

    public function stream(Certificado $cert): \Mpdf\Mpdf
    {
        $cert->loadMissing([
            'soldador', 'empresa', 'norma', 'inspector',
            'jointDesign', 'tests.testType', 'passes.proceso', 'ranges',
        ]);

        $qrDataUri           = $this->qrDataUri($cert->qr_token);
        $kosoldarLogoDataUri = $this->imageDataUri(public_path('images/logos/kosoldar.png'));
        $normaLogoDataUri    = $this->normaLogoDataUri($cert->norma?->nombre ?? '');
        $fotoDataUri         = $cert->soldador?->foto_path
            ? $this->imageDataUri(storage_path('app/' . $cert->soldador->foto_path))
            : null;
        $firmaDataUri        = $cert->inspector?->firma_path
            ? $this->imageDataUri(storage_path('app/' . $cert->inspector->firma_path))
            : null;

        $template = $this->resolveTemplate($cert->norma?->nombre ?? '');
        $html = view($template, compact(
            'cert', 'qrDataUri', 'kosoldarLogoDataUri', 'normaLogoDataUri',
            'fotoDataUri', 'firmaDataUri',
        ))->render();

        $mpdf = $this->makeMpdf();
        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function resolveTemplate(string $normaName): string
    {
        return match (true) {
            str_contains($normaName, '1104')  => 'pdf.rcs-api1104',
            str_contains($normaName, 'NAG')   => 'pdf.rcs-nag',
            default                           => 'pdf.rcs-aws-asme',
        };
    }

    private function makeMpdf(): Mpdf
    {
        $cfg = config('mpdf');

        return new Mpdf([
            'mode'          => $cfg['mode'],
            'format'        => $cfg['format'],
            'orientation'   => $cfg['orientation'],
            'margin_left'   => $cfg['margin_left'],
            'margin_right'  => $cfg['margin_right'],
            'margin_top'    => $cfg['margin_top'],
            'margin_bottom' => $cfg['margin_bottom'],
            'margin_header' => $cfg['margin_header'],
            'margin_footer' => $cfg['margin_footer'],
            'tempDir'       => $cfg['temp_dir'],
        ]);
    }

    private function qrDataUri(string $token): string
    {
        $url = rtrim(config('app.url'), '/') . '/api/verificar/' . $token;

        $result = (new Builder(
            data: $url,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 200,
            margin: 5,
        ))->build();

        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    private function normaLogoDataUri(string $normaName): ?string
    {
        $file = match (true) {
            in_array($normaName, ['ASME IX', 'ASME B31.3', 'ASME B31.8']) => 'asme.png',
            in_array($normaName, ['AWS D1.1', 'AWS D1.3', 'AWS D1.6'])    => 'aws-d11.png',
            $normaName === 'API 1104'                                      => 'api-1104.png',
            default                                                        => null,
        };

        return $file ? $this->imageDataUri(public_path('images/logos/' . $file)) : null;
    }

    private function imageDataUri(string $path): ?string
    {
        if (!file_exists($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    private function buildFilename(Certificado $cert): string
    {
        $apellido = Str::slug($cert->soldador?->apellido ?? 'soldador');
        $dni      = preg_replace('/\D/', '', $cert->soldador?->dni ?? '');
        $proceso  = Str::slug($cert->proceso ?? '');
        $norma    = Str::slug($cert->norma?->nombre ?? '');
        $fecha    = $cert->fecha_calificacion?->format('d_m_Y') ?? '';

        return sprintf(
            'RCS%s_%s-%s-%s-%s-%s-%s-REV%s.pdf',
            $cert->numero,
            substr((string) $cert->anio, -2),
            $apellido,
            $dni,
            $proceso,
            $norma,
            $fecha,
            $cert->revision
        );
    }
}
