<?php

namespace App\Services;

use App\Models\CertLayout;
use App\Models\Certificado;
use App\Models\TestType;
use Mpdf\Mpdf;

class PdfFromLayoutService
{
    public function generate(Certificado $cert, CertLayout $layout): string
    {
        $mpdf = $this->renderToMpdf($cert, $layout);

        $filename  = $this->buildFilename($cert, $layout);
        $outputDir = config('mpdf.output_dir');
        $mpdf->Output($outputDir . DIRECTORY_SEPARATOR . $filename, \Mpdf\Output\Destination::FILE);

        $relativePath = 'pdfs/' . $filename;
        $cert->updateQuietly(['pdf_path' => $relativePath]);

        return $relativePath;
    }

    /**
     * Devuelve el HTML interno de un bloque dinámico (sin wrapper absoluto) para previsualizar
     * en el browser sin generar PDF. Usa estilos en px/pt compatibles con browser.
     */
    public function renderBlockHtml(array $block, Certificado $cert): string
    {
        $cert->loadMissing([
            'soldador', 'empresa', 'norma', 'inspector',
            'jointDesign', 'tests.testType', 'passes.proceso', 'ranges',
        ]);

        // Dummy layout para buildDataContext
        $dummyLayout = new CertLayout();
        $ctx  = $this->buildDataContext($cert, $dummyLayout);
        $type = $block['type'] ?? '';

        $inner = match ($type) {
            'variables_block'    => $this->renderVariablesBlock($cert, $ctx, $block),
            'results_block'      => $this->renderResultsBlock($cert, $block),
            'passes_block'       => $this->renderPassesBlock($cert, $block),
            'joint_design_block' => $this->renderJointDesignBlock($cert),
            'header_block'       => implode('', array_map(
                fn($sub) => '<div style="margin-bottom:2px;">' . $this->renderBlock($sub, $ctx, $cert) . '</div>',
                $block['blocks'] ?? []
            )),
            default              => '<em>Tipo no previsualizable: ' . e($type) . '</em>',
        };

        return "<div style=\"font-family:Arial,sans-serif;padding:8px;\">{$inner}</div>";
    }

    /**
     * Return a configured Mpdf instance ready to Output() — used by preview endpoint.
     */
    public function stream(Certificado $cert, CertLayout $layout): Mpdf
    {
        return $this->renderToMpdf($cert, $layout);
    }

    // ── Core rendering ────────────────────────────────────────────────────────

