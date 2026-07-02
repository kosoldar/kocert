<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CertLayout;
use App\Models\Certificado;
use App\Services\PdfFromLayoutService;
use Illuminate\Http\Request;

class CertLayoutController extends Controller
{
    public function index()
    {
        // Exclude thumbnail from list to keep payload small
        return response()->json(
            CertLayout::select(['id', 'nombre', 'descripcion', 'norma_ids', 'es_default', 'orientacion', 'thumbnail', 'created_at', 'updated_at'])
                ->orderBy('nombre')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'norma_ids'   => 'nullable|array',
            'norma_ids.*' => 'integer|exists:normas,id',
            'es_default'  => 'boolean',
            'orientacion' => 'in:portrait,landscape',
            'blocks'      => 'required|array',
            'thumbnail'   => 'nullable|string',
        ]);

        $layout = CertLayout::create($data);

        return response()->json($layout, 201);
    }

    public function show(CertLayout $certLayout)
    {
        return response()->json($certLayout);
    }

    public function update(Request $request, CertLayout $certLayout)
    {
        $data = $request->validate([
            'nombre'      => 'sometimes|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'norma_ids'   => 'nullable|array',
            'norma_ids.*' => 'integer|exists:normas,id',
            'es_default'  => 'boolean',
            'orientacion' => 'in:portrait,landscape',
            'blocks'      => 'sometimes|array',
            'thumbnail'   => 'nullable|string',
        ]);

        $certLayout->update($data);

        return response()->json($certLayout);
    }

    public function destroy(CertLayout $certLayout)
    {
        $certLayout->delete();
        return response()->json(null, 204);
    }

    /**
     * Generate a PDF preview using a specific certificate as data source.
     * If no certificado_id is given, uses the most recent non-draft certificate.
     */
    public function preview(Request $request, CertLayout $certLayout, PdfFromLayoutService $pdfService)
    {
        $request->validate([
            'certificado_id' => 'nullable|exists:certificados,id',
        ]);

        $cert = $request->filled('certificado_id')
            ? Certificado::with(['soldador', 'empresa', 'norma', 'inspector', 'jointDesign', 'tests.testType', 'passes.proceso', 'ranges'])->findOrFail($request->certificado_id)
            : Certificado::with(['soldador', 'empresa', 'norma', 'inspector', 'jointDesign', 'tests.testType', 'passes.proceso', 'ranges'])
                ->where('estado', '!=', 'borrador')
                ->latest()
                ->firstOrFail();

        $mpdf = $pdfService->stream($cert, $certLayout);

        $base64 = base64_encode($mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN));

        return response()->json([
            'pdf' => 'data:application/pdf;base64,' . $base64,
        ]);
    }

    /**
     * Devuelve el HTML interno de un bloque dinámico (sin PDF, sin posicionamiento absoluto).
     * Usado por el editor visual para previsualizar el bloque con datos reales.
     */
    public function previewBlock(Request $request, PdfFromLayoutService $pdfService)
    {
        $request->validate([
            'block'          => 'required|array',
            'block.type'     => 'required|string',
            'certificado_id' => 'nullable|exists:certificados,id',
        ]);

        $cert = $request->filled('certificado_id')
            ? Certificado::findOrFail($request->certificado_id)
            : Certificado::where('estado', '!=', 'borrador')->latest()->firstOrFail();

        $html = $pdfService->renderBlockHtml($request->input('block'), $cert);

        return response()->json(['html' => $html]);
    }
}
