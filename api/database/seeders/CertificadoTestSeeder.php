<?php

namespace Database\Seeders;

use App\Models\Certificado;
use App\Models\Soldador;
use App\Services\CertificateTestSyncService;
use App\Services\RangeCalculatorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 10 certificados de prueba completos y contrastables con las normas.
 *
 * Cobertura:
 *   #1  ASME IX   SMAW  6G  caño  t=14mm 4-pasadas → espesor ilimitado
 *   #2  ASME IX   GTAW  5G  caño  t=6.35mm F-No.6 con respaldo
 *   #3  AWS D1.1  SMAW  3G  chapa t=25.4mm RANURA → ilimitado
 *   #4  AWS D1.1  SMAW  2G-P chapa t=12.7mm FILETE
 *   #5  API 1104  SMAW  5G  caño  t=9.5mm OD=168.3mm WF-1 sin respaldo
 *   #6  API 1104  SMAW  6G  caño  t=19.1mm OD=323.9mm WF-2 con respaldo → ilimitado
 *   #7  ASME B31.3 GTAW 5G  caño  t=8.0mm OD=88.9mm (delega a ASME IX)
 *   #8  NAG 105 Cat. A SMAW 6G caño t=14mm (delega a ASME IX)
 *   #9  NAG 105 Cat. C SMAW 5G caño t=9.5mm (delega a API 1104)
 *   #10 NAG 105 Cat. D SMAW 5G caño t=9.5mm EPS N°1 con cálculo SMYS i=17.74% < 20%
 *
 * Uso:
 *   php artisan db:seed --class=CertificadoTestSeeder
 */
