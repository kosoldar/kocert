<?php

namespace Database\Seeders;

use App\Models\CertLayout;
use Illuminate\Database\Seeder;

class CertLayoutSeeder extends Seeder
{
    public function run(): void
    {
        CertLayout::whereIn('nombre', [
            'RCS ASME/AWS/API 650/IRAM',
            'RCS API 1104',
            'RCS NAG 105',
        ])->delete();

        $this->seedAsmeAws();
        $this->seedApi1104();
        $this->seedNag();
    }

    // ── Layout 1: ASME/AWS/API 650/IRAM ──────────────────────────────────────

    private function seedAsmeAws(): void
    {
        CertLayout::create([
            'nombre'      => 'RCS ASME/AWS/API 650/IRAM',
            'descripcion' => 'Template base — ASME IX, AWS D1.1/D1.6, API 650, B31.3, B31.8, IRAM',
            'norma_ids'   => [1, 2, 3, 5, 6, 7, 8],
            'es_default'  => true,
            'orientacion' => 'portrait',
            'thumbnail'   => null,
            'blocks'      => [
                'pageSize'    => 'A4',
                'orientation' => 'portrait',
                'marginMm'    => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],
                'pages'       => [
                    [
                        'pageNumber' => 1,
                        'blocks'     => array_merge(
                            $this->headerBlocks(),
                            $this->welderDataBlocks(),
                            [
                                $this->block('variables_block', [],
                                    x: 5, y: 147, w: 200, h: 118),
                            ],
                            $this->footerBlocks(1, 2),
                        ),
                    ],
                    [
                        'pageNumber' => 2,
                        'blocks'     => array_merge(
                            $this->headerBlocks(),
                            [
                                $this->block('passes_block', [],
                                    x: 5, y: 37, w: 200, h: 50),
                                $this->block('line', ['direction' => 'horizontal'],
                                    x: 5, y: 88, w: 200, h: 1,
                                    style: ['color' => '#cccccc']),
                                $this->block('results_block', [],
                                    x: 5, y: 90, w: 200, h: 100),
                                $this->block('line', ['direction' => 'horizontal'],
                                    x: 5, y: 191, w: 200, h: 1,
                                    style: ['color' => '#cccccc']),
                            ],
                            $this->inspectorBlocks(),
                            $this->footerBlocks(2, 2),
                        ),
                    ],
                ],
            ],
        ]);
    }

    // ── Layout 2: API 1104 ────────────────────────────────────────────────────

    private function seedApi1104(): void
    {
        // NAG Cat.C (id=11) has API 1104 as parent — lookup will resolve here
        CertLayout::create([
            'nombre'      => 'RCS API 1104',
            'descripcion' => 'Template API 1104 Ed 2021 — Línea Regular. Tabla de pasadas en página 1.',
            'norma_ids'   => [4],
            'es_default'  => false,
            'orientacion' => 'portrait',
            'thumbnail'   => null,
            'blocks'      => [
                'pageSize'    => 'A4',
                'orientation' => 'portrait',
                'marginMm'    => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],
                'pages'       => [
                    [
                        'pageNumber' => 1,
                        'blocks'     => array_merge(
                            $this->headerBlocks(),
                            // API 1104 agrega fila Empresa | Cliente | Obra antes de los datos del soldador
                            $this->api1104CompanyRow(),
                            $this->welderDataBlocks(yOffset: 8, includeEmpresaRow: false),
                            [
                                // En API 1104 las variables son tabla de 3 cols (sin columna Range)
                                // El block dinámico usa el mismo renderer — la diferencia visual
                                // la da la norma. Altura reducida porque pasan luego.
                                $this->block('variables_block', [],
                                    x: 5, y: 155, w: 200, h: 65),
                                $this->block('line', ['direction' => 'horizontal'],
                                    x: 5, y: 221, w: 200, h: 1,
                                    style: ['color' => '#cccccc']),
                                // En API 1104 la tabla de pasadas va en página 1 (Características Eléctricas)
                                $this->block('passes_block', [],
                                    x: 5, y: 223, w: 200, h: 42),
                            ],
                            $this->footerBlocks(1, 2),
                        ),
                    ],
                    [
                        'pageNumber' => 2,
                        'blocks'     => array_merge(
                            $this->headerBlocks(),
                            [
                                $this->block('results_block', [],
                                    x: 5, y: 37, w: 200, h: 100),
                                $this->block('line', ['direction' => 'horizontal'],
                                    x: 5, y: 138, w: 200, h: 1,
                                    style: ['color' => '#cccccc']),
                            ],
                            $this->inspectorBlocks(yBase: 142),
                            $this->footerBlocks(2, 2),
                        ),
                    ],
                ],
            ],
        ]);
    }

    // ── Layout 3: NAG 105 ─────────────────────────────────────────────────────

    private function seedNag(): void
    {
        CertLayout::create([
            'nombre'      => 'RCS NAG 105',
            'descripcion' => 'Template NAG 105 Cat.A/B/D (ASME IX base). Cat.C resuelve vía API 1104.',
            'norma_ids'   => [9, 10, 12],
            'es_default'  => false,
            'orientacion' => 'portrait',
            'thumbnail'   => null,
            'blocks'      => [
                'pageSize'    => 'A4',
                'orientation' => 'portrait',
                'marginMm'    => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],
                'pages'       => [
                    [
                        'pageNumber' => 1,
                        'blocks'     => array_merge(
                            $this->headerBlocks(),
                            $this->welderDataBlocks(),
                            [
                                $this->block('variables_block', [],
                                    x: 5, y: 147, w: 200, h: 118),
                            ],
                            $this->footerBlocks(1, 2),
                        ),
                    ],
                    [
                        'pageNumber' => 2,
                        'blocks'     => array_merge(
                            $this->headerBlocks(),
                            [
                                $this->block('passes_block', [],
                                    x: 5, y: 37, w: 200, h: 50),
                                $this->block('line', ['direction' => 'horizontal'],
                                    x: 5, y: 88, w: 200, h: 1,
                                    style: ['color' => '#cccccc']),
                                $this->block('results_block', [],
                                    x: 5, y: 90, w: 200, h: 100),
                                $this->block('line', ['direction' => 'horizontal'],
                                    x: 5, y: 191, w: 200, h: 1,
                                    style: ['color' => '#cccccc']),
                            ],
                            $this->inspectorBlocks(),
                            $this->footerBlocks(2, 2),
                        ),
                    ],
                ],
            ],
        ]);
    }

    // ── Bloques reutilizables ─────────────────────────────────────────────────

    /**
     * Header de 3 columnas idéntico en todas las páginas y layouts.
     * Izquierda: logo Kosoldar + datos inspector
     * Centro: logo norma + títulos RCS/WPQ
     * Derecha: QR pequeño + caja número RCS
     */
    private function headerBlocks(): array
    {
        return [
            // Izquierda: logo
            $this->block('image', ['imageType' => 'logo'],
                x: 5, y: 3, w: 40, h: 16),
            // Izquierda: datos inspector
            $this->block('text', ['content' => "RICARDO KOSIK\nMóvil 3873 651982\nricardokosik@gmail.com"],
                x: 5, y: 19, w: 40, h: 13,
                style: ['fontSize' => 6]),
            // Centro: logo norma
            $this->block('image', ['imageType' => 'norma_logo'],
                x: 70, y: 3, w: 30, h: 12),
            // Centro: norma nombre + edición
            $this->block('text', ['content' => '{{norma.nombre}} {{norma.edicion}}'],
                x: 55, y: 15, w: 100, h: 5,
                style: ['fontSize' => 7, 'textAlign' => 'center']),
            // Centro: título principal
            $this->block('text', ['content' => "REGISTRO DE CALIFICACION DE SOLDADORES\n(RCS)\nWELDER PERFOMANCE QUQLIFICATIONS (WPQ)"],
                x: 55, y: 20, w: 100, h: 13,
                style: ['fontSize' => 8, 'fontWeight' => 'bold', 'textAlign' => 'center']),
            // Derecha: QR pequeño (autenticidad)
            $this->block('image', ['imageType' => 'qr'],
                x: 160, y: 2, w: 20, h: 20),
            // Derecha: caja número RCS
            $this->block('text', ['content' => "RCS / WPQ\nNº {{cert.codigo}}"],
                x: 158, y: 22, w: 47, h: 12,
                style: ['fontSize' => 8, 'fontWeight' => 'bold', 'textAlign' => 'center',
                        'borderWidth' => 1, 'borderColor' => '#000000']),
            // Separador inferior header
            $this->block('line', ['direction' => 'horizontal'],
                x: 5, y: 35, w: 200, h: 1,
                style: ['color' => '#000000']),
        ];
    }

    /**
     * Bloque de datos del soldador — página 1.
     * yOffset permite desplazar verticalmente (API 1104 agrega fila empresa arriba).
     */
    private function welderDataBlocks(float $yOffset = 0, bool $includeEmpresaRow = true): array
    {
        $y = fn(float $base): float => $base + $yOffset;

        return [
            // ── Fila proceso ─────────────────────────────────────────────────
            $this->block('text', ['content' => "Welding process(es): Type\nProceso(s) de Soldadura: Tipo"],
                x: 5, y: $y(37), w: 88, h: 9,
                style: ['fontSize' => 7]),
            $this->block('field', ['fieldKey' => 'cert.proceso'],
                x: 95, y: $y(37), w: 55, h: 9,
                style: ['fontSize' => 14, 'fontWeight' => 'bold', 'textAlign' => 'center']),
            $this->block('text', ['content' => 'MANUAL'],
                x: 152, y: $y(37), w: 53, h: 9,
                style: ['fontSize' => 14, 'fontWeight' => 'bold', 'textAlign' => 'center']),
            $this->block('line', ['direction' => 'horizontal'],
                x: 5, y: $y(46), w: 200, h: 1,
                style: ['color' => '#000000']),

            // ── Nombre soldador ───────────────────────────────────────────────
            $this->block('text', ['content' => "Welder's name-\nNombre del Soldador\nStamp / CUÑO"],
                x: 5, y: $y(47), w: 108, h: 10,
                style: ['fontSize' => 7]),
            $this->block('field', ['fieldKey' => 'soldador.apellido'],
                x: 5, y: $y(57), w: 108, h: 10,
                style: ['fontSize' => 20, 'fontWeight' => 'bold']),
            $this->block('field', ['fieldKey' => 'soldador.nombre'],
                x: 5, y: $y(67), w: 108, h: 8,
                style: ['fontSize' => 16, 'fontWeight' => 'bold']),
            // Foto DNI (derecha, abarca filas soldador)
            $this->block('image', ['imageType' => 'photo'],
                x: 113, y: $y(47), w: 48, h: 55),

            // ── Empresa ───────────────────────────────────────────────────────
            // En API 1104 esta fila la cubre api1104CompanyRow() (Empresa/Cliente/Obra).
            ...($includeEmpresaRow ? [
                $this->block('text', ['content' => 'Company: EMPRESA:'],
                    x: 5, y: $y(76), w: 35, h: 7,
                    style: ['fontSize' => 7]),
                $this->block('field', ['fieldKey' => 'empresa.nombre'],
                    x: 40, y: $y(76), w: 71, h: 7,
                    style: ['fontSize' => 12, 'fontWeight' => 'bold']),
            ] : []),

            // ── DNI ───────────────────────────────────────────────────────────
            $this->block('text', ['content' => 'Identification - Nº documento :'],
                x: 5, y: $y(84), w: 58, h: 7,
                style: ['fontSize' => 7]),
            $this->block('field', ['fieldKey' => 'soldador.dni'],
                x: 63, y: $y(84), w: 48, h: 7,
                style: ['fontSize' => 11, 'fontWeight' => 'bold']),

            // ── EPS / WPS ─────────────────────────────────────────────────────
            $this->block('text', ['content' => "Identification of WPS followed:\nIdentificación de EPS Aplicado:"],
                x: 5, y: $y(92), w: 57, h: 9,
                style: ['fontSize' => 7]),
            $this->block('field', ['fieldKey' => 'cert.eps_numero'],
                x: 63, y: $y(92), w: 48, h: 9,
                style: ['fontSize' => 10, 'fontWeight' => 'bold']),

            // ── Fecha calificación ────────────────────────────────────────────
            $this->block('text', ['content' => "Qualification Date\nFecha de Calificación"],
                x: 5, y: $y(102), w: 57, h: 8,
                style: ['fontSize' => 7]),
            $this->block('field', ['fieldKey' => 'cert.fecha_calificacion'],
                x: 63, y: $y(102), w: 48, h: 8,
                style: ['fontSize' => 10, 'fontWeight' => 'bold']),

            // ── Fecha vencimiento (ROJO) ──────────────────────────────────────
            $this->block('text', ['content' => "Expiration date\nFECHA DE VENCIMIENTO"],
                x: 5, y: $y(111), w: 57, h: 9,
                style: ['fontSize' => 7, 'fontWeight' => 'bold']),
            $this->block('field', ['fieldKey' => 'cert.fecha_vencimiento'],
                x: 63, y: $y(111), w: 48, h: 11,
                style: ['fontSize' => 16, 'fontWeight' => 'bold', 'color' => '#CC0000']),

            // ── Ampliación / Renovación ───────────────────────────────────────
            $this->block('text', ['content' => 'Extension/Renewal of the RCS / WPQ  —  Ampliación/ Renovación del RCS / WPQ'],
                x: 5, y: $y(123), w: 200, h: 6,
                style: ['fontSize' => 7, 'textAlign' => 'center']),
            $this->block('line', ['direction' => 'horizontal'],
                x: 5, y: $y(129), w: 200, h: 1,
                style: ['color' => '#000000']),

            // ── Metal base / cupón — fila 1 ──────────────────────────────────
            $this->block('text', ['content' => 'Test coupon / Cupón de prueba  ' . $this->cb(true) . '      Production Weld / Soldadura de producción  ' . $this->cb(false)],
                x: 5, y: $y(131), w: 83, h: 7,
                style: ['fontSize' => 7]),
            $this->block('text', ['content' => "Specification of base metal(s)\nEspecificación del Metal/es) Base:"],
                x: 90, y: $y(131), w: 58, h: 7,
                style: ['fontSize' => 7]),
            $this->block('field', ['fieldKey' => 'cert.metal_base'],
                x: 150, y: $y(131), w: 55, h: 7,
                style: ['fontSize' => 11, 'fontWeight' => 'bold']),

            // ── Metal base / cupón — fila 2 ──────────────────────────────────
            $this->block('text', ['content' => 'Pipe/ Caño  ' . $this->cb(true) . '    Sheet / Chapa  ' . $this->cb(false)],
                x: 5, y: $y(139), w: 83, h: 7,
                style: ['fontSize' => 7]),
            $this->block('text', ['content' => "Diameter / Thickness / Espesor: {{cert.diametro_espesor}}"],
                x: 90, y: $y(139), w: 58, h: 7,
                style: ['fontSize' => 7]),
            $this->block('text', ['content' => "CALIFICA: {{cert.califica_rangos}}"],
                x: 150, y: $y(139), w: 55, h: 7,
                style: ['fontSize' => 9, 'fontWeight' => 'bold']),
            $this->block('line', ['direction' => 'horizontal'],
                x: 5, y: $y(146), w: 200, h: 1,
                style: ['color' => '#000000']),
        ];
    }

    /**
     * Fila extra API 1104: Empresa | Cliente | Obra — va antes de welderDataBlocks.
     */
    private function api1104CompanyRow(): array
    {
        return [
            $this->block('text', ['content' => 'Company / EMPRESA: {{empresa.nombre}}'],
                x: 5, y: 37, w: 65, h: 7,
                style: ['fontSize' => 8, 'fontWeight' => 'bold']),
            $this->block('text', ['content' => "Client / CLIENTE: {{cert.cliente}}"],
                x: 72, y: 37, w: 65, h: 7,
                style: ['fontSize' => 8]),
            $this->block('text', ['content' => "Work / OBRA: {{cert.obra}}"],
                x: 139, y: 37, w: 66, h: 7,
                style: ['fontSize' => 8]),
            $this->block('line', ['direction' => 'horizontal'],
                x: 5, y: 44, w: 200, h: 1,
                style: ['color' => '#cccccc']),
        ];
    }

    /**
     * Área de firma, inspector y QR grande — página 2.
     * yBase: coordenada y donde empieza la zona (default 192).
     */
    private function inspectorBlocks(float $yBase = 192): array
    {
        return [
            $this->block('text', ['content' => "SOLDADOR APROBADO\nAPPROVED WELDER"],
                x: 5, y: $yBase, w: 100, h: 10,
                style: ['fontSize' => 11, 'fontWeight' => 'bold']),
            $this->block('image', ['imageType' => 'signature'],
                x: 58, y: $yBase + 13, w: 44, h: 18),
            $this->block('field', ['fieldKey' => 'inspector.nombre'],
                x: 48, y: $yBase + 32, w: 110, h: 7,
                style: ['fontSize' => 9, 'fontWeight' => 'bold', 'textAlign' => 'center']),
            $this->block('field', ['fieldKey' => 'inspector.certificacion'],
                x: 48, y: $yBase + 40, w: 110, h: 7,
                style: ['fontSize' => 8, 'textAlign' => 'center']),
            $this->block('text', ['content' => 'Firma y Documento CODIFICADOS - Ley 25.506 régimen internacional de reconocimiento de documentos con firma digital y firma electrónica'],
                x: 20, y: $yBase + 48, w: 130, h: 10,
                style: ['fontSize' => 7, 'color' => '#CC6600', 'textAlign' => 'center']),
            // QR grande (derecha)
            $this->block('image', ['imageType' => 'qr'],
                x: 155, y: $yBase + 7, w: 48, h: 48),
        ];
    }

    /**
     * Footer bilingüe + "Página X de N" — ambas páginas.
     */
    private function footerBlocks(int $page, int $totalPages): array
    {
        return [
            $this->block('text', [
                'content' => "We certify that the statements in this record are correct and that the test coupons were prepared, welded, and tested in accordance with the requirements of {{norma.edicion_texto}}\n"
                           . "Nosotros certificamos que las declaraciones en este registro son correctas y que los cupones de la prueba fueron preparados, soldados y ensayados de acuerdo a los requisitos de {{norma.edicion_texto}}",
            ],
                x: 5, y: 268, w: 200, h: 12,
                style: ['fontSize' => 6, 'textAlign' => 'center']),
            $this->block('text', ['content' => 'VERIFICAR LA AUTENTICIDAD DE ESTE DOCUMENTO ESCANEANDO EL CODIGO QR'],
                x: 5, y: 281, w: 185, h: 6,
                style: ['fontSize' => 7, 'fontWeight' => 'bold', 'textAlign' => 'center']),
            $this->block('text', ['content' => "Página {$page} de {$totalPages}"],
                x: 160, y: 287, w: 45, h: 5,
                style: ['fontSize' => 7, 'textAlign' => 'right']),
        ];
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    /**
     * Checkbox como texto — mPDF no tiene los glifos ☑/☐ (tofu) y el border en
     * inline-block vacío no renderiza (mPDF ignora width/height en elementos inline).
     */
    private function cb(bool $checked): string
    {
        return $checked ? '[X]' : '[&nbsp;&nbsp;]';
    }

    private function block(
        string $type,
        array  $extra,
        float  $x,
        float  $y,
        float  $w,
        float  $h,
        array  $style = [],
    ): array {
        return array_filter([
            'id'     => \Illuminate\Support\Str::uuid()->toString(),
            'type'   => $type,
            'x'      => $x,
            'y'      => $y,
            'width'  => $w,
            'height' => $h,
            'style'  => $style,
        ] + $extra, fn($v) => $v !== null);
    }
}
