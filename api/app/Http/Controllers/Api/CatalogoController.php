<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use App\Models\Consumible;
use App\Models\GrupoBaseMetal;
use App\Models\GrupoConsumible;
use App\Models\JointDesign;
use App\Models\Norma;
use App\Models\Posicion;
use App\Models\Proceso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    // GET /api/catalogos/norma/{norma}
    public function porNorma(Norma $norma): JsonResponse
    {
        $norma->loadMissing('parent');
        $effective = $norma->parent ?? $norma;
        $slug      = strtolower($effective->nombre);

        $catalogs = $this->catalogosPorNorma($slug, $effective);

        if (str_contains(strtolower($norma->nombre), 'nag')) {
            $catalogs['campos_norma'] = $this->camposNag($norma->nombre);
        }

        return response()->json([
            'norma'    => $norma->nombre,
            'procesos' => $this->procesosNorma($norma),
            ...$catalogs,
            'metal_consumible'       => $this->metalConsumibleMap($effective),
            'consumible_posiciones'  => $this->consumiblePosicionesMap($effective),
            'metal_calificados'      => $this->metalCalificadosMap($effective),
            'consumible_calificados' => $this->consumibleCalificadosMap($effective),
        ]);
    }

    // GET /api/catalogos/posiciones
    public function posiciones(Request $request): JsonResponse
    {
        $query = Posicion::orderBy('codigo');

        if ($norma = $request->query('norma')) {
            $query->where(function ($q) use ($norma) {
                $q->whereNull('norma')->orWhere('norma', $norma);
            });
        }

        if ($tipo = $request->query('tipo')) {
            $query->where(function ($q) use ($tipo) {
                $q->where('tipo', $tipo)->orWhere('tipo', 'ambas');
            });
        }

        return response()->json($query->get());
    }

    // GET /api/catalogos/normas
    public function normas(): JsonResponse
    {
        return response()->json(Norma::orderBy('nombre')->get());
    }

    // GET /api/catalogos/next-numero
    public function nextNumero(): JsonResponse
    {
        $anio   = now()->year;
        $ultimo = Certificado::where('anio', $anio)->max('numero') ?? 0;
        return response()->json(['numero' => $ultimo + 1, 'anio' => $anio]);
    }

    // GET /api/catalogos/check-numero?numero=X&anio=Y
    public function checkNumero(Request $request): JsonResponse
    {
        $request->validate(['numero' => 'required|integer', 'anio' => 'required|integer']);
        $existe = Certificado::where('numero', $request->numero)
                             ->where('anio', $request->anio)
                             ->exists();
        return response()->json(['disponible' => !$existe]);
    }

    // GET /api/catalogos/procesos
    public function procesos(): JsonResponse
    {
        return response()->json(Proceso::orderBy('nombre')->get());
    }

    // GET /api/catalogos/joint-designs
    public function jointDesigns(): JsonResponse
    {
        return response()->json(
            JointDesign::select('id', 'code', 'nombre')->orderBy('nombre')->get()
        );
    }

    // ── Helpers privados ───────────────────────────────────────────────────────

    private function procesosNorma(Norma $norma): array
    {
        $rows = DB::table('procesos')
            ->join('norma_proceso', 'norma_proceso.proceso_id', '=', 'procesos.id')
            ->where('norma_proceso.norma_id', $norma->id)
            ->orderBy('procesos.nombre')
            ->get(['procesos.id', 'procesos.nombre'])
            ->toArray();

        // Fallback si aún no hay pivots cargados
        if (empty($rows)) {
            return Proceso::orderBy('nombre')->get(['id', 'nombre'])->toArray();
        }

        return array_map(fn($r) => (array) $r, $rows);
    }

    /** { "P-No. 1 Gr. 1": ["F-No. 1", "F-No. 4", "F-No. 6"], ... } */
    private function metalConsumibleMap(Norma $norma): array
    {
        $rows = DB::table('grupo_base_metal_consumible as gbc')
            ->join('grupos_base_metal as gbm', 'gbm.id', '=', 'gbc.grupo_base_metal_id')
            ->join('grupos_consumible as gc',  'gc.id',  '=', 'gbc.grupo_consumible_id')
            ->where('gbm.norma_id', $norma->id)
            ->get(['gbm.codigo as metal', 'gc.codigo as consumible']);

        $map = [];
        foreach ($rows as $row) {
            $map[$row->metal][] = $row->consumible;
        }
        return $map;
    }

    /** { "F-No. 1": ["1G-P", "2G-P", "1G", "2G", "1F", "2F"] } — solo grupos restringidos */
    private function consumiblePosicionesMap(Norma $norma): array
    {
        $rows = DB::table('grupo_consumible_posicion as gcp')
            ->join('grupos_consumible as gc', 'gc.id', '=', 'gcp.grupo_consumible_id')
            ->join('posiciones as p',         'p.id',  '=', 'gcp.posicion_id')
            ->where('gc.norma_id', $norma->id)
            ->get(['gc.codigo as consumible', 'p.codigo as posicion']);

        $map = [];
        foreach ($rows as $row) {
            $map[$row->consumible][] = $row->posicion;
        }
        return $map;
    }

    /** { "P-No. 1 Gr. 1": ["P-No. 1 Gr. 1", "P-No. 1 Gr. 2", ...] } */
    private function metalCalificadosMap(Norma $norma): array
    {
        $rows = DB::table('grupo_base_metal_calificado as gbc')
            ->join('grupos_base_metal as probado',    'probado.id',    '=', 'gbc.probado_id')
            ->join('grupos_base_metal as calificado', 'calificado.id', '=', 'gbc.calificado_id')
            ->where('probado.norma_id', $norma->id)
            ->get(['probado.codigo as probado', 'calificado.codigo as calificado']);

        $map = [];
        foreach ($rows as $row) {
            $map[$row->probado][] = $row->calificado;
        }
        return $map;
    }

    /** { "F-No. 4": ["F-No. 1", "F-No. 2", "F-No. 3", "F-No. 4"] } */
    private function consumibleCalificadosMap(Norma $norma): array
    {
        $rows = DB::table('grupo_consumible_calificado as gcc')
            ->join('grupos_consumible as probado',    'probado.id',    '=', 'gcc.probado_id')
            ->join('grupos_consumible as calificado', 'calificado.id', '=', 'gcc.calificado_id')
            ->where('probado.norma_id', $norma->id)
            ->get(['probado.codigo as probado', 'calificado.codigo as calificado']);

        $map = [];
        foreach ($rows as $row) {
            $map[$row->probado][] = $row->calificado;
        }
        return $map;
    }

    private function posicionesNorma(string $normaSlug): \Illuminate\Database\Eloquent\Collection
    {
        return Posicion::where(function ($q) use ($normaSlug) {
            $q->whereNull('norma')->orWhere('norma', $normaSlug);
        })->orderBy('codigo')->get();
    }

    private function consumiblesPorNorma(Norma $norma): \Illuminate\Database\Eloquent\Collection
    {
        return Consumible::whereHas('grupo', fn($q) => $q->where('norma_id', $norma->id))
            ->with('grupo:id,codigo')
            ->orderBy('clasificacion')
            ->get();
    }

    private function catalogosPorNorma(string $slug, Norma $norma): array
    {
        return match (true) {
            str_contains($slug, 'asme ix') || $slug === 'asme' => [
                'posiciones'        => $this->posicionesNorma('asme'),
                'grupos_base_metal' => GrupoBaseMetal::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'grupos_consumible' => GrupoConsumible::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'consumibles'       => $this->consumiblesPorNorma($norma),
                'campos_norma'      => $this->camposAsme(),
            ],
            str_contains($slug, 'd1.1') || str_contains($slug, 'd1-1') => [
                'posiciones'        => $this->posicionesNorma('aws'),
                'grupos_base_metal' => GrupoBaseMetal::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'grupos_consumible' => GrupoConsumible::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'consumibles'       => $this->consumiblesPorNorma($norma),
                'campos_norma'      => $this->camposAws(),
            ],
            str_contains($slug, 'd1.6') || str_contains($slug, 'd1-6') => [
                'posiciones'        => $this->posicionesNorma('aws'),
                'grupos_base_metal' => GrupoBaseMetal::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'grupos_consumible' => GrupoConsumible::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'consumibles'       => $this->consumiblesPorNorma($norma),
                'campos_norma'      => $this->camposAwsD16(),
            ],
            str_contains($slug, '1104') => [
                'posiciones'        => $this->posicionesNorma('api'),
                'grupos_base_metal' => GrupoBaseMetal::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'grupos_consumible' => GrupoConsumible::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'consumibles'       => $this->consumiblesPorNorma($norma),
                'campos_norma'      => $this->camposApi(),
            ],
            str_contains($slug, '650') => [
                'posiciones'        => $this->posicionesNorma('aws'),
                'grupos_base_metal' => GrupoBaseMetal::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'grupos_consumible' => GrupoConsumible::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'consumibles'       => $this->consumiblesPorNorma($norma),
                'campos_norma'      => $this->camposApi650(),
            ],
            str_contains($slug, 'iram') || str_contains($slug, '9606') => [
                'posiciones'        => $this->posicionesNorma('iso'),
                'grupos_base_metal' => GrupoBaseMetal::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'grupos_consumible' => GrupoConsumible::where('norma_id', $norma->id)->orderBy('orden')->get(),
                'consumibles'       => $this->consumiblesPorNorma($norma),
                'campos_norma'      => $this->camposIram(),
            ],
            default => [],
        };
    }

    // ── Schemas de campos por norma ────────────────────────────────────────────

    private function camposAsme(): array
    {
        return [
            ['campo' => 'proceso',          'tipo' => 'catalog', 'catalogo' => 'procesos',           'requerido' => true],
            ['campo' => 'tipo',             'tipo' => 'enum',    'opciones' => ['MANUAL', 'SEMI-AUTO', 'AUTOMATICO'], 'requerido' => false],
            ['campo' => 'transfer_type',    'tipo' => 'enum',    'opciones' => ['spray', 'pulse', 'short_circuit', 'globular', 'na'], 'si_proceso' => ['GMAW', 'GMAW-S'], 'requerido' => false],
            ['campo' => 'progresion',       'tipo' => 'enum',    'opciones' => ['ascendente', 'descendente'], 'requerido' => true],
            ['campo' => 'tipo_cupon',       'tipo' => 'enum',    'opciones' => ['caño', 'chapa'],     'requerido' => true],
            ['campo' => 'p_number',         'tipo' => 'catalog', 'catalogo' => 'grupos_base_metal',   'requerido' => true],
            ['campo' => 'metal_base',       'tipo' => 'text',                                          'requerido' => false],
            ['campo' => 'f_number',         'tipo' => 'catalog', 'catalogo' => 'grupos_consumible',   'requerido' => true],
            ['campo' => 'a_number',         'tipo' => 'enum',    'opciones' => ['A-No. 1','A-No. 2','A-No. 3','A-No. 4','A-No. 5','A-No. 6','A-No. 7','A-No. 8','A-No. 9','A-No. 10','A-No. 11'], 'requerido' => false],
            ['campo' => 'electrodo',        'tipo' => 'catalog', 'catalogo' => 'consumibles',         'requerido' => true],
            ['campo' => 'posicion',         'tipo' => 'catalog', 'catalogo' => 'posiciones',          'requerido' => true],
            ['campo' => 'respaldo',         'tipo' => 'enum',    'opciones' => ['CON RESPALDO', 'SIN RESPALDO'], 'requerido' => true],
            ['campo' => 'junta_calificada', 'tipo' => 'enum',    'opciones' => ['RANURA Y FILETE', 'FILETE'], 'requerido' => false],
            ['campo' => 'corriente',        'tipo' => 'enum',    'opciones' => ['CCEP', 'CCEN', 'CA'], 'requerido' => true],
            ['campo' => 'espesor_cupon',    'tipo' => 'decimal',                                       'requerido' => true],
            ['campo' => 'diametro_cupon',   'tipo' => 'decimal', 'si_cupon' => 'caño',                'requerido' => true],
            ['campo' => 'espesor_deposito', 'tipo' => 'decimal',                                       'requerido' => false],
            ['campo' => 'num_pasadas',      'tipo' => 'integer',                                       'requerido' => false],
            ['campo' => 'gas_proteccion',   'tipo' => 'text',    'si_proceso' => ['GTAW', 'GMAW', 'GMAW-S', 'FCAW', 'PAW'], 'requerido' => false],
            ['campo' => 'gas_respaldo',     'tipo' => 'text',    'si_proceso' => ['GTAW', 'GMAW', 'GMAW-S', 'FCAW', 'PAW'], 'requerido' => false],
            ['campo' => 'tungsteno',            'tipo' => 'text', 'si_proceso' => ['GTAW'],            'requerido' => false],
            ['campo' => 'insertos_consumibles', 'tipo' => 'enum', 'opciones' => ['CON INSERTO', 'SIN INSERTO'], 'si_proceso' => ['GTAW', 'PAW'], 'requerido' => false],
            ['campo' => 'resultado_vt',     'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
            ['campo' => 'resultado_bend',   'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
            ['campo' => 'resultado_rt',     'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado', 'NA'], 'requerido' => false],
        ];
    }

    private function camposAws(): array
    {
        return [
            ['campo' => 'proceso',              'tipo' => 'catalog', 'catalogo' => 'procesos',           'requerido' => true],
            ['campo' => 'tipo',                 'tipo' => 'enum',    'opciones' => ['MANUAL', 'SEMI-AUTO', 'AUTOMATICO'], 'requerido' => false],
            ['campo' => 'transfer_type',        'tipo' => 'enum',    'opciones' => ['spray', 'pulse', 'short_circuit', 'globular', 'na'], 'si_proceso' => ['GMAW', 'GMAW-S'], 'requerido' => false],
            ['campo' => 'tipo_junta',           'tipo' => 'enum',    'opciones' => ['RANURA', 'FILETE'],  'requerido' => true],
            ['campo' => 'progresion',           'tipo' => 'enum',    'opciones' => ['ascendente', 'descendente'], 'requerido' => false],
            ['campo' => 'tipo_cupon',           'tipo' => 'enum',    'opciones' => ['caño', 'chapa'],     'requerido' => true],
            ['campo' => 'espesor_cupon',        'tipo' => 'decimal',                                      'requerido' => true],
            ['campo' => 'espesor_deposito',     'tipo' => 'decimal',                                      'requerido' => false],
            ['campo' => 'grupo_base_metal',     'tipo' => 'catalog', 'catalogo' => 'grupos_base_metal',   'requerido' => true],
            ['campo' => 'metal_base',           'tipo' => 'text',                                          'requerido' => false],
            ['campo' => 'grupo_consumible',     'tipo' => 'catalog', 'catalogo' => 'grupos_consumible',   'requerido' => true],
            ['campo' => 'a_number',             'tipo' => 'enum',    'opciones' => ['A-No. 1','A-No. 2','A-No. 3','A-No. 4','A-No. 5','A-No. 6','A-No. 7','A-No. 8','A-No. 9','A-No. 10','A-No. 11'], 'requerido' => false],
            ['campo' => 'electrodo',            'tipo' => 'catalog', 'catalogo' => 'consumibles',         'requerido' => true],
            ['campo' => 'posicion',             'tipo' => 'catalog', 'catalogo' => 'posiciones',          'requerido' => true],
            ['campo' => 'respaldo',             'tipo' => 'enum',    'opciones' => ['CON RESPALDO', 'SIN RESPALDO'], 'requerido' => true],
            ['campo' => 'junta_calificada',     'tipo' => 'enum',    'opciones' => ['RANURA Y FILETE', 'FILETE'], 'requerido' => false],
            ['campo' => 'temperatura_preheat',  'tipo' => 'decimal',                                      'requerido' => false],
            ['campo' => 'temperatura_interpass','tipo' => 'decimal',                                      'requerido' => false],
            ['campo' => 'gas_proteccion',       'tipo' => 'text',    'si_proceso' => ['GTAW', 'GMAW', 'GMAW-S', 'FCAW', 'PAW'], 'requerido' => false],
            ['campo' => 'resultado_vt',         'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
            ['campo' => 'resultado_bend',       'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => false, 'si_junta' => 'RANURA'],
            ['campo' => 'resultado_rt',         'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado', 'NA'], 'requerido' => false, 'si_junta' => 'RANURA'],
            ['campo' => 'resultado_fractura_filete', 'tipo' => 'enum', 'opciones' => ['aprobado', 'rechazado'], 'requerido' => false, 'si_junta' => 'FILETE'],
            ['campo' => 'resultado_macro',      'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => false, 'si_junta' => 'FILETE'],
        ];
    }

    private function camposAwsD16(): array
    {
        return [
            ['campo' => 'proceso',              'tipo' => 'catalog', 'catalogo' => 'procesos',           'requerido' => true],
            ['campo' => 'tipo',                 'tipo' => 'enum',    'opciones' => ['MANUAL', 'SEMI-AUTO', 'AUTOMATICO'], 'requerido' => false],
            ['campo' => 'transfer_type',        'tipo' => 'enum',    'opciones' => ['spray', 'pulse', 'short_circuit', 'globular', 'na'], 'si_proceso' => ['GMAW', 'GMAW-S'], 'requerido' => false],
            ['campo' => 'tipo_junta',           'tipo' => 'enum',    'opciones' => ['RANURA', 'FILETE'],  'requerido' => true],
            ['campo' => 'progresion',           'tipo' => 'enum',    'opciones' => ['ascendente', 'descendente'], 'requerido' => false],
            ['campo' => 'tipo_cupon',           'tipo' => 'enum',    'opciones' => ['caño', 'chapa'],     'requerido' => true],
            ['campo' => 'espesor_cupon',        'tipo' => 'decimal',                                      'requerido' => true],
            ['campo' => 'espesor_deposito',     'tipo' => 'decimal',                                      'requerido' => false],
            ['campo' => 'grupo_base_metal',     'tipo' => 'catalog', 'catalogo' => 'grupos_base_metal',   'requerido' => true],
            ['campo' => 'metal_base',           'tipo' => 'text',                                          'requerido' => false],
            ['campo' => 'grupo_consumible',     'tipo' => 'catalog', 'catalogo' => 'grupos_consumible',   'requerido' => true],
            ['campo' => 'electrodo',            'tipo' => 'catalog', 'catalogo' => 'consumibles',         'requerido' => true],
            ['campo' => 'posicion',             'tipo' => 'catalog', 'catalogo' => 'posiciones',          'requerido' => true],
            ['campo' => 'respaldo',             'tipo' => 'enum',    'opciones' => ['CON RESPALDO', 'SIN RESPALDO'], 'requerido' => true],
            ['campo' => 'junta_calificada',     'tipo' => 'enum',    'opciones' => ['RANURA Y FILETE', 'FILETE'], 'requerido' => false],
            ['campo' => 'gas_proteccion',       'tipo' => 'text',                                          'requerido' => true],
            ['campo' => 'gas_respaldo',         'tipo' => 'text',    'si_proceso' => ['GTAW'],            'requerido' => false],
            ['campo' => 'temperatura_interpass','tipo' => 'decimal',                                      'requerido' => false],
            ['campo' => 'resultado_vt',         'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
            ['campo' => 'resultado_bend',       'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => false, 'si_junta' => 'RANURA'],
            ['campo' => 'resultado_fractura_filete', 'tipo' => 'enum', 'opciones' => ['aprobado', 'rechazado'], 'requerido' => false, 'si_junta' => 'FILETE'],
            ['campo' => 'resultado_macro',      'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => false, 'si_junta' => 'FILETE'],
        ];
    }

    private function camposNag(string $nombreNorma): array
    {
        $nagCommon = [
            ['campo' => 'nag_categoria',  'tipo' => 'enum', 'opciones' => ['A', 'B', 'C', 'D'], 'requerido' => true],
            ['campo' => 'nag_credencial', 'tipo' => 'text',                                       'requerido' => false],
        ];

        $esB = str_contains($nombreNorma, 'Cat. B') || str_contains($nombreNorma, 'Cat B');
        $esC = str_contains($nombreNorma, 'Cat. C') || str_contains($nombreNorma, 'Cat C');
        $esD = str_contains($nombreNorma, 'Cat. D') || str_contains($nombreNorma, 'Cat D');

        if ($esD) {
            return array_merge($nagCommon, [
                ['campo' => 'proceso',              'tipo' => 'catalog', 'catalogo' => 'procesos',             'requerido' => true],
                ['campo' => 'progresion',           'tipo' => 'enum',    'opciones' => ['descendente', 'ascendente'], 'requerido' => true],
                ['campo' => 'tipo_cupon',           'tipo' => 'enum',    'opciones' => ['caño'],                'requerido' => true],
                ['campo' => 'espesor_cupon',        'tipo' => 'decimal',                                       'requerido' => true],
                ['campo' => 'diametro_cupon',       'tipo' => 'decimal',                                       'requerido' => true],
                ['campo' => 'presion_diseno',       'tipo' => 'decimal',                                       'requerido' => false],
                ['campo' => 'tension_fluencia',     'tipo' => 'decimal',                                       'requerido' => false],
                ['campo' => 'posicion',             'tipo' => 'catalog', 'catalogo' => 'posiciones',           'requerido' => true],
                ['campo' => 'electrodo',            'tipo' => 'enum',    'opciones' => ['E-6010', 'E-6015'],   'requerido' => true],
                ['campo' => 'resultado_vt',         'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
                ['campo' => 'resultado_nick_break', 'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
                ['campo' => 'resultado_bend',       'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
            ]);
        }

        if ($esB) {
            // Cat. B: solo operadores de procesos automáticos (NAG 105 §4.2)
            $campos = $this->camposAsme();
            foreach ($campos as &$c) {
                if ($c['campo'] === 'proceso') {
                    $c['tipo']    = 'enum';
                    $c['opciones'] = ['SAW', 'ESW', 'GMAW-Auto'];
                    unset($c['catalogo']);
                }
            }
            return array_merge($nagCommon, $campos);
        }

        return array_merge($nagCommon, $esC ? $this->camposApi() : $this->camposAsme());
    }

    private function camposApi(): array
    {
        return [
            ['campo' => 'linea_tipo',           'tipo' => 'enum',    'opciones' => ['LINEA REGULAR', 'LINEA ESPECIAL'], 'requerido' => true],
            ['campo' => 'cliente',              'tipo' => 'text',                                       'requerido' => true],
            ['campo' => 'obra',                 'tipo' => 'text',                                       'requerido' => true],
            ['campo' => 'empresa_contratista',  'tipo' => 'text',                                       'requerido' => true],
            ['campo' => 'proceso',              'tipo' => 'catalog', 'catalogo' => 'procesos',           'requerido' => true],
            ['campo' => 'progresion',           'tipo' => 'enum',    'opciones' => ['ascendente', 'descendente'], 'requerido' => true],
            ['campo' => 'tipo_cupon',           'tipo' => 'enum',    'opciones' => ['caño'],             'requerido' => true],
            ['campo' => 'espesor_cupon',        'tipo' => 'decimal',                                    'requerido' => true],
            ['campo' => 'diametro_cupon',       'tipo' => 'decimal', 'si_cupon' => 'caño',              'requerido' => true],
            ['campo' => 'grupo_base_metal',     'tipo' => 'catalog', 'catalogo' => 'grupos_base_metal',  'requerido' => true],
            ['campo' => 'metal_base',           'tipo' => 'text',                                        'requerido' => false],
            ['campo' => 'respaldo',             'tipo' => 'enum',    'opciones' => ['CON RESPALDO', 'SIN RESPALDO'], 'requerido' => true],
            ['campo' => 'backing_material',     'tipo' => 'text',                                        'requerido' => false],
            ['campo' => 'grupo_consumible',     'tipo' => 'catalog', 'catalogo' => 'grupos_consumible',  'requerido' => true],
            ['campo' => 'posicion',             'tipo' => 'catalog', 'catalogo' => 'posiciones',         'requerido' => true],
            ['campo' => 'electrodo_raiz',       'tipo' => 'text',                                        'requerido' => true],
            ['campo' => 'electrodo_relleno',    'tipo' => 'text',                                        'requerido' => true],
            ['campo' => 'electrodo_terminacion','tipo' => 'text',                                        'requerido' => false],
            ['campo' => 'corriente_raiz',       'tipo' => 'enum',    'opciones' => ['CCEP', 'CCEN'],     'requerido' => true],
            ['campo' => 'corriente_relleno',    'tipo' => 'enum',    'opciones' => ['CCEP', 'CCEN'],     'requerido' => true],
            ['campo' => 'temperatura_preheat',  'tipo' => 'decimal',                                    'requerido' => false],
            ['campo' => 'num_pasadas',          'tipo' => 'integer',                                    'requerido' => false],
            ['campo' => 'tiempo_p1_p2',         'tipo' => 'integer',                                    'requerido' => false],
            ['campo' => 'tiempo_p2_rest',       'tipo' => 'integer',                                    'requerido' => false],
            ['campo' => 'velocidad_avance',     'tipo' => 'text',                                        'requerido' => false],
            ['campo' => 'limpieza_entre_pasadas','tipo' => 'text',                                      'requerido' => false],
            ['campo' => 'presentador',          'tipo' => 'enum',    'opciones' => ['EXTERNO', 'INTERNO'], 'requerido' => false],
            ['campo' => 'resultado_vt',         'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
            ['campo' => 'resultado_bend',       'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
            ['campo' => 'resultado_nick_break', 'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
        ];
    }

    private function camposApi650(): array
    {
        return [
            ['campo' => 'proceso',             'tipo' => 'catalog', 'catalogo' => 'procesos',          'requerido' => true],
            ['campo' => 'tipo_junta',           'tipo' => 'enum',    'opciones' => ['RANURA', 'FILETE'], 'requerido' => true],
            ['campo' => 'tipo_cupon',           'tipo' => 'enum',    'opciones' => ['chapa'],            'requerido' => true],
            ['campo' => 'espesor_cupon',        'tipo' => 'decimal',                                    'requerido' => true],
            ['campo' => 'grupo_base_metal',     'tipo' => 'catalog', 'catalogo' => 'grupos_base_metal',  'requerido' => true],
            ['campo' => 'grupo_consumible',     'tipo' => 'catalog', 'catalogo' => 'grupos_consumible',  'requerido' => true],
            ['campo' => 'electrodo',            'tipo' => 'catalog', 'catalogo' => 'consumibles',        'requerido' => true],
            ['campo' => 'posicion',             'tipo' => 'catalog', 'catalogo' => 'posiciones',         'requerido' => true],
            ['campo' => 'respaldo',             'tipo' => 'enum',    'opciones' => ['CON RESPALDO', 'SIN RESPALDO'], 'requerido' => true],
            ['campo' => 'temperatura_preheat',  'tipo' => 'decimal',                                   'requerido' => false],
            ['campo' => 'resultado_vt',         'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
            ['campo' => 'resultado_bend',        'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => false, 'si_junta' => 'RANURA'],
            ['campo' => 'resultado_fractura_filete', 'tipo' => 'enum', 'opciones' => ['aprobado', 'rechazado'], 'requerido' => false, 'si_junta' => 'FILETE'],
        ];
    }

    private function camposIram(): array
    {
        return [
            ['campo' => 'proceso',           'tipo' => 'catalog', 'catalogo' => 'procesos',         'requerido' => true],
            ['campo' => 'tipo_producto',      'tipo' => 'enum',    'opciones' => ['P', 'T'],          'requerido' => true],
            ['campo' => 'tipo_junta',         'tipo' => 'enum',    'opciones' => ['BW', 'FW'],        'requerido' => true],
            ['campo' => 'progresion',         'tipo' => 'enum',    'opciones' => ['ascendente', 'descendente'], 'requerido' => false],
            ['campo' => 'espesor_cupon',      'tipo' => 'decimal',                                   'requerido' => true],
            ['campo' => 'diametro_cupon',     'tipo' => 'decimal', 'si_producto' => 'T',             'requerido' => true],
            ['campo' => 'grupo_base_metal',   'tipo' => 'catalog', 'catalogo' => 'grupos_base_metal', 'requerido' => true],
            ['campo' => 'grupo_consumible',   'tipo' => 'catalog', 'catalogo' => 'grupos_consumible', 'requerido' => true],
            ['campo' => 'posicion',           'tipo' => 'catalog', 'catalogo' => 'posiciones',        'requerido' => true],
            ['campo' => 'tipo_respaldo',      'tipo' => 'enum',    'opciones' => ['ss nb', 'ss mb', 'bs'], 'requerido' => true],
            ['campo' => 'resultado_vt',       'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
            ['campo' => 'resultado_bend',     'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => false],
            ['campo' => 'resultado_rt',       'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado', 'NA'], 'requerido' => false],
        ];
    }
}
