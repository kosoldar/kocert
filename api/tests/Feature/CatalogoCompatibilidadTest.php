<?php

namespace Tests\Feature;

use App\Models\Norma;
use App\Models\Proceso;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\ConsumibleSeeder;
use Database\Seeders\GrupoBaseMetalCalificadoSeeder;
use Database\Seeders\GrupoBaseMetalConsumibleSeeder;
use Database\Seeders\GrupoBaseMetalSeeder;
use Database\Seeders\GrupoConsumibleCalificadoSeeder;
use Database\Seeders\GrupoConsumiblePosicionSeeder;
use Database\Seeders\GrupoConsumibleSeeder;
use Database\Seeders\NormaProcesoSeeder;
use Database\Seeders\PosicionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoCompatibilidadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Procesos base
        foreach (['SMAW', 'GMAW', 'GTAW', 'FCAW', 'SAW', 'PAW', 'OFW'] as $p) {
            Proceso::firstOrCreate(['nombre' => $p]);
        }

        // Normas
        foreach ([
            'ASME IX', 'AWS D1.1', 'AWS D1.6', 'API 1104',
            'API 650', 'ASME B31.3', 'ASME B31.8', 'IRAM',
        ] as $n) {
            Norma::firstOrCreate(['nombre' => $n]);
        }

        // Delegación B31.3/B31.8 → ASME IX
        $asmeIxId = Norma::where('nombre', 'ASME IX')->value('id');
        Norma::where('nombre', 'ASME B31.3')->update(['parent_norma_id' => $asmeIxId]);
        Norma::where('nombre', 'ASME B31.8')->update(['parent_norma_id' => $asmeIxId]);

        $this->seed([
            PosicionSeeder::class,
            GrupoBaseMetalSeeder::class,
            GrupoConsumibleSeeder::class,
            ConsumibleSeeder::class,
            NormaProcesoSeeder::class,
            GrupoBaseMetalConsumibleSeeder::class,
            GrupoConsumiblePosicionSeeder::class,
            GrupoBaseMetalCalificadoSeeder::class,
            GrupoConsumibleCalificadoSeeder::class,
        ]);
    }

    // ── Helper ──────────────────────────────────────────────────────────────

    private function catalogo(string $normaName): array
    {
        $user = User::firstOrCreate(
            ['email' => 'test@kocert.test'],
            ['nombre' => 'Test', 'apellido' => 'User', 'password' => Hash::make('secret'), 'rol' => 'admin'],
        );

        $id = Norma::where('nombre', $normaName)->value('id');
        return $this->actingAs($user)->getJson("/api/catalogos/norma/{$id}")->assertOk()->json();
    }

    // ── ASME IX — metal / consumible ─────────────────────────────────────────

    /** P-No. 1 (carbono) no puede usar F-No. 5 (inox SMAW) */
    public function test_asme_carbono_no_acepta_consumible_inox(): void
    {
        $mc = $this->catalogo('ASME IX')['metal_consumible'];

        $this->assertContains('F-No. 4', $mc['P-No. 1 Gr. 1'], 'Carbono debe aceptar F-No. 4');
        $this->assertContains('F-No. 6', $mc['P-No. 1 Gr. 1'], 'Carbono debe aceptar F-No. 6');
        $this->assertNotContains('F-No. 5', $mc['P-No. 1 Gr. 1'], 'Carbono NO debe aceptar F-No. 5 (inox SMAW)');
    }

    /** P-No. 8 (inox austenítico) no puede usar E7018 ni E6010 */
    public function test_asme_inox_austenitico_no_acepta_consumibles_carbono(): void
    {
        $mc = $this->catalogo('ASME IX')['metal_consumible'];

        $this->assertContains('F-No. 5', $mc['P-No. 8 Gr. 1'], 'Inox debe aceptar F-No. 5');
        $this->assertContains('F-No. 6', $mc['P-No. 8 Gr. 1'], 'Inox debe aceptar F-No. 6');
        $this->assertNotContains('F-No. 1', $mc['P-No. 8 Gr. 1'], 'Inox NO debe aceptar F-No. 1 (plano/H)');
        $this->assertNotContains('F-No. 3', $mc['P-No. 8 Gr. 1'], 'Inox NO debe aceptar F-No. 3 (E6010)');
        $this->assertNotContains('F-No. 4', $mc['P-No. 8 Gr. 1'], 'Inox NO debe aceptar F-No. 4 (E7018)');
    }

    /** Los tres grupos austeníticos reciben el mismo filtro */
    public function test_asme_todos_grupos_p8_excluyen_consumibles_carbono(): void
    {
        $mc = $this->catalogo('ASME IX')['metal_consumible'];

        foreach (['P-No. 8 Gr. 1', 'P-No. 8 Gr. 2', 'P-No. 8 Gr. 3'] as $grupo) {
            $this->assertNotContains('F-No. 4', $mc[$grupo], "$grupo no debe aceptar F-No. 4");
            $this->assertContains('F-No. 5', $mc[$grupo], "$grupo debe aceptar F-No. 5");
        }
    }

    /** P-No. 10H (dúplex 2205) excluye consumibles de carbono */
    public function test_asme_duplex_no_acepta_consumibles_carbono(): void
    {
        $mc = $this->catalogo('ASME IX')['metal_consumible'];

        $this->assertContains('F-No. 5', $mc['P-No. 10H']);
        $this->assertContains('F-No. 6', $mc['P-No. 10H']);
        $this->assertNotContains('F-No. 3', $mc['P-No. 10H']);
        $this->assertNotContains('F-No. 4', $mc['P-No. 10H']);
    }

    /** P-No. 5B (P91) acepta solo F-No. 4 y F-No. 6 (no celulósico) */
    public function test_asme_p91_no_acepta_celulosico(): void
    {
        $mc = $this->catalogo('ASME IX')['metal_consumible'];

        $this->assertContains('F-No. 4', $mc['P-No. 5B Gr. 2']);
        $this->assertNotContains('F-No. 3', $mc['P-No. 5B Gr. 2'], 'P91 NO debe aceptar celulósico E6010');
        $this->assertNotContains('F-No. 5', $mc['P-No. 5B Gr. 2'], 'P91 NO debe aceptar inox SMAW');
    }

    // ── ASME IX — consumible / posición ──────────────────────────────────────

    /** F-No. 1 (EXX24/EXX28) solo posiciones plana y horizontal */
    public function test_asme_f_numero_1_restringido_plana_horizontal(): void
    {
        $cp = $this->catalogo('ASME IX')['consumible_posiciones'];

        $this->assertArrayHasKey('F-No. 1', $cp, 'F-No. 1 debe tener restricción de posición');

        $permitidas = $cp['F-No. 1'];
        $this->assertContains('1G',   $permitidas, '1G (plana) debe estar permitida');
        $this->assertContains('2G',   $permitidas, '2G (horizontal) debe estar permitida');
        $this->assertContains('1F',   $permitidas, '1F (plana filete) debe estar permitida');
        $this->assertContains('2F',   $permitidas, '2F (H filete) debe estar permitida');
        $this->assertContains('1G-P', $permitidas, '1G-P debe estar permitida');
        $this->assertContains('2G-P', $permitidas, '2G-P debe estar permitida');

        $this->assertNotContains('3G', $permitidas, '3G (vertical) NO debe estar permitida para F-No. 1');
        $this->assertNotContains('4G', $permitidas, '4G (sobrecabeza) NO debe estar permitida para F-No. 1');
        $this->assertNotContains('5G', $permitidas, '5G NO debe estar permitida para F-No. 1');
        $this->assertNotContains('6G', $permitidas, '6G NO debe estar permitida para F-No. 1');
    }

    /** F-No. 4 (E7018) no tiene restricción de posición */
    public function test_asme_f_numero_4_sin_restriccion_posicion(): void
    {
        $cp = $this->catalogo('ASME IX')['consumible_posiciones'];

        $this->assertArrayNotHasKey('F-No. 4', $cp, 'F-No. 4 no debe tener restricción de posición');
    }

    /** F-No. 5 y F-No. 6 tampoco tienen restricción */
    public function test_asme_f5_f6_sin_restriccion_posicion(): void
    {
        $cp = $this->catalogo('ASME IX')['consumible_posiciones'];

        $this->assertArrayNotHasKey('F-No. 5', $cp);
        $this->assertArrayNotHasKey('F-No. 6', $cp);
    }

    // ── API 1104 — material / consumible ─────────────────────────────────────

    private function api1104Mc(): array
    {
        return $this->catalogo('API 1104')['metal_consumible'];
    }

    /** X70-X80 alta resistencia no acepta celulósico WF-1 */
    public function test_api1104_x70_x80_no_acepta_wf1_celulosico(): void
    {
        $mc  = $this->api1104Mc();
        $key = array_key_first(array_filter($mc, fn($_, $k) => str_contains($k, 'X70'), ARRAY_FILTER_USE_BOTH));

        $this->assertNotNull($key, 'Debe existir grupo X70-X80 en metal_consumible');
        $this->assertContains('WF-2', $mc[$key], 'X70-X80 debe aceptar WF-2');
        $this->assertContains('WF-3', $mc[$key], 'X70-X80 debe aceptar WF-3 vertical-down');
        $this->assertNotContains('WF-1', $mc[$key], 'X70-X80 NO debe aceptar WF-1 celulósico');
    }

    /** Grado A/B no acepta WF-3 (vertical-down — solo para X42+) */
    public function test_api1104_grado_ab_no_acepta_wf3_vertical_down(): void
    {
        $mc  = $this->api1104Mc();
        $key = array_key_first(array_filter($mc, fn($_, $k) => str_contains($k, 'A/B'), ARRAY_FILTER_USE_BOTH));

        $this->assertNotNull($key, 'Debe existir grupo A/B en metal_consumible');
        $this->assertContains('WF-1', $mc[$key], 'Grado A/B acepta celulósico');
        $this->assertNotContains('WF-3', $mc[$key], 'Grado A/B NO acepta WF-3 vertical-down');
    }

    /** X56-X65 acepta todos los grupos WF */
    public function test_api1104_x56_x65_acepta_todos_los_wf(): void
    {
        $mc  = $this->api1104Mc();
        $key = array_key_first(array_filter($mc, fn($_, $k) => str_contains($k, 'X56'), ARRAY_FILTER_USE_BOTH));

        $this->assertNotNull($key, 'Debe existir grupo X56-X65 en metal_consumible');
        foreach (['WF-1', 'WF-2', 'WF-3', 'WF-4', 'WF-6'] as $wf) {
            $this->assertContains($wf, $mc[$key], "X56-X65 debe aceptar $wf");
        }
    }

    // ── API 650 — material / consumible ──────────────────────────────────────

    /** Grupo IV (alta resistencia) solo acepta F4 — bajo hidrógeno obligatorio */
    public function test_api650_grupo_iv_solo_bajo_hidrogeno(): void
    {
        $mc = $this->catalogo('API 650')['metal_consumible'];

        $this->assertContains('F4', $mc['Grupo IV']);
        $this->assertNotContains('F1', $mc['Grupo IV'], 'Grupo IV NO acepta F1 (celulósico)');
        $this->assertNotContains('F2', $mc['Grupo IV'], 'Grupo IV NO acepta F2 (rutílico)');
        $this->assertNotContains('F3', $mc['Grupo IV'], 'Grupo IV NO acepta F3');
    }

    /** Grupo III excluye celulósico F1 */
    public function test_api650_grupo_iii_no_acepta_f1(): void
    {
        $mc = $this->catalogo('API 650')['metal_consumible'];

        $this->assertNotContains('F1', $mc['Grupo III']);
        $this->assertContains('F3', $mc['Grupo III']);
        $this->assertContains('F4', $mc['Grupo III']);
    }

    /** Grupo I acepta todos los grupos de consumible */
    public function test_api650_grupo_i_acepta_todos_los_consumibles(): void
    {
        $mc = $this->catalogo('API 650')['metal_consumible'];

        foreach (['F1', 'F2', 'F3', 'F4'] as $f) {
            $this->assertContains($f, $mc['Grupo I'], "Grupo I debe aceptar $f");
        }
    }

    // ── Procesos por norma ────────────────────────────────────────────────────

    /** AWS D1.6 (solo SMAW/GTAW/GMAW/FCAW) no incluye SAW */
    public function test_aws_d16_no_incluye_saw(): void
    {
        $procesos = array_column($this->catalogo('AWS D1.6')['procesos'], 'nombre');

        $this->assertNotContains('SAW', $procesos, 'AWS D1.6 no acepta SAW');
        $this->assertContains('SMAW', $procesos);
        $this->assertContains('GTAW', $procesos);
    }

    /** API 650 incluye SAW (tanques grandes usan arco sumergido) */
    public function test_api650_incluye_saw(): void
    {
        $procesos = array_column($this->catalogo('API 650')['procesos'], 'nombre');

        $this->assertContains('SAW', $procesos, 'API 650 debe incluir SAW');
    }

    /** API 1104 no incluye SAW (rarísimo en gasoductos) */
    public function test_api1104_no_incluye_saw(): void
    {
        $procesos = array_column($this->catalogo('API 1104')['procesos'], 'nombre');

        $this->assertNotContains('SAW', $procesos, 'API 1104 no acepta SAW');
        $this->assertContains('SMAW', $procesos);
        $this->assertContains('GMAW', $procesos);
    }

    // ── ASME B31.3 — delegación ───────────────────────────────────────────────

    /** B31.3 delega a ASME IX: mismas compatibilidades metal/consumible */
    public function test_b313_hereda_compatibilidades_de_asme_ix(): void
    {
        $asme = $this->catalogo('ASME IX');
        $b313 = $this->catalogo('ASME B31.3');

        $this->assertEquals(
            $asme['metal_consumible'],
            $b313['metal_consumible'],
            'B31.3 debe tener el mismo mapa metal/consumible que ASME IX',
        );
        $this->assertCount(
            count($asme['grupos_base_metal']),
            $b313['grupos_base_metal'],
            'B31.3 debe tener los mismos grupos de metal base que ASME IX',
        );
    }

    /** B31.8 también hereda de ASME IX */
    public function test_b318_hereda_compatibilidades_de_asme_ix(): void
    {
        $asme = $this->catalogo('ASME IX');
        $b318 = $this->catalogo('ASME B31.8');

        $this->assertEquals($asme['metal_consumible'], $b318['metal_consumible']);
    }

    // ── AWS D1.6 — solo inox ─────────────────────────────────────────────────

    /** En D1.6 todos los grupos de material solo aceptan consumibles inox */
    public function test_aws_d16_grupos_inox_solo_aceptan_consumibles_inox(): void
    {
        $mc = $this->catalogo('AWS D1.6')['metal_consumible'];

        foreach (['Grupo A', 'Grupo B', 'Grupo C', 'Grupo D'] as $grupo) {
            $this->assertArrayHasKey($grupo, $mc, "$grupo debe estar en el mapa");
            $this->assertContains('F-No. 5',    $mc[$grupo], "$grupo debe aceptar F-No. 5");
            $this->assertContains('F-No. 6-SS', $mc[$grupo], "$grupo debe aceptar F-No. 6-SS");
        }
    }

    // ── Downward qualification (grupos_consumible_calificado) ─────────────────

    /** F-No. 4 debe calificar F-No. 1, 2, 3 y 4 (QW-433) */
    public function test_asme_f4_califica_grupos_inferiores(): void
    {
        $f4Id = \Illuminate\Support\Facades\DB::table('grupos_consumible')
            ->join('normas', 'normas.id', '=', 'grupos_consumible.norma_id')
            ->where('normas.nombre', 'ASME IX')
            ->where('grupos_consumible.codigo', 'F-No. 4')
            ->value('grupos_consumible.id');

        $calificados = \Illuminate\Support\Facades\DB::table('grupo_consumible_calificado')
            ->join('grupos_consumible', 'grupos_consumible.id', '=', 'grupo_consumible_calificado.calificado_id')
            ->where('probado_id', $f4Id)
            ->pluck('grupos_consumible.codigo')
            ->toArray();

        foreach (['F-No. 1', 'F-No. 2', 'F-No. 3', 'F-No. 4'] as $esperado) {
            $this->assertContains($esperado, $calificados, "F-No. 4 debe calificar $esperado (QW-433)");
        }
        $this->assertNotContains('F-No. 5', $calificados, 'F-No. 4 NO califica F-No. 5');
        $this->assertNotContains('F-No. 6', $calificados, 'F-No. 4 NO califica F-No. 6');
    }

    /** F-No. 5 (inox) no tiene downward qualification */
    public function test_asme_f5_no_tiene_downward_qualification(): void
    {
        $f5Id = \Illuminate\Support\Facades\DB::table('grupos_consumible')
            ->join('normas', 'normas.id', '=', 'grupos_consumible.norma_id')
            ->where('normas.nombre', 'ASME IX')
            ->where('grupos_consumible.codigo', 'F-No. 5')
            ->value('grupos_consumible.id');

        $calificados = \Illuminate\Support\Facades\DB::table('grupo_consumible_calificado')
            ->join('grupos_consumible', 'grupos_consumible.id', '=', 'grupo_consumible_calificado.calificado_id')
            ->where('probado_id', $f5Id)
            ->pluck('grupos_consumible.codigo')
            ->toArray();

        $this->assertCount(1, $calificados, 'F-No. 5 solo debe calificar a sí mismo');
        $this->assertContains('F-No. 5', $calificados);
    }

    // ── Base metal qualification ranges ──────────────────────────────────────

    /** P-No. 1 Gr. 1 califica todos los grupos de P-No. 1 (QW-422) */
    public function test_asme_p1_gr1_califica_todos_grupos_p1(): void
    {
        $probadoId = \Illuminate\Support\Facades\DB::table('grupos_base_metal')
            ->join('normas', 'normas.id', '=', 'grupos_base_metal.norma_id')
            ->where('normas.nombre', 'ASME IX')
            ->where('grupos_base_metal.codigo', 'P-No. 1 Gr. 1')
            ->value('grupos_base_metal.id');

        $calificados = \Illuminate\Support\Facades\DB::table('grupo_base_metal_calificado')
            ->join('grupos_base_metal', 'grupos_base_metal.id', '=', 'grupo_base_metal_calificado.calificado_id')
            ->where('probado_id', $probadoId)
            ->pluck('grupos_base_metal.codigo')
            ->toArray();

        foreach (['P-No. 1 Gr. 1', 'P-No. 1 Gr. 2', 'P-No. 1 Gr. 3'] as $esperado) {
            $this->assertContains($esperado, $calificados, "P-No. 1 Gr. 1 debe calificar $esperado");
        }
        // NO califica inox
        $this->assertNotContains('P-No. 8 Gr. 1', $calificados);
    }

    /** AWS D1.1: Grupo III califica Grupo I, II y III */
    public function test_aws_d11_grupo_iii_califica_inferiores(): void
    {
        $probadoId = \Illuminate\Support\Facades\DB::table('grupos_base_metal')
            ->join('normas', 'normas.id', '=', 'grupos_base_metal.norma_id')
            ->where('normas.nombre', 'AWS D1.1')
            ->where('grupos_base_metal.codigo', 'Grupo III')
            ->value('grupos_base_metal.id');

        $calificados = \Illuminate\Support\Facades\DB::table('grupo_base_metal_calificado')
            ->join('grupos_base_metal', 'grupos_base_metal.id', '=', 'grupo_base_metal_calificado.calificado_id')
            ->where('probado_id', $probadoId)
            ->pluck('grupos_base_metal.codigo')
            ->toArray();

        foreach (['Grupo I', 'Grupo II', 'Grupo III'] as $esperado) {
            $this->assertContains($esperado, $calificados);
        }
        $this->assertNotContains('Grupo IV', $calificados, 'Grupo III NO califica Grupo IV');
    }
}