    private function renderToMpdf(Certificado $cert, CertLayout $layout): Mpdf
    {
        $cert->loadMissing([
            'soldador', 'empresa', 'norma', 'inspector',
            'jointDesign', 'tests.testType', 'passes.proceso', 'ranges',
        ]);

        $ctx = $this->buildDataContext($cert, $layout);
        $html = $this->buildHtml($cert, $layout, $ctx);

        $isLandscape = $layout->orientacion === 'landscape';
        $mpdf = new Mpdf([
            'mode'          => config('mpdf.mode', 'utf-8'),
            'format'        => $isLandscape ? 'A4-L' : 'A4',
            'orientation'   => $isLandscape ? 'L' : 'P',
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir'       => config('mpdf.temp_dir'),
        ]);
        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    private function buildHtml(Certificado $cert, CertLayout $layout, array $ctx): string
    {
        $blocks = $layout->blocks;
        $pages  = $blocks['pages'] ?? [['blocks' => $blocks]];

        // Bloques con shared=true definidos en página 1 se inyectan en TODAS las páginas.
        $sharedBlocks = array_values(
            array_filter($pages[0]['blocks'] ?? [], fn($b) => !empty($b['shared']))
        );

        // mPDF only honors absolute left/top on elements that are DIRECT children
        // of <body> — any wrapping div (even an unstyled one) breaks `left`.
        $html = '<html><body>';
        foreach ($pages as $i => $page) {
            if ($i > 0) {
                $html .= '<div style="page-break-before:always;"></div>';
                foreach ($sharedBlocks as $block) {
                    $html .= $this->renderRootBlock($block, $ctx, $cert);
                }
            }
            foreach ($page['blocks'] ?? [] as $block) {
                $html .= $this->renderRootBlock($block, $ctx, $cert);
            }
        }
        $html .= '</body></html>';

        return $html;
    }

    /**
     * Render un bloque raíz (hijo directo de body en mPDF).
     * header_block es especial: expande sus sub-bloques directamente como
     * hermanos en el body, desplazando el Y por la posición del contenedor.
     * (Necesario porque mPDF sólo respeta position:absolute en hijos directos de body.)
     */
    private function renderRootBlock(array $block, array $ctx, Certificado $cert): string
    {
        // Cualquier bloque contenedor (con sub-bloques propios) se expande directamente en body.
        $containerTypes = ['header_block', 'soldador_block'];
        if (in_array($block['type'] ?? '', $containerTypes) && !empty($block['blocks'])) {
            $baseY  = (float)($block['y'] ?? 0);
            $html   = '';
            foreach ($block['blocks'] ?? [] as $sub) {
                $sub['y'] = (float)($sub['y'] ?? 0) + $baseY;
                $html .= $this->renderBlock($sub, $ctx, $cert);
            }
            return $html;
        }

        return $this->renderBlock($block, $ctx, $cert);
    }

    private function renderBlock(array $block, array $ctx, Certificado $cert): string
    {
        $style = $this->absoluteStyle($block);
        $inner = match ($block['type'] ?? '') {
            'field'               => e((string) ($ctx[$block['fieldKey'] ?? ''] ?? '')),
            'text'                => $this->interpolate($block['content'] ?? '', $ctx),
            'image'               => $this->renderImage($block, $cert),
            'line'                => $this->renderLine($block),
            'variables_block'     => $this->renderVariablesBlock($cert, $ctx, $block),
            'results_block'       => $this->renderResultsBlock($cert, $block),
            'passes_block'        => $this->renderPassesBlock($cert, $block),
            'joint_design_block'  => $this->renderJointDesignBlock($cert),
            default               => '',
        };

        return "<div style=\"{$style}\">{$inner}</div>";
    }

    // ── Block renderers ───────────────────────────────────────────────────────

    private function renderImage(array $block, Certificado $cert): string
    {
        $src = match ($block['imageType'] ?? '') {
            'logo'       => $this->imageDataUri(public_path('images/logos/kosoldar.png')),
            'norma_logo' => $this->normaLogoDataUri($cert->norma?->nombre ?? ''),
            'photo'      => $cert->soldador?->foto_path
                                ? $this->imageDataUri(storage_path('app/' . $cert->soldador->foto_path))
                                : null,
            'signature'  => $cert->inspector?->firma_path
                                ? $this->imageDataUri(storage_path('app/' . $cert->inspector->firma_path))
                                : null,
            'qr'         => $this->qrDataUri($cert->qr_token ?? ''),
            default      => null,
        };

        if (!$src) {
            return '';
        }

        return "<img src=\"{$src}\" style=\"width:100%;height:100%;object-fit:contain;\">";
    }

    private function renderLine(array $block): string
    {
        $style  = ($block['style'] ?? []);
        $color  = $style['color'] ?? '#000000';
        $isV    = ($block['direction'] ?? 'horizontal') === 'vertical';
        $border = "border-{$color}";

        return $isV
            ? "<div style=\"border-left:0.3mm solid {$color};height:100%;\"></div>"
            : "<div style=\"border-top:0.3mm solid {$color};width:100%;\"></div>";
    }

    /**
     * Filas fijas (orden y rótulos bilingües) replicando la tabla "Welding Variables /
     * Variables de Soldadura" de los certificados RCS reales — ver info/RCS_LAYOUT_REFERENCE.md.
     * API 1104 = 3 col (Variable/Condición/Comentario, sin Rango — F4 de la referencia);
     * ASME IX / AWS D1.1-D1.6 / API 650 / B31.x / IRAM / NAG = 4 col con Rango Calificado
     * (F1 de la referencia), tomado de cert->ranges (motor de reglas, ver CertificateRange).
     */
    private function renderVariablesBlock(Certificado $cert, array $ctx, array $block = []): string
    {
        $cfg       = $block['config'] ?? [];
        $ts        = $this->tableStyle($cfg);
        $isApi1104 = str_contains($cert->norma?->nombre ?? '', 'API 1104');
        $showRange    = ($cfg['showRange']    ?? true) && !$isApi1104;
        $showComments = $cfg['showComments'] ?? true;

        $v      = $cert->variables ?? [];
        $ranges = $cert->ranges->groupBy('type');

        $rangeFor = function (string $type) use ($ranges): string {
            return $ranges->has($type) ? $ranges[$type]->pluck('descripcion')->implode(' / ') : '';
        };

        $rows = $isApi1104
            ? $this->api1104VariableRows($ctx, $v)
            : $this->asmeVariableRows($cert, $ctx, $v, $rangeFor);

        // Aplicar orden y visibilidad configurados por el usuario
        $rowOrder   = $cfg['rowOrder']   ?? null;
        $hiddenRows = array_flip((array)($cfg['hiddenRows'] ?? []));

        if ($rowOrder) {
            $indexed = array_column($rows, null, 'key');
            $rows    = array_values(array_filter(
                array_map(fn($k) => $indexed[$k] ?? null, $rowOrder),
                fn($r) => $r !== null,
            ));
        }

        $rows = array_filter($rows, fn(array $r) =>
            !isset($hiddenRows[$r['key'] ?? ''])
            && (($r['value'] ?? '') !== '' || ($r['labelExtra'] ?? '') !== '')
        );
        if (!$rows) {
            return '';
        }

        $colCount = 1 + 1 + ($showRange ? 1 : 0) + ($showComments ? 1 : 0);
        $labelW   = $colCount === 4 ? '24' : ($colCount === 3 ? '30' : '40');
        $condW    = $colCount === 4 ? '28' : ($colCount === 3 ? '35' : '60');

        $bodyHtml = '';
        $idx      = 0;
        foreach ($rows as $r) {
            $rowBg  = $ts['rowBg']  ? "background:{$ts['rowBg']};"  : '';
            $altBg  = $ts['altBg']  ? "background:{$ts['altBg']};"  : '';
            $bg     = $idx % 2 === 0 ? $rowBg : ($altBg ?: $rowBg);
            $tdBase = "{$ts['pad']}font-size:{$ts['fs']};{$ts['border']}{$bg}";

            $bodyHtml .= '<tr>'
                . "<td style=\"{$tdBase}font-weight:{$ts['lFw']};color:{$ts['lColor']};width:{$labelW}%;\">" . e($r['label']) . ($r['labelExtra'] ?? '') . '</td>'
                . "<td style=\"{$tdBase}color:{$ts['vColor']};width:{$condW}%;\">" . e((string)$r['value']) . '</td>';

            if ($showRange) {
                $bodyHtml .= "<td style=\"{$tdBase}color:{$ts['vColor']};width:30%;\">" . e(($r['range'] ?? '') !== '' ? $r['range'] : '—') . '</td>';
            }
            if ($showComments) {
                $bodyHtml .= "<td style=\"{$tdBase}color:{$ts['vColor']};width:18%;\">" . e($r['comments'] ?? '') . '</td>';
            }

            $bodyHtml .= '</tr>';
            $idx++;
        }

        $hBgCss = $ts['hBg'] ? "background:{$ts['hBg']};" : '';
        $thStyle    = "{$ts['pad']}font-size:{$ts['hFs']};color:{$ts['hColor']};text-align:left;{$ts['border']}{$hBgCss}";
        $thTitle    = "{$ts['pad']}font-size:7.5pt;font-weight:bold;color:{$ts['hColor']};text-align:left;{$ts['border']}{$hBgCss}";
        $headerCols = "<th style=\"{$thStyle}\">Variable</th><th style=\"{$thStyle}\">Condición</th>";
        $headerCols .= $showRange    ? "<th style=\"{$thStyle}\">Rango Calificado</th>" : '';
        $headerCols .= $showComments ? "<th style=\"{$thStyle}\">Comentarios</th>" : '';

        $outerBorder = $ts['border'] === 'border:none;' ? '' : 'border:0.3mm solid #000;';

        return "<table style=\"width:100%;border-collapse:collapse;{$outerBorder}\">
            <thead>
                <tr><th colspan=\"{$colCount}\" style=\"{$thTitle}\">Welding Variables / Variables de Soldadura</th></tr>
                <tr>{$headerCols}</tr>
            </thead>
            <tbody>{$bodyHtml}</tbody>
        </table>";
    }

    private function asmeVariableRows(Certificado $cert, array $ctx, array $v, callable $rangeFor): array
    {
        $proceso       = $ctx['cert.proceso'] ?? '';
        $esGtaw        = str_contains($proceso, 'GTAW');
        $electrodo     = trim(($v['electrodo_raiz'] ?? $v['electrodo'] ?? '') . (isset($v['electrodo_relleno']) ? ' / ' . $v['electrodo_relleno'] : ''));
        $respaldo      = (string)($ctx['cert.respaldo'] ?? '');
        $respaldoRango = str_contains(mb_strtoupper($respaldo), 'SIN') ? 'CON y SIN RESPALDO' : $respaldo;

        $rows = [
            'type_used'            => ['key' => 'type_used',            'label' => 'Type used / Tipo Usado',                               'value' => 'MANUAL', 'range' => 'MANUAL'],
            'backing'              => ['key' => 'backing',              'label' => 'Backing / Respaldo',                                   'value' => $respaldo, 'range' => $respaldoRango],
            'base_metal_pnumber'   => ['key' => 'base_metal_pnumber',   'label' => 'Base metal P/S-Number / Metal Base Número P ó S',      'value' => $ctx['cert.metal_base_pnumber'] ?? '', 'range' => $rangeFor('grupo_base_metal')],
            'filler_class'         => ['key' => 'filler_class',         'label' => 'Filler metal classification / Metal de aporte',        'value' => $electrodo, 'range' => ''],
            'filler_fnumber'       => ['key' => 'filler_fnumber',       'label' => 'Filler metal F-Number(s) / Metal de aporte Números F', 'value' => $v['f_number'] ?? '', 'range' => $rangeFor('grupo_consumible')],
            'tungsten'             => ['key' => 'tungsten',             'label' => 'Tungsten / Tungsteno (GTAW)',                          'value' => $esGtaw ? ($v['tungsteno'] ?? 'NO') : '', 'range' => ''],
        ];

        $jd = $cert->jointDesign;
        if ($jd) {
            $rows['joint_design'] = [
                'key'        => 'joint_design',
                'label'      => 'DISEÑO DE JUNTA',
                'labelExtra' => '<div style="margin-top:0.5mm;max-width:18mm;">' . ($jd->svg ?? '') . '</div>',
                'value'      => $ctx['cert.joint_detail'] ?: ($jd->nombre ?? ''),
                'range'      => '',
            ];
        }

        $rows += [
            'deposit_thickness'    => ['key' => 'deposit_thickness',    'label' => 'Deposit thickness / Espesor depositado para cada proceso', 'value' => $v['espesor_deposito'] ?? '', 'range' => $rangeFor('espesor')],
            'position_progression' => ['key' => 'position_progression', 'label' => 'Position/Progression / Posición/Progresión',               'value' => trim(($ctx['cert.posicion'] ?? '') . ' ' . ($ctx['cert.progresion'] ?? '')), 'range' => $rangeFor('posicion')],
            'gas_type'             => ['key' => 'gas_type',             'label' => 'Type of gas / Tipo de gas (GTAW/GMAW)',                    'value' => $esGtaw ? ($ctx['cert.tipo_gas'] ?? '') : '', 'range' => ''],
            'inert_gas_backing'    => ['key' => 'inert_gas_backing',    'label' => 'Inert gas backing / Respaldo de gas inerte (GTAW)',        'value' => $esGtaw ? ($v['gas_respaldo'] ?? 'NO') : '', 'range' => ''],
            'current_type'         => ['key' => 'current_type',         'label' => 'Current type/polarity / Tipo de corriente/polaridad',      'value' => trim($proceso . ' -' . ($ctx['cert.tipo_corriente'] ?? '')), 'range' => ''],
        ];

        return array_values($rows);
    }

    private function api1104VariableRows(array $ctx, array $v): array
    {
        return [
            ['key' => 'process',            'label' => 'Process / Proceso',                                  'value' => $ctx['cert.proceso'] ?? '', 'range' => ''],
            ['key' => 'backing',            'label' => 'Backing / Respaldo',                                 'value' => trim(($ctx['cert.respaldo'] ?? '') . (isset($v['backing_material']) ? ' — ' . $v['backing_material'] : '')), 'range' => ''],
            ['key' => 'base_metal',         'label' => 'Base metal / Metal base',                            'value' => $ctx['cert.metal_base'] ?? '', 'range' => ''],
            ['key' => 'filler_root',        'label' => 'Filler metal root / Electrodo raíz',                 'value' => $v['electrodo_raiz'] ?? '', 'range' => ''],
            ['key' => 'filler_fill',        'label' => 'Filler metal fill / Electrodo relleno',              'value' => $v['electrodo_relleno'] ?? '', 'range' => ''],
            ['key' => 'current_root',       'label' => 'Current root / Corriente raíz',                      'value' => $v['corriente_raiz'] ?? '', 'range' => ''],
            ['key' => 'current_fill',       'label' => 'Current fill / Corriente relleno',                   'value' => $v['corriente_relleno'] ?? '', 'range' => ''],
            ['key' => 'position_prog',      'label' => 'Position/Progression / Posición/Progresión',         'value' => trim(($ctx['cert.posicion'] ?? '') . ' ' . ($ctx['cert.progresion'] ?? '')), 'range' => ''],
            ['key' => 'num_passes',         'label' => 'Number of passes / Número de pasadas',               'value' => $ctx['cert.num_pasadas'] ?? '', 'range' => ''],
            ['key' => 'time_p1_p2',         'label' => 'Time between passes P1→P2 / Tiempo entre pasadas',   'value' => $ctx['cert.tiempo_p1_p2'] ?? '', 'range' => ''],
            ['key' => 'time_p2_rest',       'label' => 'Time between passes P2→rest / Tiempo entre pasadas', 'value' => $ctx['cert.tiempo_p2_rest'] ?? '', 'range' => ''],
            ['key' => 'preheat',            'label' => 'Preheat temperature / Precalentamiento',             'value' => $ctx['cert.precalentamiento'] ?? '', 'range' => ''],
        ];
    }

    private function renderResultsBlock(Certificado $cert, array $block = []): string
    {
        $cfg            = $block['config'] ?? [];
        $layout         = $cfg['layout'] ?? 'horizontal';
        $showResultText = (bool)($cfg['showResultText'] ?? false);

        $normaId   = $cert->norma_id;
        $testTypes = TestType::where('norma_id', $normaId)->orderBy('id')->get();

        if ($testTypes->isEmpty()) {
            return '';
        }

        $certTests = $cert->tests->keyBy(fn($t) => $t->testType?->code ?? '');
        $chk = fn(bool $c): string => $c ? '&#9632;' : '&#9633;';

        if ($layout === 'vertical') {
            $rows = '';
            foreach ($testTypes as $tt) {
                $certTest  = $certTests->get($tt->code);
                $resultado = $certTest?->resultado ?? 'N/A';
                $checked   = $resultado === 'aprobado';
                $resultCell = $showResultText ? e(mb_strtoupper($resultado)) : '';
                $rows .= "<tr>
                    <td style=\"padding:0.5mm 1mm;font-size:7pt;text-align:center;border:0.3mm solid #000;width:8%;\">{$chk($checked)}</td>
                    <td style=\"padding:0.5mm 1mm;font-size:7pt;border:0.3mm solid #000;\">" . e($tt->nombre ?? $tt->code) . "</td>
                    <td style=\"padding:0.5mm 1mm;font-size:7pt;text-align:center;border:0.3mm solid #000;width:22%;\">{$resultCell}</td>
                </tr>";
            }
            return "<table style=\"width:100%;border-collapse:collapse;\"><tbody>{$rows}</tbody></table>";
        }

        // horizontal (default)
        $cells = '';
        foreach ($testTypes as $tt) {
            $certTest  = $certTests->get($tt->code);
            $resultado = $certTest?->resultado ?? 'na';
            $checked   = $resultado === 'aprobado';
            $resultLine = $showResultText ? '<br><span style="font-size:5.5pt;">' . e(mb_strtoupper($resultado)) . '</span>' : '';
            $cells .= "<td style=\"padding:0.5mm 1mm;font-size:7pt;text-align:center;border:0.3mm solid #000;\">
                {$chk($checked)}<br><span style=\"font-size:6pt;\">{$tt->code}</span>{$resultLine}
            </td>";
        }
        return "<table style=\"width:100%;border-collapse:collapse;\"><tr>{$cells}</tr></table>";
    }

    private function renderPassesBlock(Certificado $cert, array $block = []): string
    {
        $cfg     = $block['config'] ?? [];
        $ts      = $this->tableStyle($cfg);
        $allCols = ['pasada','proceso','clasificacion','diametro','amperaje','voltaje','avance','polaridad','progresion','transferencia'];
        $cols    = !empty($cfg['columns']) ? (array)$cfg['columns'] : $allCols;
        $colSet  = array_flip($cols);

        $passes = $cert->passes->sortBy('orden');
        if ($passes->isEmpty()) {
            return '';
        }

        $colDefs = [
            'pasada'        => 'Pasada',
            'proceso'       => 'Proceso',
            'clasificacion' => 'Clasif.',
            'diametro'      => 'Ø [mm]',
            'amperaje'      => 'A [A]',
            'voltaje'       => 'V [V]',
            'avance'        => 'Avance [cm/min]',
            'polaridad'     => 'Polar.',
            'progresion'    => 'Prog.',
            'transferencia' => 'Transf.',
        ];

        $hBgCss  = $ts['hBg'] ? "background:{$ts['hBg']};" : 'background:#f0f0f0;';
        $thStyle = "{$ts['pad']}font-size:{$ts['hFs']};color:{$ts['hColor']};{$ts['border']}{$hBgCss}";
        $tdBase  = "{$ts['pad']}font-size:{$ts['fs']};{$ts['border']}";

        $header = '<tr>';
        foreach ($colDefs as $key => $label) {
            if (isset($colSet[$key])) {
                $header .= "<th style=\"{$thStyle}\">{$label}</th>";
            }
        }
        $header .= '</tr>';

        $rows = '';
        $idx  = 0;
        foreach ($passes as $pass) {
            $velMin = $pass->velocidad_avance_min !== null ? round($pass->velocidad_avance_min / 10, 1) : null;
            $velMax = $pass->velocidad_avance_max !== null ? round($pass->velocidad_avance_max / 10, 1) : null;
            $vel    = $velMin !== null ? "{$velMin}" . ($velMax ? " – {$velMax}" : '') : '';
            $amp    = ($pass->amperaje_min ?? '') . ($pass->amperaje_max ? ' – ' . $pass->amperaje_max : '');
            $vol    = ($pass->voltaje_min  ?? '') . ($pass->voltaje_max  ? ' – ' . $pass->voltaje_max  : '');

            $rowBg = $idx % 2 === 0
                ? ($ts['rowBg'] ? "background:{$ts['rowBg']};" : '')
                : ($ts['altBg'] ? "background:{$ts['altBg']};" : '');
            $tdStyle = "{$tdBase}color:{$ts['vColor']};{$rowBg}";

            $cellMap = [
                'pasada'        => e($pass->etiqueta ?? ('Pasada ' . $pass->orden)),
                'proceso'       => e($pass->proceso?->nombre ?? ''),
                'clasificacion' => e($pass->clasificacion_aporte ?? ''),
                'diametro'      => e($pass->diametro_aporte_mm ?? ''),
                'amperaje'      => e($amp),
                'voltaje'       => e($vol),
                'avance'        => e($vel),
                'polaridad'     => e($pass->polaridad ?? ''),
                'progresion'    => e($pass->progresion ?? ''),
                'transferencia' => e($pass->tipo_transferencia ?? ''),
            ];

            $rows .= '<tr>';
            foreach ($cellMap as $key => $val) {
                if (isset($colSet[$key])) {
                    $rows .= "<td style=\"{$tdStyle}\">{$val}</td>";
                }
            }
            $rows .= '</tr>';
            $idx++;
        }

        return "<table style=\"width:100%;border-collapse:collapse;\">
            <thead>{$header}</thead>
            <tbody>{$rows}</tbody>
        </table>";
    }

    private function renderJointDesignBlock(Certificado $cert): string
    {
        $jd = $cert->jointDesign;
        if (!$jd) {
            return '';
        }

        $nombre = e($jd->nombre ?? '');
        $svg    = $jd->svg ?? '';

        return "<div style=\"text-align:center;\">
            <div style=\"font-size:7pt;font-weight:bold;margin-bottom:1mm;\">{$nombre}</div>
            <div style=\"max-width:100%;\">{$svg}</div>
        </div>";
    }

    // ── Data context ──────────────────────────────────────────────────────────

    private function buildDataContext(Certificado $cert, CertLayout $layout): array
    {
        $s = $cert->soldador;
        $e = $cert->empresa;
        $n = $cert->norma;
        $i = $cert->inspector;
        $v = $cert->variables ?? [];

        return [
            'cert.numero'             => $cert->numero ?? '',
            'cert.anio'               => substr((string)($cert->anio ?? ''), -2),
            'cert.revision'           => $cert->revision ?? '0',
            'cert.codigo'             => 'RCS' . ($cert->numero ?? '') . '_' . substr((string)($cert->anio ?? ''), -2),
            'cert.tipo'               => $cert->tipo ?? '',
            'cert.resultado'          => mb_strtoupper($cert->resultado ?? ''),
            'cert.fecha_calificacion' => $cert->fecha_calificacion?->format('d/m/Y') ?? '',
            'cert.fecha_vencimiento'  => $cert->fecha_vencimiento?->format('d/m/Y') ?? '',
            'cert.eps_numero'         => $cert->eps_numero ?? '',
            'cert.pqr_numero'         => $cert->pqr_numero ?? '',
            'cert.proceso'            => mb_strtoupper($cert->proceso ?? ''),
            'cert.posicion'           => mb_strtoupper($cert->posicion ?? ''),
            'cert.progresion'         => mb_strtoupper($cert->progresion ?? ''),
            'cert.tipo_cupon'         => $cert->tipo_cupon ?? '',
            'cert.observaciones'      => $cert->observaciones ?? '',
            'cert.joint_detail'       => $cert->joint_detail ?? '',
            // Variables de soldadura (desde cert->variables JSON)
            'cert.metal_base'         => mb_strtoupper($v['especificacion_metal_base'] ?? $v['metal_base'] ?? ''),
            'cert.diametro_espesor'   => $v['diametro_cupon'] ?? $v['espesor_cupon'] ?? '',
            'cert.califica_rangos'    => $v['grupo_base_metal'] ?? '',
            'cert.cliente'            => $v['cliente'] ?? '',
            'cert.obra'               => $v['obra'] ?? '',
            'cert.respaldo'           => mb_strtoupper($v['respaldo'] ?? ''),
            'cert.metal_base_pnumber' => $v['p_number'] ?? '',
            'cert.tipo_gas'           => $v['gas_proteccion'] ?? '',
            'cert.tipo_corriente'     => $v['corriente'] ?? $v['corriente_raiz'] ?? '',
            'cert.num_pasadas'        => (string)($v['num_pasadas'] ?? ''),
            'cert.tiempo_p1_p2'       => $v['tiempo_p1_p2'] ?? '',
            'cert.tiempo_p2_rest'     => $v['tiempo_p2_rest'] ?? '',
            'cert.precalentamiento'   => $v['temperatura_preheat'] ?? '',
            'soldador.apellido'       => mb_strtoupper($s?->apellido ?? ''),
            'soldador.nombre'         => mb_strtoupper($s?->nombre ?? ''),
            'soldador.dni'            => $s?->dni ?? '',
            'soldador.cuño'           => $s?->cuño ?? '',
            'soldador.ciudad'         => mb_strtoupper($s?->ciudad ?? ''),
            'soldador.nacionalidad'   => $s?->nacionalidad ?? '',
            'empresa.nombre'          => mb_strtoupper($e?->nombre ?? 'PARTICULAR'),
            'empresa.ciudad'          => mb_strtoupper($s?->ciudad ?? ''),
            'inspector.nombre'        => $i?->nombre ?? '',
            'inspector.telefono'      => $i?->telefono ?? '',
            'inspector.email'         => $i?->email ?? '',
            'inspector.certificacion' => $i?->certificacion ?? '',
            'norma.nombre'            => $n?->nombre ?? '',
            'norma.edicion_texto'     => $this->normaEdicionTexto($n?->nombre ?? ''),
            'norma.edicion'           => $this->normaEdicion($n?->nombre ?? ''),
            'norma.subtitulo'         => $this->normaSubtitulo($n?->nombre ?? ''),
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Genera CSS tokens para tablas dinámicas a partir de config[].
     * Devuelve array con keys: fs, hBg, hColor, hFs, lFw, lColor, vColor,
     * rowBg, altBg, border, borderH (header border), pad.
     */
    private function tableStyle(array $cfg): array
    {
        $fs   = (float)($cfg['fontSize']       ?? 7);
        $hFs  = (float)($cfg['headerFontSize'] ?? $fs);
        $pH   = (float)($cfg['cellPaddingH']   ?? 1);
        $pV   = (float)($cfg['cellPaddingV']   ?? 0.5);
        $bc   = $cfg['borderColor'] ?? '#000000';
        $noBorder = $bc === 'transparent' || $bc === 'none' || $bc === '';

        return [
            'fs'      => "{$fs}pt",
            'hFs'     => "{$hFs}pt",
            'hBg'     => $cfg['headerBg']       ?? '',
            'hColor'  => $cfg['headerColor']     ?? '#000000',
            'lFw'     => $cfg['labelFontWeight'] ?? 'bold',
            'lColor'  => $cfg['labelColor']      ?? '#000000',
            'vColor'  => $cfg['valueColor']      ?? '#000000',
            'rowBg'   => $cfg['rowBg']           ?? '',
            'altBg'   => $cfg['altRowBg']        ?? '',
            'border'  => $noBorder ? 'border:none;' : "border:0.3mm solid {$bc};",
            'pad'     => "padding:{$pV}mm {$pH}mm;",
        ];
    }

    private function absoluteStyle(array $block): string
    {
        $s      = $block['style'] ?? [];
        $x      = $block['x'] ?? 0;
        $y      = $block['y'] ?? 0;
        $w      = $block['width'] ?? 50;
        $h      = $block['height'] ?? 10;
        $fs     = $s['fontSize'] ?? 10;
        $fw     = ($s['fontWeight'] ?? 'normal') === 'bold' ? 'bold' : 'normal';
        $color  = $s['color'] ?? '#000000';
        $align  = $s['textAlign'] ?? 'left';
        $border = isset($s['borderWidth']) && $s['borderWidth'] > 0
            ? "border:{$s['borderWidth']}px solid " . ($s['borderColor'] ?? '#000000') . ';'
            : '';
        $bg     = !empty($s['backgroundColor'])
            ? "background-color:{$s['backgroundColor']};"
            : '';

        return "position:absolute;left:{$x}mm;top:{$y}mm;width:{$w}mm;height:{$h}mm;"
             . "font-size:{$fs}pt;font-weight:{$fw};color:{$color};text-align:{$align};"
             . "overflow:hidden;{$border}{$bg}";
    }

    private function interpolate(string $template, array $ctx): string
    {
        return preg_replace_callback('/\{\{(\w+(?:\.\w+)*)\}\}/', function ($m) use ($ctx) {
            return e((string)($ctx[$m[1]] ?? ''));
        }, $template);
    }

    private function normaEdicionTexto(string $normaName): string
    {
        return match (true) {
            str_contains($normaName, 'ASME IX')    => 'ASME BOILER AND PRESSURE VESSEL CODE SECTION IX',
            str_contains($normaName, 'AWS D1.1')   => 'AWS D1.1/D1.1M STRUCTURAL WELDING CODE – STEEL',
            str_contains($normaName, 'AWS D1.6')   => 'AWS D1.6/D1.6M STRUCTURAL WELDING CODE – STAINLESS STEEL',
            str_contains($normaName, 'API 1104')   => 'API STANDARD 1104 WELDING OF PIPELINES AND RELATED FACILITIES',
            str_contains($normaName, 'API 650')    => 'API STANDARD 650 WELDED TANKS FOR OIL STORAGE',
            str_contains($normaName, 'ASME B31.3') => 'ASME B31.3 PROCESS PIPING',
            str_contains($normaName, 'ASME B31.8') => 'ASME B31.8 GAS TRANSMISSION AND DISTRIBUTION PIPING SYSTEMS',
            str_contains($normaName, 'IRAM')       => 'IRAM',
            str_contains($normaName, 'NAG')        => 'NAG 105',
            default                                => $normaName,
        };
    }

    private function normaEdicion(string $normaName): string
    {
        return match (true) {
            str_contains($normaName, 'ASME')    => 'Ad 2023',
            str_contains($normaName, 'AWS D1.1') => 'Ad 2020',
            str_contains($normaName, 'API 1104') => 'Ed 2021',
            str_contains($normaName, 'API 650')  => 'Ed 2020',
            str_contains($normaName, 'NAG')      => 'Ed 2018',
            default                              => '',
        };
    }

    private function normaSubtitulo(string $normaName): string
    {
        return match (true) {
            str_contains($normaName, 'API 1104') => 'LINEA REGULAR',
            default                              => '',
        };
    }

    private function qrDataUri(string $token): string
    {
        if (!$token) {
            return '';
        }

        $url    = rtrim(config('app.url'), '/') . '/api/verificar/' . $token;
        $result = (new \Endroid\QrCode\Builder\Builder(
            data: $url,
            errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::Medium,
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

    private function buildFilename(Certificado $cert, CertLayout $layout): string
    {
        $apellido = \Illuminate\Support\Str::slug($cert->soldador?->apellido ?? 'soldador');
        $norma    = \Illuminate\Support\Str::slug($cert->norma?->nombre ?? '');
        $fecha    = $cert->fecha_calificacion?->format('d_m_Y') ?? '';

        return sprintf('RCS%s_%s-%s-%s-layout%d-REV%s.pdf',
            $cert->numero,
            substr((string)$cert->anio, -2),
            $apellido,
            $norma,
            $layout->id,
            $cert->revision
        );
    }
}
