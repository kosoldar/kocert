<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Inspector;
use App\Models\Norma;
use App\Models\Proceso;
use App\Models\Soldador;
use App\Models\User;
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
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CertificadoValidacionTest extends TestCase
{
    use RefreshDatabase;

    private User      $user;
    private Empresa   $empresa;
    private Soldador  $soldador;
    private Inspector $inspector;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['SMAW', 'GMAW', 'GTAW', 'FCAW', 'SAW', 'PAW', 'OFW'] as $p) {
            Proceso::firstOrCreate(['nombre' => $p]);
        }
        foreach ([
            'ASME IX', 'AWS D1.1', 'AWS D1.6', 'API 1104',
            'API 650', 'ASME B31.3', 'ASME B31.8', 'IRAM',
        ] as $n) {
            Norma::firstOrCreate(['nombre' => $n]);
        }
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

        $this->user = User::firstOrCreate(
            ['email' => 'test@kocert.test'],
            ['nombre' => 'Test', 'apellido' => 'User', 'password' => Hash::make('s'), 'rol' => 'admin'],
        );

        $this->empresa = Empresa::firstOrCreate(
            ['nombre' => 'Empresa Test'],
            ['cuit' => '30-12345678-9', 'activo' => true],
        );

        $this->soldador = Soldador::firstOrCreate(
            ['dni' => '99999999'],
            [
                'nombre'   => 'Soldador',
                'apellido' => 'Test',
                'activo'   => true,
            ],
        );

        $this->inspector = Inspector::firstOrCreate(
            ['nombre' => 'Inspector Test'],
            ['certificacion' => 'CSWIP 3.1', 'activo' => true],
        );
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function normaId(string $nombre): int
    {
        return Norma::where('nombre', $nombre)->value('id');
    }

    private function basePayload(string $norma, array $variables = [], array $overrides = []): array
    {
        return array_merge([
            'numero'             => 1,
            'anio'               => 2026,
            'soldador_id'        => $this->soldador->id,
            'empresa_id'         => $this->empresa->id,
            'norma_id'           => $this->normaId($norma),
            'inspector_id'       => $this->inspector->id,
            'tipo'               => 'inicial',
            'resultado'          => 'aprobado',
            'fecha_calificacion' => '2026-06-20',
            'eps_numero'         => 'WPS-001',
            'proceso'            => 'SMAW',
            'posicion'           => '2G',
            'tipo_cupon'         => 'chapa',
            'fecha_vencimiento'  => '2027-06-20',
            'variables'          => $variables,
        ], $overrides);
    }

    private function crearCert(array $payload): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->user)->postJson('/api/certificados', $payload);
    }

    // ── Proceso válido / inválido ─────────────────────────────────────────────

    public function test_proceso_valido_para_norma_pasa(): void
    {
        $payload = $this->basePayload('ASME IX', [
            'p_number' => 'P-No. 1 Gr. 1',
            'f_number' => 'F-No. 4',
            'electrodo' => 'E7018',
            'respaldo' => 'SIN RESPALDO',
            'corriente' => 'CCEP',
            'progresion' => 'ascendente',
            'espesor_cupon' => 10,
            'resultado_vt' => 'aprobado',
            'resultado_bend' => 'aprobado',
        ]);

        $this->crearCert($payload)->assertStatus(201);
    }

    public function test_proceso_invalido_para_norma_falla(): void
    {
        // AWS D1.6 no acepta SAW
        $payload = $this->basePayload('AWS D1.6', [], ['proceso' => 'SAW']);

        $response = $this->crearCert($payload);
        $response->assertStatus(422);
        $this->assertStringContainsString('SAW', $response->json('errors.proceso.0'));
    }

    public function test_proceso_invalido_api1104_falla(): void
    {
        // API 1104 no acepta SAW
        $payload = $this->basePayload('API 1104', [], ['proceso' => 'SAW']);

        $this->crearCert($payload)->assertStatus(422)
            ->assertJsonValidationErrors(['proceso']);
    }

    // ── Metal base + consumible ───────────────────────────────────────────────

    public function test_inox_con_electrodo_carbono_falla(): void
    {
        // P-No. 8 (inox 304/316) + F-No. 4 (E7018) → combinación inválida
        $payload = $this->basePayload('ASME IX', [
            'p_number' => 'P-No. 8 Gr. 1',
            'f_number' => 'F-No. 4',        // bajo hidrógeno carbono: NO válido con inox
        ]);

        $this->crearCert($payload)->assertStatus(422)
            ->assertJsonValidationErrors(['variables.f_number']);
    }

    public function test_inox_con_electrodo_inox_pasa(): void
    {
        $payload = $this->basePayload('ASME IX', [
            'p_number'   => 'P-No. 8 Gr. 1',
            'f_number'   => 'F-No. 5',      // E308L-16: válido para inox
            'electrodo'  => 'E308L-16',
            'respaldo'   => 'SIN RESPALDO',
            'corriente'  => 'CCEP',
            'progresion' => 'ascendente',
            'espesor_cupon'  => 5,
            'resultado_vt'   => 'aprobado',
            'resultado_bend' => 'aprobado',
        ]);

        $this->crearCert($payload)->assertStatus(201);
    }

    public function test_carbono_con_electrodo_inox_falla(): void
    {
        // P-No. 1 (carbono) + F-No. 5 (inox SMAW) → combinación inválida
        $payload = $this->basePayload('ASME IX', [
            'p_number' => 'P-No. 1 Gr. 1',
            'f_number' => 'F-No. 5',
        ]);

        $this->crearCert($payload)->assertStatus(422)
            ->assertJsonValidationErrors(['variables.f_number']);
    }

    public function test_duplex_con_electrodo_carbono_falla(): void
    {
        $payload = $this->basePayload('ASME IX', [
            'p_number' => 'P-No. 10H',
            'f_number' => 'F-No. 3',   // celulósico E6010: inválido para dúplex
        ]);

        $this->crearCert($payload)->assertStatus(422)
            ->assertJsonValidationErrors(['variables.f_number']);
    }

    public function test_api650_grupo_iv_con_f1_celulosico_falla(): void
    {
        $payload = $this->basePayload('API 650', [
            'grupo_base_metal' => 'Grupo IV',
            'grupo_consumible' => 'F1',    // celulósico: inválido para Grupo IV
        ]);

        $this->crearCert($payload)->assertStatus(422)
            ->assertJsonValidationErrors(['variables.grupo_consumible']);
    }

    public function test_api650_grupo_iv_con_f4_bajo_hidrogeno_pasa(): void
    {
        $payload = $this->basePayload('API 650', [
            'tipo_junta'       => 'RANURA',
            'grupo_base_metal' => 'Grupo IV',
            'grupo_consumible' => 'F4',
            'respaldo'         => 'SIN RESPALDO',
            'espesor_cupon'    => 20,
            'resultado_vt'     => 'aprobado',
            'resultado_bend'   => 'aprobado',
        ]);

        $this->crearCert($payload)->assertStatus(201);
    }

    public function test_api650_grupo_i_con_cualquier_f_pasa(): void
    {
        foreach (['F1', 'F2', 'F3', 'F4'] as $i => $f) {
            $payload = $this->basePayload('API 650', [
                'tipo_junta'       => 'RANURA',
                'grupo_base_metal' => 'Grupo I',
                'grupo_consumible' => $f,
                'respaldo'         => 'SIN RESPALDO',
                'espesor_cupon'    => 10,
                'resultado_vt'     => 'aprobado',
                'resultado_bend'   => 'aprobado',
            ], ['numero' => 10 + $i]);

            $this->crearCert($payload)->assertStatus(201, "Grupo I + $f debe ser válido");
        }
    }

    // ── Consumible + posición ─────────────────────────────────────────────────

    public function test_f1_en_posicion_vertical_falla(): void
    {
        // F-No. 1 (EXX24/28) no se puede usar en vertical (3G)
        $payload = $this->basePayload('ASME IX', [
            'p_number' => 'P-No. 1 Gr. 1',
            'f_number' => 'F-No. 1',
        ], ['posicion' => '3G']);

        $this->crearCert($payload)->assertStatus(422)
            ->assertJsonValidationErrors(['posicion']);
    }

    public function test_f1_en_posicion_sobrecabeza_falla(): void
    {
        $payload = $this->basePayload('ASME IX', [
            'p_number' => 'P-No. 1 Gr. 1',
            'f_number' => 'F-No. 1',
        ], ['posicion' => '4G']);

        $this->crearCert($payload)->assertStatus(422)
            ->assertJsonValidationErrors(['posicion']);
    }

    public function test_f1_en_posicion_plana_pasa(): void
    {
        $payload = $this->basePayload('ASME IX', [
            'p_number'   => 'P-No. 1 Gr. 1',
            'f_number'   => 'F-No. 1',
            'electrodo'  => 'E7024',
            'respaldo'   => 'CON RESPALDO',
            'corriente'  => 'CA',
            'progresion' => 'ascendente',
            'espesor_cupon'  => 12,
            'resultado_vt'   => 'aprobado',
            'resultado_bend' => 'aprobado',
        ], ['posicion' => '1G']);

        $this->crearCert($payload)->assertStatus(201);
    }

    public function test_f4_en_posicion_vertical_pasa(): void
    {
        // F-No. 4 no tiene restricción de posición
        $payload = $this->basePayload('ASME IX', [
            'p_number'   => 'P-No. 1 Gr. 1',
            'f_number'   => 'F-No. 4',
            'electrodo'  => 'E7018',
            'respaldo'   => 'SIN RESPALDO',
            'corriente'  => 'CCEP',
            'progresion' => 'ascendente',
            'espesor_cupon'  => 10,
            'resultado_vt'   => 'aprobado',
            'resultado_bend' => 'aprobado',
        ], ['posicion' => '3G', 'numero' => 99]);

        $this->crearCert($payload)->assertStatus(201);
    }

    // ── Update también valida ─────────────────────────────────────────────────

    public function test_update_con_combinacion_invalida_falla(): void
    {
        // Crear certificado válido primero
        $payload = $this->basePayload('ASME IX', [
            'p_number'   => 'P-No. 1 Gr. 1',
            'f_number'   => 'F-No. 4',
            'electrodo'  => 'E7018',
            'respaldo'   => 'SIN RESPALDO',
            'corriente'  => 'CCEP',
            'progresion' => 'ascendente',
            'espesor_cupon'  => 10,
            'resultado_vt'   => 'aprobado',
            'resultado_bend' => 'aprobado',
        ]);

        $cert = $this->crearCert($payload)->assertStatus(201)->json();

        // Intentar cambiar solo f_number a uno incompatible con el p_number existente
        $response = $this->actingAs($this->user)
            ->putJson("/api/certificados/{$cert['id']}", [
                'variables' => ['f_number' => 'F-No. 5'],  // inox con carbono: inválido
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['variables.f_number']);
    }
}
