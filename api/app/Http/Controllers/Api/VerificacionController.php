<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificado;

class VerificacionController extends Controller
{
    public function show(string $token)
    {
        $certificado = Certificado::where('qr_token', $token)
            ->with(['soldador', 'empresa', 'norma', 'inspector'])
            ->firstOrFail();

        return response()->json([
            'numero'             => $certificado->numero,
            'anio'               => $certificado->anio,
            'tipo'               => $certificado->tipo,
            'soldador'           => trim(($certificado->soldador?->apellido ?? '') . ' ' . ($certificado->soldador?->nombre ?? '')),
            'dni'                => $certificado->soldador?->dni,
            'empresa'            => $certificado->empresa?->nombre,
            'norma'              => $certificado->norma?->nombre,
            'proceso'            => $certificado->proceso,
            'resultado'          => $certificado->resultado,
            'fecha_calificacion' => $certificado->fecha_calificacion?->toDateString(),
            'fecha_vencimiento'  => $certificado->fecha_vencimiento?->toDateString(),
            'estado'             => $certificado->estado,
            'revision'           => $certificado->revision,
            'inspector'          => $certificado->inspector?->nombre,
        ]);
    }
}