class CertificadoTestSeeder extends Seeder
{
    public function run(): void
    {
        $rangeCalc = app(RangeCalculatorService::class);
        $testSync  = app(CertificateTestSyncService::class);

        // ── IDs de referencia ─────────────────────────────────────────────────

        $userId      = DB::table('users')->where('email', 'admin@kosoldar.com')->value('id');
        $inspectorId = DB::table('inspectors')->orderBy('id')->value('id');

        $norma = fn(string $nombre) => DB::table('normas')->where('nombre', $nombre)->value('id');
        $emp   = fn(string $nombre) => DB::table('empresas')->where('nombre', $nombre)->value('id');
        $sold  = fn(string $dni)    => DB::table('soldadores')->where('dni', $dni)->value('id');

        $asmeIx  = $norma('ASME IX');
        $awsD11  = $norma('AWS D1.1');
        $api1104 = $norma('API 1104');
        $b31     = $norma('ASME B31.3');
        $nagA    = $norma('NAG 105 Cat. A');
        $nagC    = $norma('NAG 105 Cat. C');
        $nagD    = $norma('NAG 105 Cat. D');

        // ── Actualizar soldadores con datos NAG requeridos ────────────────────
        // checkNagSoldadorData() exige fecha_nacimiento + nacionalidad para NAG 105

        Soldador::where('dni', '30256714')->update([   // Torres, Federico — NAG Cat. A
            'fecha_nacimiento' => '1981-07-14',
            'nacionalidad'     => 'Argentina',
        ]);
        Soldador::where('dni', '32698745')->update([   // Herrera, Daniel — NAG Cat. C
            'fecha_nacimiento' => '1979-03-22',
            'nacionalidad'     => 'Argentina',
        ]);
        Soldador::where('dni', '26543219')->update([   // Díaz, Gustavo — NAG Cat. D
            'fecha_nacimiento' => '1988-11-05',
            'nacionalidad'     => 'Bolivia',
        ]);

        // ── Definición de los 10 certificados ────────────────────────────────

        $certs = [

            // ─── #1 ─── ASME IX · SMAW · 6G · caño ─────────────────────────
            // QW-452.1(b): t=14mm ≥ 13mm + 4 pasadas ≥ 3 → espesor ILIMITADO
            // QW-452.3: OD=114.3mm > 73mm → desde 73mm ILIMITADO
            // QW-461.9: 6G → califica todas posiciones ranura y filete
            [
                'numero'             => 1,
                'anio'               => 2026,
                'soldador_id'        => $sold('28541032'),   // González, Carlos
                'empresa_id'         => $emp('Petroquímica San Lorenzo S.A.'),
                'norma_id'           => $asmeIx,
                'inspector_id'       => $inspectorId,
                'tipo'               => 'inicial',
                'estado'             => 'vigente',
                'resultado'          => 'aprobado',
                'fecha_calificacion' => '2026-01-15',
                'eps_numero'         => 'EPS-2026-001',
                'pqr_numero'         => 'PQR-ASME-001-2026',
                'proceso'            => 'SMAW',
                'posicion'           => '6G',
                'progresion'         => 'ascendente',
                'tipo_cupon'         => 'caño',
                'usuario_id'         => $userId,
                'qr_token'           => Str::uuid()->toString(),
                'revision'           => 0,
                'variables'          => [
                    'proceso'            => 'SMAW',
                    'tipo'               => 'MANUAL',
                    'progresion'         => 'ascendente',
                    'tipo_cupon'         => 'caño',
                    'p_number'           => 'P-No. 1 Gr. 1',
                    'metal_base'         => 'ASTM A106 Gr. B',
                    'f_number'           => 'F-No. 4',
                    'a_number'           => 'A-No. 1',
                    'electrodo'          => 'E7018',
                    'posicion'           => '6G',
                    'respaldo'           => 'SIN RESPALDO',
                    'corriente'          => 'CCEP',
                    'espesor_cupon'      => 14.0,
                    'diametro_cupon'     => 114.3,
                    'num_pasadas'        => 4,
                    'resultado_vt'       => 'aprobado',
                    'resultado_bend'     => 'aprobado',
                    'resultado_rt'       => 'aprobado',
                ],
            ],

            // ─── #2 ─── ASME IX · GTAW · 5G · caño ─────────────────────────
            // QW-452.1(b): t=6.35mm → califica hasta 2t = 12.7mm
            // QW-452.3: OD=60.3mm ∈ [25.4, 73) → desde 25.4mm ILIMITADO
            // QW-461.9: 5G → califica 1G, 1G-P, 3G, 4G, 5G + todos filetes
            [
                'numero'             => 2,
                'anio'               => 2026,
                'soldador_id'        => $sold('31287654'),   // Ramírez, Luis
                'empresa_id'         => $emp('Techint Construcciones S.A.'),
                'norma_id'           => $asmeIx,
                'inspector_id'       => $inspectorId,
                'tipo'               => 'inicial',
                'estado'             => 'vigente',
                'resultado'          => 'aprobado',
                'fecha_calificacion' => '2026-01-22',
                'eps_numero'         => 'EPS-2026-002',
                'proceso'            => 'GTAW',
                'posicion'           => '5G',
                'progresion'         => 'ascendente',
                'tipo_cupon'         => 'caño',
                'usuario_id'         => $userId,
                'qr_token'           => Str::uuid()->toString(),
                'revision'           => 0,
                'variables'          => [
                    'proceso'               => 'GTAW',
                    'tipo'                  => 'MANUAL',
                    'progresion'            => 'ascendente',
                    'tipo_cupon'            => 'caño',
                    'p_number'              => 'P-No. 1 Gr. 1',
                    'metal_base'            => 'ASTM A53 Gr. B',
                    'f_number'              => 'F-No. 6',
                    'a_number'              => 'A-No. 1',
                    'electrodo'             => 'ER70S-2',
                    'posicion'              => '5G',
                    'respaldo'              => 'CON RESPALDO',
                    'corriente'             => 'CCEP',
                    'espesor_cupon'         => 6.35,
                    'diametro_cupon'        => 60.3,
                    'gas_proteccion'        => 'Argón 99.99%',
                    'gas_respaldo'          => 'Argón 99.99%',
                    'tungsteno'             => 'EWTh-2 Ø2.4mm',
                    'insertos_consumibles'  => 'SIN INSERTO',
                    'resultado_vt'          => 'aprobado',
                    'resultado_bend'        => 'aprobado',
                ],
            ],

            // ─── #3 ─── AWS D1.1 · SMAW · 3G · chapa · RANURA ──────────────
            // Tabla 6.11: t=25.4mm ≥ 25.4mm → espesor ILIMITADO (desde 3.2mm)
            // Tabla 6.10: 3G ranura → califica 1G-P, 2G-P, 3G + filetes 1F, 2F, 3F
            [
                'numero'             => 3,
                'anio'               => 2026,
                'soldador_id'        => $sold('25963148'),   // Fernández, Martín
                'empresa_id'         => $emp('YPF S.A.'),
                'norma_id'           => $awsD11,
                'inspector_id'       => $inspectorId,
                'tipo'               => 'inicial',
                'estado'             => 'vigente',
                'resultado'          => 'aprobado',
                'fecha_calificacion' => '2026-02-05',
                'eps_numero'         => 'EPS-2026-003',
                'proceso'            => 'SMAW',
                'posicion'           => '3G',
                'progresion'         => 'ascendente',
                'tipo_cupon'         => 'chapa',
                'usuario_id'         => $userId,
                'qr_token'           => Str::uuid()->toString(),
                'revision'           => 0,
                'variables'          => [
                    'proceso'           => 'SMAW',
                    'tipo'              => 'MANUAL',
                    'tipo_junta'        => 'RANURA',
                    'progresion'        => 'ascendente',
                    'tipo_cupon'        => 'chapa',
                    'espesor_cupon'     => 25.4,
                    'espesor_deposito'  => 20.0,
                    'grupo_base_metal'  => 'Grupo I',
                    'metal_base'        => 'ASTM A36',
                    'grupo_consumible'  => 'F4',
                    'a_number'          => 'A-No. 1',
                    'electrodo'         => 'E7018',
                    'posicion'          => '3G',
                    'respaldo'          => 'CON RESPALDO',
                    'resultado_vt'      => 'aprobado',
                    'resultado_bend'    => 'aprobado',
                    'resultado_rt'      => 'NA',
                ],
            ],

            // ─── #4 ─── AWS D1.1 · SMAW · 2G-P · chapa · FILETE ────────────
            // Tabla 6.11: t=12.7mm ∈ [9.5, 25.4) → hasta 2t = 25.4mm
            // Tabla 6.10: 2G-P filete → califica 1F, 2F
            [
                'numero'             => 4,
                'anio'               => 2026,
                'soldador_id'        => $sold('33741025'),   // López, Sebastián
                'empresa_id'         => $emp('Tenaris Global Services S.A.'),
                'norma_id'           => $awsD11,
                'inspector_id'       => $inspectorId,
                'tipo'               => 'inicial',
                'estado'             => 'vigente',
                'resultado'          => 'aprobado',
                'fecha_calificacion' => '2026-02-12',
                'eps_numero'         => 'EPS-2026-004',
                'proceso'            => 'SMAW',
                'posicion'           => '2G-P',
                'progresion'         => 'ascendente',
                'tipo_cupon'         => 'chapa',
                'usuario_id'         => $userId,
                'qr_token'           => Str::uuid()->toString(),
                'revision'           => 0,
                'variables'          => [
                    'proceso'                => 'SMAW',
                    'tipo'                   => 'MANUAL',
                    'tipo_junta'             => 'FILETE',
                    'progresion'             => 'ascendente',
                    'tipo_cupon'             => 'chapa',
                    'espesor_cupon'          => 12.7,
                    'grupo_base_metal'       => 'Grupo I',
                    'metal_base'             => 'ASTM A36',
                    'grupo_consumible'       => 'F3',
                    'electrodo'              => 'E6010',
                    'posicion'               => '2G-P',
                    'respaldo'               => 'SIN RESPALDO',
                    'resultado_vt'           => 'aprobado',
                    'resultado_fractura_filete' => 'aprobado',
                    'resultado_macro'        => 'aprobado',
                ],
            ],

            // ─── #5 ─── API 1104 · SMAW · 5G · caño · sin respaldo ──────────
            // Tabla 5: t=9.5mm ∈ [3.9,19) → max(19.0, 1.5×9.5) = 19.0mm
            // §6.2.2(d): OD=168.3mm ∈ [60.3,323.9) → Grupo 2, max 323.9mm
            // §6.2.2(f): 5G → califica 1G, 1G-P, 3G, 4G, 5G
            [
                'numero'             => 5,
                'anio'               => 2026,
                'soldador_id'        => $sold('29874561'),   // Martínez, Diego
                'empresa_id'         => $emp('IMPSA Industrias Metalúrgicas'),
                'norma_id'           => $api1104,
                'inspector_id'       => $inspectorId,
                'tipo'               => 'inicial',
                'estado'             => 'vigente',
                'resultado'          => 'aprobado',
                'fecha_calificacion' => '2026-02-20',
                'eps_numero'         => 'EPS-2026-005',
                'pqr_numero'         => 'PQR-API-004-2026',
                'proceso'            => 'SMAW',
                'posicion'           => '5G',
                'progresion'         => 'descendente',
                'tipo_cupon'         => 'caño',
                'usuario_id'         => $userId,
                'qr_token'           => Str::uuid()->toString(),
                'revision'           => 0,
                'variables'          => [
                    'linea_tipo'          => 'LINEA REGULAR',
                    'cliente'             => 'IMPSA Industrias Metalúrgicas',
                    'obra'                => 'Planta Compresora Norte — Expansión',
                    'empresa_contratista' => 'Constructora Del Valle S.R.L.',
                    'proceso'             => 'SMAW',
                    'progresion'          => 'descendente',
                    'tipo_cupon'          => 'caño',
                    'espesor_cupon'       => 9.5,
                    'diametro_cupon'      => 168.3,
                    'grupo_base_metal'    => 'API 5L X42–X52',
                    'metal_base'          => 'API 5L X52',
                    'respaldo'            => 'SIN RESPALDO',
                    'grupo_consumible'    => 'WF-1',
                    'posicion'            => '5G',
                    'electrodo_raiz'      => 'E6010',
                    'electrodo_relleno'   => 'E7010-P1',
                    'corriente_raiz'      => 'CCEP',
                    'corriente_relleno'   => 'CCEP',
                    'temperatura_preheat' => 20,
                    'resultado_vt'        => 'aprobado',
                    'resultado_bend'      => 'aprobado',
                    'resultado_nick_break'=> 'aprobado',
                ],
            ],

            // ─── #6 ─── API 1104 · SMAW · 6G · caño · con respaldo ──────────
            // Tabla 5: t=19.1mm ≥ 19mm → espesor ILIMITADO
            // §6.2.2(d): OD=323.9mm ≥ 323.9mm → Grupo 3, ILIMITADO
            // Ed. 22ª var. j: backing_material registrado para trazabilidad
            [
                'numero'             => 6,
                'anio'               => 2026,
                'soldador_id'        => $sold('27652341'),   // Sánchez, Pablo
                'empresa_id'         => $emp('Constructora Del Valle S.R.L.'),
                'norma_id'           => $api1104,
                'inspector_id'       => $inspectorId,
                'tipo'               => 'inicial',
                'estado'             => 'vigente',
                'resultado'          => 'aprobado',
                'fecha_calificacion' => '2026-03-01',
                'eps_numero'         => 'EPS-2026-006',
                'pqr_numero'         => 'PQR-API-005-2026',
                'proceso'            => 'SMAW',
                'posicion'           => '6G',
                'progresion'         => 'descendente',
                'tipo_cupon'         => 'caño',
                'usuario_id'         => $userId,
                'qr_token'           => Str::uuid()->toString(),
                'revision'           => 0,
                'variables'          => [
                    'linea_tipo'          => 'LINEA ESPECIAL',
                    'cliente'             => 'Gasoducto Centro-Oeste S.A.',
                    'obra'                => 'Gasoducto Centro-Oeste — Tramo III',
                    'empresa_contratista' => 'Techint Construcciones S.A.',
                    'proceso'             => 'SMAW',
                    'progresion'          => 'descendente',
                    'tipo_cupon'          => 'caño',
                    'espesor_cupon'       => 19.1,
                    'diametro_cupon'      => 323.9,
                    'grupo_base_metal'    => 'API 5L X56–X65',
                    'metal_base'          => 'API 5L X65',
                    'respaldo'            => 'CON RESPALDO',
                    'backing_material'    => 'Cinta de acero ASTM A109 (CJP)',
                    'grupo_consumible'    => 'WF-2',
                    'posicion'            => '6G',
                    'electrodo_raiz'      => 'E6010',
                    'electrodo_relleno'   => 'E7018',
                    'corriente_raiz'      => 'CCEP',
                    'corriente_relleno'   => 'CCEP',
                    'temperatura_preheat' => 50,
                    'resultado_vt'        => 'aprobado',
                    'resultado_bend'      => 'aprobado',
                    'resultado_nick_break'=> 'aprobado',
                ],
            ],

            // ─── #7 ─── ASME B31.3 · GTAW · 5G · caño ──────────────────────
            // B31.3 delega calificación a ASME IX (parent_norma_id → ASME IX)
            // RangeCalculatorService usa effectiveNorma() → reglas ASME IX
            // QW-452.1(b): t=8.0mm → hasta 2t = 16.0mm
            // QW-452.3: OD=88.9mm > 73mm → desde 73mm ILIMITADO
            [
                'numero'             => 7,
                'anio'               => 2026,
                'soldador_id'        => $sold('35214987'),   // Rodríguez, Alejandro
                'empresa_id'         => $emp('YPF S.A.'),
                'norma_id'           => $b31,
                'inspector_id'       => $inspectorId,
                'tipo'               => 'inicial',
                'estado'             => 'vigente',
                'resultado'          => 'aprobado',
                'fecha_calificacion' => '2026-03-10',
                'eps_numero'         => 'EPS-2026-007',
                'pqr_numero'         => 'PQR-B313-001-2026',
                'proceso'            => 'GTAW',
                'posicion'           => '5G',
                'progresion'         => 'ascendente',
                'tipo_cupon'         => 'caño',
                'usuario_id'         => $userId,
                'qr_token'           => Str::uuid()->toString(),
                'revision'           => 0,
                'variables'          => [
                    'proceso'              => 'GTAW',
                    'tipo'                 => 'MANUAL',
                    'progresion'           => 'ascendente',
                    'tipo_cupon'           => 'caño',
                    'p_number'             => 'P-No. 1 Gr. 1',
                    'metal_base'           => 'ASTM A106 Gr. B',
                    'f_number'             => 'F-No. 6',
                    'a_number'             => 'A-No. 1',
                    'electrodo'            => 'ER70S-2',
                    'posicion'             => '5G',
                    'respaldo'             => 'CON RESPALDO',
                    'corriente'            => 'CCEP',
                    'espesor_cupon'        => 8.0,
                    'diametro_cupon'       => 88.9,
                    'gas_proteccion'       => 'Argón 99.99%',
                    'gas_respaldo'         => 'Argón 99.99%',
                    'tungsteno'            => 'EWTh-2 Ø2.4mm',
                    'insertos_consumibles' => 'SIN INSERTO',
                    'resultado_vt'         => 'aprobado',
                    'resultado_bend'       => 'aprobado',
                ],
            ],

            // ─── #8 ─── NAG 105 Cat. A · SMAW · 6G · caño ──────────────────
            // Cat. A delega a ASME IX → mismas reglas de rango que #1
            // Form 513-780-0: fecha_nacimiento + nacionalidad son obligatorios
            // Vigencia NAG: +2 años desde fecha_calificacion
            [
                'numero'             => 8,
                'anio'               => 2026,
                'soldador_id'        => $sold('30256714'),   // Torres, Federico
                'empresa_id'         => $emp('Gasoducto Centro-Oeste S.A.'),
                'norma_id'           => $nagA,
                'inspector_id'       => $inspectorId,
                'tipo'               => 'inicial',
                'estado'             => 'vigente',
                'resultado'          => 'aprobado',
                'fecha_calificacion' => '2026-04-05',
                'fecha_vencimiento'  => '2028-04-05',   // NAG: +2 años
                'eps_numero'         => 'EPS-NAG-2026-008',
                'pqr_numero'         => 'PQR-NAG-A-001-2026',
                'proceso'            => 'SMAW',
                'posicion'           => '6G',
                'progresion'         => 'ascendente',
                'tipo_cupon'         => 'caño',
                'usuario_id'         => $userId,
                'qr_token'           => Str::uuid()->toString(),
                'revision'           => 0,
                'variables'          => [
                    'nag_categoria'     => 'A',
                    'nag_credencial'    => 'NAG-A-2026-008',
                    'proceso'           => 'SMAW',
                    'tipo'              => 'MANUAL',
                    'progresion'        => 'ascendente',
                    'tipo_cupon'        => 'caño',
                    'p_number'          => 'P-No. 1 Gr. 1',
                    'metal_base'        => 'API 5L Gr. B',
                    'f_number'          => 'F-No. 4',
                    'a_number'          => 'A-No. 1',
                    'electrodo'         => 'E7018',
                    'posicion'          => '6G',
                    'respaldo'          => 'SIN RESPALDO',
                    'corriente'         => 'CCEP',
                    'espesor_cupon'     => 14.0,
                    'diametro_cupon'    => 114.3,
                    'num_pasadas'       => 4,
                    'resultado_vt'      => 'aprobado',
                    'resultado_bend'    => 'aprobado',
                    'resultado_rt'      => 'aprobado',
                ],
            ],

            // ─── #9 ─── NAG 105 Cat. C · SMAW · 5G · caño ──────────────────
            // Cat. C delega a API 1104 → mismas reglas que #5
            // Credencial NAG: alcance diámetro bucket §6.4 (50.8/304.8mm)
            // OD=168.3mm → bucket "2"–12" (50.8–304.8mm)"
            [
                'numero'             => 9,
                'anio'               => 2026,
                'soldador_id'        => $sold('32698745'),   // Herrera, Daniel
                'empresa_id'         => $emp('Gasoducto Centro-Oeste S.A.'),
                'norma_id'           => $nagC,
                'inspector_id'       => $inspectorId,
                'tipo'               => 'inicial',
                'estado'             => 'vigente',
                'resultado'          => 'aprobado',
                'fecha_calificacion' => '2026-04-15',
                'fecha_vencimiento'  => '2028-04-15',   // NAG: +2 años
                'eps_numero'         => 'EPS-NAG-2026-009',
                'proceso'            => 'SMAW',
                'posicion'           => '5G',
                'progresion'         => 'descendente',
                'tipo_cupon'         => 'caño',
                'usuario_id'         => $userId,
                'qr_token'           => Str::uuid()->toString(),
                'revision'           => 0,
                'variables'          => [
                    'nag_categoria'       => 'C',
                    'nag_credencial'      => 'NAG-C-2026-009',
                    'linea_tipo'          => 'LINEA REGULAR',
                    'cliente'             => 'Transportadora Gas del Norte S.A.',
                    'obra'                => 'Gasoducto Norte — Km 245–312',
                    'empresa_contratista' => 'Constructora Del Valle S.R.L.',
                    'proceso'             => 'SMAW',
                    'progresion'          => 'descendente',
                    'tipo_cupon'          => 'caño',
                    'espesor_cupon'       => 9.5,
                    'diametro_cupon'      => 168.3,
                    'grupo_base_metal'    => 'API 5L X42–X52',
                    'metal_base'          => 'API 5L X52',
                    'respaldo'            => 'SIN RESPALDO',
                    'grupo_consumible'    => 'WF-1',
                    'posicion'            => '5G',
                    'electrodo_raiz'      => 'E6010',
                    'electrodo_relleno'   => 'E7010-P1',
                    'corriente_raiz'      => 'CCEP',
                    'corriente_relleno'   => 'CCEP',
                    'temperatura_preheat' => 10,
                    'resultado_vt'        => 'aprobado',
                    'resultado_bend'      => 'aprobado',
                    'resultado_nick_break'=> 'aprobado',
                ],
            ],

            // ─── #10 ─── NAG 105 Cat. D · SMAW · 5G · caño · EPS N°1 ───────
            // Standalone (sin parent). Reglas propias de ThicknessRule/DiameterRule.
            // Espesor: t=9.5mm → 2t = 19.0mm (cap EPS N°1)
            // Diámetro: OD=168.3mm → bucket 2"–12" (50.8–304.8mm)
            // SMYS: i = P×D×100 / (2×t×S) = 50×168.3×100 / (2×9.5×2497) = 17.74% < 20% ✓
            [
                'numero'             => 10,
                'anio'               => 2026,
                'soldador_id'        => $sold('26543219'),   // Díaz, Gustavo
                'empresa_id'         => $emp('Gasoducto Centro-Oeste S.A.'),
                'norma_id'           => $nagD,
                'inspector_id'       => $inspectorId,
                'tipo'               => 'inicial',
                'estado'             => 'vigente',
                'resultado'          => 'aprobado',
                'fecha_calificacion' => '2026-05-01',
                'fecha_vencimiento'  => '2028-05-01',   // NAG: +2 años
                'eps_numero'         => 'EPS-NAG-2026-010',
                'proceso'            => 'SMAW',
                'posicion'           => '5G',
                'progresion'         => 'descendente',
                'tipo_cupon'         => 'caño',
                'usuario_id'         => $userId,
                'qr_token'           => Str::uuid()->toString(),
                'revision'           => 0,
                'variables'          => [
                    'nag_categoria'     => 'D',
                    'nag_credencial'    => 'NAG-D-2026-010',
                    'proceso'           => 'SMAW',
                    'progresion'        => 'descendente',
                    'tipo_cupon'        => 'caño',
                    'espesor_cupon'     => 9.5,
                    'diametro_cupon'    => 168.3,
                    'presion_diseno'    => 50.0,
                    'tension_fluencia'  => 2497.0,
                    'posicion'          => '5G',
                    'electrodo'         => 'E-6010',
                    'resultado_vt'      => 'aprobado',
                    'resultado_nick_break' => 'aprobado',
                    'resultado_bend'    => 'aprobado',
                ],
            ],
        ];

        // ── Crear certificados + calcular rangos ──────────────────────────────

        foreach ($certs as $data) {
            // Omitir si ya existe (idempotente)
            if (Certificado::where('numero', $data['numero'])->where('anio', $data['anio'])->exists()) {
                continue;
            }

            $cert = Certificado::create($data);

            $rangeCalc->calculate($cert);
            $testSync->sync($cert->load('norma'));
        }
    }
}
