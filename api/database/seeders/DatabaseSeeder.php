<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Norma;
use App\Models\Proceso;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuarios ─────────────────────────────────────────────────────────
        User::firstOrCreate(['email' => 'admin@kosoldar.com'], [
            'nombre'   => 'Admin',
            'apellido' => 'Kosoldar',
            'password' => Hash::make('123456789'),
            'rol'      => 'admin',
        ]);

        // ── Procesos ─────────────────────────────────────────────────────────
        foreach (['SMAW', 'GMAW', 'GTAW', 'FCAW', 'SAW', 'PAW', 'OFW'] as $p) {
            Proceso::firstOrCreate(['nombre' => $p]);
        }

        // ── Normas ───────────────────────────────────────────────────────────
        foreach ([
            'ASME IX', 'AWS D1.1', 'AWS D1.6', 'API 1104',
            'API 650', 'ASME B31.3', 'ASME B31.8', 'IRAM',
        ] as $n) {
            Norma::firstOrCreate(['nombre' => $n]);
        }

        // B31.3 y B31.8 delegan calificación a ASME IX
        $asmeIxId  = Norma::where('nombre', 'ASME IX')->value('id');
        $api1104Id = Norma::where('nombre', 'API 1104')->value('id');
        Norma::where('nombre', 'ASME B31.3')->update(['parent_norma_id' => $asmeIxId]);
        Norma::where('nombre', 'ASME B31.8')->update(['parent_norma_id' => $asmeIxId]);

        // NAG 105: Cat. A y B delegan a ASME IX; Cat. C a API 1104; Cat. D standalone
        foreach ([
            ['nombre' => 'NAG 105 Cat. A', 'parent' => $asmeIxId],
            ['nombre' => 'NAG 105 Cat. B', 'parent' => $asmeIxId],
            ['nombre' => 'NAG 105 Cat. C', 'parent' => $api1104Id],
            ['nombre' => 'NAG 105 Cat. D', 'parent' => null],
        ] as $row) {
            $n = Norma::firstOrCreate(['nombre' => $row['nombre']]);
            if ($row['parent']) $n->update(['parent_norma_id' => $row['parent']]);
        }

        // ── Materiales (catálogo simple) ──────────────────────────────────────
        foreach ([
            'CHAPA - Acero al Carbono',
            'TUBERÍA - Acero al Carbono',
            'TUBERÍA - API 5L',
            'Acero Inoxidable',
            'Cromo-Molibdeno',
            'Aluminio',
        ] as $m) {
            Material::firstOrCreate(['nombre' => $m]);
        }

        // ── Datos de prueba ───────────────────────────────────────────────────
        $this->call([
            EmpresaSeeder::class,
            SoldadorSeeder::class,
            InspectorSeeder::class,
        ]);

        // ── Catálogos normativos ──────────────────────────────────────────────
        $this->call([
            PosicionSeeder::class,
            GrupoBaseMetalSeeder::class,
            GrupoConsumibleSeeder::class,
            ConsumibleSeeder::class,
        ]);

        // ── Pivots de compatibilidad y rangos ─────────────────────────────────
        $this->call([
            NormaProcesoSeeder::class,
            GrupoBaseMetalConsumibleSeeder::class,
            GrupoConsumiblePosicionSeeder::class,
            GrupoBaseMetalCalificadoSeeder::class,
            GrupoConsumibleCalificadoSeeder::class,
        ]);

        // ── Motor de reglas normativas ────────────────────────────────────────
        $this->call([
            JointDesignSeeder::class,
            ThicknessRuleSeeder::class,
            DiameterRuleSeeder::class,
            QualifiedPositionSeeder::class,
            TestTypeSeeder::class,
        ]);

        // ── Plantillas de certificado ─────────────────────────────────────────
        $this->call([CertLayoutSeeder::class]);
    }
}
