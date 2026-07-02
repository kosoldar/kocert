<?php

namespace App\Http\Controllers\Api;

use App\Models\Certificado;
use App\Models\Soldador;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController
{
    public function stats(): JsonResponse
    {
        $hoy         = Carbon::today();
        $en30        = Carbon::today()->addDays(30);

        $totalSoldadores    = Soldador::count();
        $vigentes           = Certificado::where('estado', 'vigente')->count();
        $vencidos           = Certificado::where('estado', 'vencido')->count();
        $porVencer          = Certificado::where('estado', 'vigente')
                                ->whereBetween('fecha_vencimiento', [$hoy, $en30])
                                ->count();
        $emitidosEsteMes    = Certificado::whereMonth('created_at', $hoy->month)
                                ->whereYear('created_at', $hoy->year)
                                ->count();

        $ultimosCerts = Certificado::with(['soldador', 'norma'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn($c) => [
                'id'          => $c->id,
                'numero'      => $c->numero . '/' . $c->anio,
                'soldador'    => $c->soldador->apellido . ', ' . $c->soldador->nombre,
                'norma'       => $c->norma->nombre,
                'estado'      => $c->estado,
                'vencimiento' => $c->fecha_vencimiento,
            ]);

        return response()->json([
            'stats' => [
                'soldadores'       => $totalSoldadores,
                'vigentes'         => $vigentes,
                'vencidos'         => $vencidos,
                'por_vencer'       => $porVencer,
                'emitidos_mes'     => $emitidosEsteMes,
            ],
            'ultimos_certificados' => $ultimosCerts,
        ]);
    }
}
