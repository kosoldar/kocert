<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Populates the _es translation columns on existing records.
 * Run this after the add_spanish_translations migration on an already-seeded database.
 * On fresh installs, the original seeders already include the translations.
 */
class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $this->translatePositions();
        $this->translateWeldingProcesses();
        $this->translateTestTypes();
        $this->translateBaseMetalGroups();
        $this->translateConsumableGroups();
        $this->translateBaseMaterials();
    }

    private function translatePositions(): void
    {
        $translations = [
            '1G'  => 'Plana (ranura)',
            '2G'  => 'Horizontal (ranura)',
            '3G'  => 'Vertical (ranura)',
            '4G'  => 'Sobre cabeza (ranura)',
            '5G'  => 'Tubería fija horizontal (ranura)',
            '6G'  => 'Tubería fija inclinada 45° (ranura)',
            '1F'  => 'Plana (filete)',
            '2F'  => 'Horizontal (filete)',
            '2FR' => 'Tubería rotada horizontal (filete)',
            '3F'  => 'Vertical (filete)',
            '4F'  => 'Sobre cabeza (filete)',
            '5F'  => 'Tubería todas posiciones (filete)',
        ];

        foreach ($translations as $code => $name_es) {
            DB::table('positions')->where('code', $code)->update(['name_es' => $name_es]);
        }
    }

    private function translateWeldingProcesses(): void
    {
        $translations = [
            'SMAW' => 'Soldadura de Arco con Electrodo Revestido',
            'GTAW' => 'Soldadura de Arco con Electrodo de Tungsteno (TIG)',
            'GMAW' => 'Soldadura de Arco en Atmósfera de Gas (MIG/MAG)',
            'FCAW' => 'Soldadura de Arco con Núcleo Fundente',
            'SAW'  => 'Soldadura de Arco Sumergido',
            'OFW'  => 'Soldadura Oxiacetilénica',
            'PAW'  => 'Soldadura de Arco de Plasma',
        ];

        foreach ($translations as $code => $name_es) {
            DB::table('welding_processes')->where('code', $code)->update(['name_es' => $name_es]);
        }
    }

    private function translateTestTypes(): void
    {
        $translations = [
            'VT'   => 'Examen Visual',
            'BT-F' => 'Ensayo de Doblado de Cara',
            'BT-R' => 'Ensayo de Doblado de Raíz',
            'BT-S' => 'Ensayo de Doblado Lateral',
            'RT'   => 'Examen Radiográfico',
            'UT'   => 'Examen Ultrasónico',
            'NB'   => 'Ensayo de Fractura (Nick Break)',
            'MT'   => 'Examen de Partículas Magnéticas',
            'PT'   => 'Examen de Líquidos Penetrantes',
            'MS'   => 'Examen de Macrografía',
        ];

        // Update all rows matching each code (código es el mismo en todas las normas)
        foreach ($translations as $code => $name_es) {
            DB::table('test_types')->where('code', $code)->update(['name_es' => $name_es]);
        }
    }

    private function translateBaseMetalGroups(): void
    {
        $translations = [
            // ASME IX
            'P-No. 1 Gr. 1' => 'Acero al carbono, baja resistencia (fy ≤ 55 ksi)',
            'P-No. 1 Gr. 2' => 'Acero al carbono, resistencia media (55–70 ksi)',
            'P-No. 1 Gr. 3' => 'Acero al carbono, alta resistencia (70–80 ksi)',
            'P-No. 3 Gr. 1' => 'Acero aleado — 1/2 Cr, 1/2 Mo; 1 Cr, 1/2 Mo',
            'P-No. 3 Gr. 2' => 'Acero aleado — 1-1/4 Cr, 1/2 Mo; 2-1/4 Cr, 1 Mo',
            'P-No. 4 Gr. 1' => 'Acero aleado — 2 Cr, 1 Mo; 5 Cr, 1/2 Mo',
            'P-No. 8 Gr. 1' => 'Acero inoxidable austenítico — 304, 316, 347',
            // AWS D1.1
            'Group I'       => 'Acero al carbono, fy ≤ 36 ksi (250 MPa)',
            'Group II'      => 'Acero al carbono de alta resistencia, fy 42–65 ksi',
            'Group III'     => 'Acero de baja aleación y alta resistencia, fy 46–70 ksi',
            'Group IV'      => 'Acero de alta resistencia templado y revenido, fy 90–100 ksi',
            // API 1104
            'API 5L A/B'      => 'API 5L Grados A y B — SMYS ≤ 42 ksi (290 MPa)',
            'API 5L X42–X52'  => 'API 5L Grados X42, X46, X52 — SMYS 42–52 ksi',
            'API 5L X56–X65'  => 'API 5L Grados X56, X60, X65 — SMYS 56–65 ksi',
            'API 5L X70–X80'  => 'API 5L Grados X70, X80 — Alta resistencia (SMYS ≥ 70 ksi)',
        ];

        foreach ($translations as $code => $description_es) {
            DB::table('base_metal_groups')->where('code', $code)->update(['description_es' => $description_es]);
        }
    }

    private function translateConsumableGroups(): void
    {
        $translations = [
            // ASME IX F-Numbers
            'F-No. 1' => 'Electrodos de bajo hidrógeno, plana/horizontal solamente (EXX20/24/27/28)',
            'F-No. 2' => 'Electrodos con revestimiento rutílico, todas posiciones (EXX12/13/14)',
            'F-No. 3' => 'Electrodos celulósicos, todas posiciones (EXX10/11) — E6010, E6011',
            'F-No. 4' => 'Electrodos de bajo hidrógeno, todas posiciones (EXX15/16/18) — E7018',
            'F-No. 5' => 'Electrodos de acero inoxidable austenítico (EXXX(X)-15/16)',
            'F-No. 6' => 'Metales de aporte de acero para GMAW, GTAW, FCAW, SAW (ER70S, E71T)',
            // AWS D1.1
            'F1'      => 'Plana/horizontal solamente — EXX20/24/27/28',
            'F2'      => 'Rutílico, todas posiciones — EXX12/13/14',
            'F3'      => 'Celulósico, todas posiciones — EXX10/11',
            'F4'      => 'Bajo hidrógeno, todas posiciones — EXX15/16/18',
            // API 1104 WF-Groups
            'WF-1'    => 'SMAW celulósico — EXX10, EXX11 (A5.1 / A5.5)',
            'WF-2'    => 'SMAW bajo hidrógeno — EXX15/16/18 (A5.1 / A5.5)',
            'WF-3'    => 'SMAW bajo hidrógeno descendente — E8045/9045/10045',
            'WF-4'    => 'Alambre GMAW / GTAW — ERXXS-X (A5.18 / A5.28)',
            'WF-5'    => 'Soldadura oxiacetilénica — RG60, RG65 (A5.2)',
            'WF-6'    => 'FCAW con gas de protección — E71T-1C/M, E71T-9C/M (A5.20 / A5.36)',
        ];

        foreach ($translations as $code => $description_es) {
            DB::table('consumable_groups')->where('code', $code)->update(['description_es' => $description_es]);
        }
    }

    private function translateBaseMaterials(): void
    {
        // Keyed by specification — unique enough for this dataset
        $translations = [
            // ASME IX — P-No. 1 Gr. 1
            'A/SA-36'          => 'Planchuela, barras y perfiles de acero al carbono estructural',
            'A/SA-53 Gr. A'    => 'Caño de acero sin costura y soldado — Grado A',
            'A/SA-53 Gr. B'    => 'Caño de acero sin costura y soldado — Grado B',
            'A/SA-106 Gr. A'   => 'Caño de acero al carbono sin costura para alta temperatura — Grado A',
            'A/SA-106 Gr. B'   => 'Caño de acero al carbono sin costura para alta temperatura — Grado B',
            'A/SA-106 Gr. C'   => 'Caño de acero al carbono sin costura para alta temperatura — Grado C',
            'API 5L Gr. A'     => 'Tubería de conducción — Grado A',
            'API 5L Gr. B'     => 'Tubería de conducción — Grado B',
            'API 5L X42'       => 'Tubería de conducción — Grado X42',
            'API 5L X46'       => 'Tubería de conducción — Grado X46',
            'API 5L X52'       => 'Tubería de conducción — Grado X52',
            'A/SA-516 Gr. 55'  => 'Planchuela de acero para recipientes a presión — Grado 55',
            'A/SA-516 Gr. 60'  => 'Planchuela de acero para recipientes a presión — Grado 60',
            'A/SA-105'         => 'Forjados para componentes de cañería',
            // ASME IX — P-No. 1 Gr. 2
            'A/SA-516 Gr. 65'  => 'Planchuela de acero para recipientes a presión — Grado 65',
            'A/SA-516 Gr. 70'  => 'Planchuela de acero para recipientes a presión — Grado 70',
            'API 5L X56'       => 'Tubería de conducción — Grado X56',
            'API 5L X60'       => 'Tubería de conducción — Grado X60',
            'API 5L X65'       => 'Tubería de conducción — Grado X65',
            'A/SA-285 Gr. C'   => 'Planchuela de acero al carbono para recipientes a presión — Grado C',
            // ASME IX — P-No. 1 Gr. 3
            'API 5L X70'       => 'Tubería de conducción — Grado X70',
            'API 5L X80'       => 'Tubería de conducción — Grado X80',
            'A/SA-572 Gr. 65'  => 'Acero estructural de baja aleación y alta resistencia — Grado 65',
            // AWS D1.1
            'ASTM A36'         => 'Acero al carbono estructural',
            'ASTM A53 Gr. B'   => 'Caño — Grado B',
            'ASTM A106 Gr. B'  => 'Caño sin costura — Grado B',
            'ASTM A572 Gr. 42' => 'Acero de baja aleación y alta resistencia — Grado 42',
            'ASTM A572 Gr. 50' => 'Acero de baja aleación y alta resistencia — Grado 50',
            'ASTM A588 Gr. A'  => 'Acero de baja aleación resistente a la intemperie — Grado A',
        ];

        foreach ($translations as $spec => $description_es) {
            DB::table('base_materials')
                ->where('specification', $spec)
                ->update(['description_es' => $description_es]);
        }

        // API 1104 materials share specifications but have SMYS info in description
        // Update by matching both specification and the SMYS substring
        $apiTranslations = [
            ['spec' => 'API 5L Gr. A', 'substr' => '172 MPa',  'es' => 'Tubería de conducción — Grado A (SMYS 25 ksi / 172 MPa)'],
            ['spec' => 'API 5L Gr. B', 'substr' => '241 MPa',  'es' => 'Tubería de conducción — Grado B (SMYS 35 ksi / 241 MPa)'],
            ['spec' => 'ASTM A53 Gr. B','substr' => 'Pipe',    'es' => 'Caño — Grado B'],
            ['spec' => 'ASTM A106 Gr. B','substr' => 'Seamless','es' => 'Caño sin costura — Grado B'],
            ['spec' => 'API 5L X42',   'substr' => '290 MPa',  'es' => 'Tubería de conducción — Grado X42 (SMYS 42 ksi / 290 MPa)'],
            ['spec' => 'API 5L X46',   'substr' => '317 MPa',  'es' => 'Tubería de conducción — Grado X46 (SMYS 46 ksi / 317 MPa)'],
            ['spec' => 'API 5L X52',   'substr' => '359 MPa',  'es' => 'Tubería de conducción — Grado X52 (SMYS 52 ksi / 359 MPa)'],
            ['spec' => 'API 5L X56',   'substr' => '386 MPa',  'es' => 'Tubería de conducción — Grado X56 (SMYS 56 ksi / 386 MPa)'],
            ['spec' => 'API 5L X60',   'substr' => '414 MPa',  'es' => 'Tubería de conducción — Grado X60 (SMYS 60 ksi / 414 MPa)'],
            ['spec' => 'API 5L X65',   'substr' => '448 MPa',  'es' => 'Tubería de conducción — Grado X65 (SMYS 65 ksi / 448 MPa)'],
            ['spec' => 'API 5L X70',   'substr' => '483 MPa',  'es' => 'Tubería de conducción — Grado X70 (SMYS 70 ksi / 483 MPa)'],
            ['spec' => 'API 5L X80',   'substr' => '552 MPa',  'es' => 'Tubería de conducción — Grado X80 (SMYS 80 ksi / 552 MPa)'],
        ];

        foreach ($apiTranslations as $t) {
            DB::table('base_materials')
                ->where('specification', $t['spec'])
                ->where('description', 'like', '%' . $t['substr'] . '%')
                ->update(['description_es' => $t['es']]);
        }
    }
}
