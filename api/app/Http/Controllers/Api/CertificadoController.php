<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCertificadoRequest;
use App\Http\Requests\UpdateCertificadoRequest;
use App\Models\Certificado;
use App\Models\Norma;
use App\Services\CertificateTestSyncService;
use App\Services\PdfCertificadoService;
use App\Services\RangeCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CertificadoController extends Controller
{
    public function __construct(
        private RangeCalculatorService      $rangeCalc,
        private PdfCertificadoService       $pdfService,
        private CertificateTestSyncService  $testSync,
    ) {}

    private function withRelations(): array
    {
        return ['soldador', 'empresa', 'norma', 'usuario', 'inspector', 'jointDesign', 'tests.testType', 'passes.proceso', 'ranges'];
    }

    public function index(Request $request)
    {
        $query = Certificado::with($this->withRelations());

        if ($request->filled('soldador_id')) {
            $query->where('soldador_id', $request->soldador_id);
        }
        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($outer) use ($q) {
                $outer->whereHas('soldador', function ($sq) use ($q) {
                    $sq->whereRaw('LOWER(apellido) LIKE ?', ["%".strtolower($q)."%"])
                       ->orWhereRaw('LOWER(nombre) LIKE ?', ["%".strtolower($q)."%"])
                       ->orWhere('dni', 'LIKE', "%{$q}%");
                })->orWhere('numero', 'LIKE', "%{$q}%");
            });
        }

        $dir = $request->input('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';

        switch ($request->input('sort_by')) {
            case 'norma':
                $query->orderBy(
                    \App\Models\Norma::select('nombre')
                        ->whereColumn('normas.id', 'certificados.norma_id')
                        ->limit(1),
                    $dir
                );
                break;
            case 'numero':
                $query->orderBy('anio', $dir)->orderBy('numero', $dir);
                break;
            case 'proceso':
            case 'estado':
            case 'tipo':
            case 'fecha_vencimiento':
                $query->orderBy($request->input('sort_by'), $dir);
                break;
            default:
                $query->orderBy('anio', 'desc')->orderBy('numero', 'desc');
        }

        return response()->json($query->paginate($request->integer('per_page', 20)));
    }

    public function store(StoreCertificadoRequest $request)
    {
        $isDraft = $request->boolean('borrador');

        $data    = $request->validated();
        $passes  = $data['passes'] ?? [];
        unset($data['passes']);

        $data['usuario_id'] = $request->user()->id;
        $data['qr_token']   = Str::uuid()->toString();
        $data['revision']   = $data['revision'] ?? 0;

        if ($isDraft) {
            $data['estado'] = 'borrador';
        } else {
            $data['estado'] = ($data['resultado'] ?? 'aprobado') === 'aprobado' ? 'vigente' : 'suspendido';
        }

        if (isset($data['norma_id'])) {
            $this->autoVigencia($data, (int) $data['norma_id']);

            if (!$isDraft) {
                $nagCheck = $this->checkNagSoldadorData($data);
                if ($nagCheck) return $nagCheck;
            }
        }

        $certificado = Certificado::create($data);

        if (!empty($passes)) {
            $this->syncPasses($certificado, $passes);
        }

        if (!$isDraft) {
            $this->rangeCalc->calculate($certificado);
            $this->testSync->sync($certificado->load('norma'));
            $this->tryGeneratePdf($certificado);
        }

        return response()->json($certificado->load($this->withRelations()), 201);
    }

    public function show(Certificado $certificado)
    {
        return response()->json($certificado->load($this->withRelations()));
    }

    public function update(UpdateCertificadoRequest $request, Certificado $certificado)
    {
        $data     = $request->validated();
        $passes   = array_key_exists('passes', $data) ? $data['passes'] : false;
        unset($data['passes']);
        $wasDraft = $certificado->estado === 'borrador';

        if (isset($data['resultado'])) {
            $data['estado'] = $data['resultado'] === 'aprobado' ? 'vigente' : 'suspendido';
        }

        if (array_key_exists('fecha_calificacion', $data)) {
            $normaId = (int) ($data['norma_id'] ?? $certificado->norma_id);
            $this->autoVigencia($data, $normaId);
        }

        // NAG check when transitioning out of borrador or changing to a NAG norma
        if (!$wasDraft || isset($data['norma_id'])) {
            $checkData = [
                'norma_id'    => (int) ($data['norma_id'] ?? $certificado->norma_id),
                'soldador_id' => (int) ($data['soldador_id'] ?? $certificado->soldador_id),
            ];
            if (($data['estado'] ?? $certificado->estado) !== 'borrador') {
                $nagCheck = $this->checkNagSoldadorData($checkData);
                if ($nagCheck) return $nagCheck;
            }
        }

        $certificado->update($data);

        if ($passes !== false) {
            $this->syncPasses($certificado, $passes ?? []);
        }

        $fresh = $certificado->fresh();
        if (
            ($wasDraft && isset($data['estado']) && $data['estado'] !== 'borrador') ||
            array_key_exists('variables', $data) ||
            array_key_exists('posicion', $data) ||
            array_key_exists('tipo_cupon', $data)
        ) {
            $this->rangeCalc->calculate($fresh);
            $this->testSync->sync($fresh->load('norma'));
        }

        return response()->json($fresh->load($this->withRelations()));
    }

    public function destroy(Certificado $certificado)
    {
        if ($certificado->pdf_path && file_exists(storage_path('app/'.$certificado->pdf_path))) {
            unlink(storage_path('app/'.$certificado->pdf_path));
        }
        $certificado->delete();
        return response()->json(null, 204);
    }

    public function renovar(Request $request, Certificado $certificado)
    {
        $data = $request->validate([
            'fecha_calificacion' => 'required|date',
            'fecha_vencimiento'  => 'nullable|date|after:fecha_calificacion',
            'resultado'          => 'required|in:aprobado,rechazado',
            'eps_numero'         => 'nullable|string|max:50',
            'inspector_id'       => 'nullable|exists:inspectors,id',
            'variables'          => 'nullable|array',
            'observaciones'      => 'nullable|string',
        ]);

        $renovData = [
            'fecha_calificacion' => $data['fecha_calificacion'],
            'fecha_vencimiento'  => $data['fecha_vencimiento'] ?? null,
            'norma_id'           => $certificado->norma_id,
            'soldador_id'        => $certificado->soldador_id,
        ];
        $this->autoVigencia($renovData, (int) $certificado->norma_id);
        $nagCheck = $this->checkNagSoldadorData($renovData);
        if ($nagCheck) return $nagCheck;

        $nuevo = Certificado::create([
            'numero'             => $certificado->numero,
            'anio'               => $certificado->anio,
            'soldador_id'        => $certificado->soldador_id,
            'empresa_id'         => $certificado->empresa_id,
            'norma_id'           => $certificado->norma_id,
            'usuario_id'         => $request->user()->id,
            'inspector_id'       => $data['inspector_id'] ?? $certificado->inspector_id,
            'tipo'               => 'renovacion',
            'revision'           => $certificado->revision + 1,
            'resultado'          => $data['resultado'],
            'fecha_calificacion' => $renovData['fecha_calificacion'],
            'fecha_vencimiento'  => $renovData['fecha_vencimiento'],
            'estado'             => $data['resultado'] === 'aprobado' ? 'vigente' : 'suspendido',
            'eps_numero'         => $data['eps_numero'] ?? $certificado->eps_numero,
            'pqr_numero'         => $certificado->pqr_numero,
            'proceso'            => $certificado->proceso,
            'posicion'           => $certificado->posicion,
            'progresion'         => $certificado->progresion,
            'tipo_cupon'         => $certificado->tipo_cupon,
            'variables'          => $data['variables'] ?? $certificado->variables,
            'observaciones'      => $data['observaciones'] ?? null,
            'qr_token'           => Str::uuid()->toString(),
        ]);

        $certificado->update(['estado' => 'vencido']);

        // Inherit passes from parent cert
        $this->syncPasses($nuevo, $certificado->load('passes')->passes->map(fn($p) => $p->toArray())->toArray());

        $this->rangeCalc->calculate($nuevo);
        $this->testSync->sync($nuevo->load('norma'));
        $this->tryGeneratePdf($nuevo);

        return response()->json($nuevo->load($this->withRelations()), 201);
    }

    public function ampliar(Request $request, Certificado $certificado)
    {
        $data = $request->validate([
            'empresa_id'         => 'sometimes|exists:empresas,id',
            'norma_id'           => 'sometimes|exists:normas,id',
            'fecha_calificacion' => 'required|date',
            'fecha_vencimiento'  => 'nullable|date|after:fecha_calificacion',
            'resultado'          => 'required|in:aprobado,rechazado',
            'eps_numero'         => 'nullable|string|max:50',
            'inspector_id'       => 'nullable|exists:inspectors,id',
            'proceso'            => 'nullable|string|max:20',
            'posicion'           => 'nullable|string|max:10',
            'progresion'         => 'nullable|in:ascendente,descendente',
            'variables'          => 'nullable|array',
            'observaciones'      => 'nullable|string',
        ]);

        $ampliNormaId = (int) ($data['norma_id'] ?? $certificado->norma_id);
        $ampliData = [
            'fecha_calificacion' => $data['fecha_calificacion'],
            'fecha_vencimiento'  => $data['fecha_vencimiento'] ?? null,
            'norma_id'           => $ampliNormaId,
            'soldador_id'        => $certificado->soldador_id,
        ];
        $this->autoVigencia($ampliData, $ampliNormaId);
        $nagCheck = $this->checkNagSoldadorData($ampliData);
        if ($nagCheck) return $nagCheck;

        $nuevo = Certificado::create([
            'numero'             => $certificado->numero,
            'anio'               => $certificado->anio,
            'soldador_id'        => $certificado->soldador_id,
            'empresa_id'         => $data['empresa_id']   ?? $certificado->empresa_id,
            'norma_id'           => $ampliNormaId,
            'usuario_id'         => $request->user()->id,
            'inspector_id'       => $data['inspector_id'] ?? $certificado->inspector_id,
            'tipo'               => 'ampliacion',
            'revision'           => $certificado->revision + 1,
            'resultado'          => $data['resultado'],
            'fecha_calificacion' => $ampliData['fecha_calificacion'],
            'fecha_vencimiento'  => $ampliData['fecha_vencimiento'],
            'estado'             => $data['resultado'] === 'aprobado' ? 'vigente' : 'suspendido',
            'eps_numero'         => $data['eps_numero']   ?? $certificado->eps_numero,
            'pqr_numero'         => $certificado->pqr_numero,
            'proceso'            => $data['proceso']      ?? $certificado->proceso,
            'posicion'           => $data['posicion']     ?? $certificado->posicion,
            'progresion'         => $data['progresion']   ?? $certificado->progresion,
            'tipo_cupon'         => $certificado->tipo_cupon,
            'variables'          => $data['variables']    ?? $certificado->variables,
            'observaciones'      => $data['observaciones'] ?? null,
            'qr_token'           => Str::uuid()->toString(),
        ]);

        // Inherit passes from parent cert (ampliar keeps same welding variables)
        $this->syncPasses($nuevo, $certificado->loadMissing('passes')->passes->map(fn($p) => $p->toArray())->toArray());

        $this->rangeCalc->calculate($nuevo);
        $this->testSync->sync($nuevo->load('norma'));
        $this->tryGeneratePdf($nuevo);

        return response()->json($nuevo->load($this->withRelations()), 201);
    }

    public function recalcular(Certificado $certificado)
    {
        $fresh  = $certificado->fresh()->load('norma');
        $ranges = $this->rangeCalc->calculate($fresh);
        $this->testSync->sync($fresh);

        return response()->json([
            'ranges'  => $ranges,
            'version' => RangeCalculatorService::VERSION,
        ]);
    }

    public function pdf(Certificado $certificado)
    {
        $path = $this->pdfService->generate($certificado);
        $fullPath = storage_path('app/' . $path);

        return response()->file($fullPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
        ]);
    }

    // ── Private ──────────────────────────────────────────────────────────────

    /**
     * For NAG norms, soldador must have fecha_nacimiento and nacionalidad
     * (required by Form 513-780-0 credencial).
     */
    private function checkNagSoldadorData(array $data): ?\Illuminate\Http\JsonResponse
    {
        $normaName = Norma::find($data['norma_id'] ?? 0)?->nombre ?? '';
        if (!str_contains(strtolower($normaName), 'nag')) {
            return null;
        }
        $soldador = \App\Models\Soldador::find($data['soldador_id'] ?? 0);
        $missing  = [];
        if (!$soldador?->fecha_nacimiento) $missing[] = 'fecha de nacimiento';
        if (!$soldador?->nacionalidad)     $missing[] = 'nacionalidad';
        if (empty($missing)) return null;

        return response()->json([
            'message' => 'El soldador debe tener ' . implode(' y ', $missing) .
                         ' completos para certificados NAG 105 (Form 513-780-0).',
            'errors'  => ['soldador' => ['Completar perfil del soldador antes de emitir este certificado.']],
        ], 422);
    }

    /**
     * Auto-set fecha_vencimiento for NAG norms (2 years from fecha_calificacion).
     * Only fires if fecha_vencimiento is absent or null in $data.
     */
    private function autoVigencia(array &$data, int $normaId): void
    {
        if (!empty($data['fecha_vencimiento'])) {
            return;
        }
        $fechaCal = $data['fecha_calificacion'] ?? null;
        if (!$fechaCal) {
            return;
        }
        $normaName = Norma::find($normaId)?->nombre ?? '';
        if (str_contains(strtolower($normaName), 'nag')) {
            $data['fecha_vencimiento'] = Carbon::parse($fechaCal)->addYears(2)->toDateString();
        }
    }

    private function syncPasses(Certificado $cert, array $passes): void
    {
        $cert->passes()->delete();
        foreach ($passes as $i => $row) {
            $cert->passes()->create([
                'orden'                => $row['orden']                ?? ($i + 1),
                'etiqueta'             => $row['etiqueta']             ?? "Pasada " . ($i + 1),
                'proceso_id'           => $row['proceso_id']           ?? null,
                'clasificacion_aporte' => $row['clasificacion_aporte'] ?? null,
                'diametro_aporte_mm'   => $row['diametro_aporte_mm']   ?? null,
                'polaridad'            => $row['polaridad']            ?? null,
                'amperaje_min'         => $row['amperaje_min']         ?? null,
                'amperaje_max'         => $row['amperaje_max']         ?? null,
                'voltaje_min'          => $row['voltaje_min']          ?? null,
                'voltaje_max'          => $row['voltaje_max']          ?? null,
                'velocidad_avance_min' => $row['velocidad_avance_min'] ?? null,
                'velocidad_avance_max' => $row['velocidad_avance_max'] ?? null,
                'progresion'           => $row['progresion']           ?? null,
            ]);
        }
    }

    private function tryGeneratePdf(Certificado $cert): void
    {
        try {
            $this->pdfService->generate($cert);
        } catch (\Throwable $e) {
            Log::warning('PDF generation failed for cert ' . $cert->id . ': ' . $e->getMessage());
        }
    }
}
