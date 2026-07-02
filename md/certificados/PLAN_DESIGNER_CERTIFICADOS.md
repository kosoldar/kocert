# Plan: Certificate Layout Designer (Fabric.js + mPDF)
## Revisado post-audit — 2026-06-29

---

## Estado actual del sistema

- **8 normas activas**: ASME IX, AWS D1.1, AWS D1.6, API 1104, API 650, ASME B31.3, ASME B31.8, IRAM + NAG 105 Cat.A/B/C/D (con jerarquía parent_norma_id)
- **2 Blade templates hardcodeados**: `rcs-aws-asme.blade.php` (cubre ASME/AWS/API650/IRAM) y `rcs-api1104.blade.php` (cubre API 1104)
- **NAG 105 no tiene template propio** — usa el ASME como base (vía parent), pero CatalogoController ya le agrega `campos_norma` específicos
- **`test_types` es por norma** (FK norma_id) — los ensayos requeridos en la sección Results varían por norma
- **`campos_norma`** ya está normalizado en el catálogo (CatalogoController@porNorma lo devuelve)

---

## Arquitectura

```
Angular Designer ──save JSON──► API /cert-layouts
                                     │
                                     ▼
                             cert_layouts (DB)
                                     │
                         PdfCertificadoService.generate()
                                     │
                          ┌──────────┴──────────┐
                          │                     │
                   Layout en DB?          No layout
                          │                     │
                  PdfFromLayoutService    Blade template (fallback)
                          │
                     mPDF output
```

---

## Tipos de bloque — REVISADO

La distinción clave: bloques **estáticos** (posicionables libremente) vs bloques **dinámicos** (opacos — se ubican y dimensionan, pero su contenido interno lo controla PHP).

### Bloques estáticos (posición y estilo libres)

| type | Contenido | Configuración |
|---|---|---|
| `field` | valor de un campo del cert | `fieldKey` (ver tabla de campos) |
| `text` | texto fijo con vars `{{key}}` | contenido editable |
| `image` | imagen fija | `imageType`: `logo` / `norma_logo` / `photo` / `signature` / `qr` |
| `line` | separador | `direction`: horizontal / vertical |

### Bloques dinámicos (opacos — solo posición/tamaño)

| type | Contenido PHP genera | Condición |
|---|---|---|
| `variables_block` | campos del JSON `cert.variables` filtrados por `campos_norma` del catálogo | todas las normas |
| `results_block` | ensayos requeridos con checkbox (de `test_types` por norma) | todas las normas |
| `passes_block` | tabla de pasadas por pasada (`certificate_passes`) | API 1104 + futuros |
| `joint_design_block` | SVG de `joint_designs` + nombre + rangos calificados | normas con diseño de junta |

> **Por qué opacos**: `variables_block` y `results_block` se expanden dinámicamente según los datos. No tienen altura fija. El diseñador muestra un placeholder de altura estimada; el PDF renderiza la altura real.

---

## Campos disponibles para bloques `field`

### Certificado

| fieldKey | Valor |
|---|---|
| `cert.numero` | número RCS |
| `cert.anio` | año (últimos 2 dígitos en código) |
| `cert.revision` | revisión |
| `cert.codigo` | RCS{numero}_{anio} (appended) |
| `cert.tipo` | inicial / renovacion / ampliacion |
| `cert.resultado` | aprobado / rechazado |
| `cert.fecha_calificacion` | fecha formateada |
| `cert.fecha_vencimiento` | fecha formateada |
| `cert.eps_numero` | WPS/EPS |
| `cert.pqr_numero` | PQR/RCP |
| `cert.proceso` | proceso principal |
| `cert.posicion` | posición ensayada |
| `cert.progresion` | ascendente / descendente |
| `cert.tipo_cupon` | caño / chapa |
| `cert.observaciones` | texto libre |
| `cert.joint_detail` | detalle de junta |

### Soldador

| fieldKey | Valor |
|---|---|
| `soldador.apellido` | |
| `soldador.nombre` | |
| `soldador.dni` | |
| `soldador.cuño` | |
| `soldador.ciudad` | (usado en API 1104) |
| `soldador.nacionalidad` | |
| `soldador.fecha_nacimiento` | |

### Empresa, Inspector, Norma

| fieldKey | Valor |
|---|---|
| `empresa.nombre` | |
| `inspector.nombre` | |
| `inspector.telefono` | |
| `inspector.email` | |
| `inspector.certificacion` | |
| `norma.nombre` | |
| `norma.edicion_texto` | texto de edición derivado del nombre |

---

## Modelo de datos `cert_layouts`

```php
Schema::create('cert_layouts', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->string('descripcion')->nullable();

    // Aplicación: null = default para todas las normas
    // Si tiene norma_ids, solo aplica a esas normas
    $table->json('norma_ids')->nullable(); // [1, 3, 7] — array de norma.id

    $table->boolean('es_default')->default(false);
    $table->string('orientacion')->default('portrait'); // portrait | landscape
    $table->json('blocks');           // JSON del layout completo
    $table->string('thumbnail')->nullable(); // data URI del canvas snapshot
    $table->timestamps();
});
```

**Nota sobre resolución de layout:**
```
cert.norma_id → busca layout con norma_ids CONTAINS cert.norma_id
→ si no hay, busca layout con norma_ids CONTAINS cert.norma.parent_norma_id (para NAG)
→ si no hay, usa es_default = true
→ si no hay, usa Blade template (fallback)
```

---

## Estructura JSON del layout

```json
{
  "pageSize": "A4",
  "orientation": "portrait",
  "marginMm": { "top": 10, "right": 10, "bottom": 10, "left": 10 },
  "pages": [
    {
      "pageNumber": 1,
      "blocks": [
        {
          "id": "uuid-1",
          "type": "field",
          "fieldKey": "soldador.apellido",
          "x": 20, "y": 45, "width": 80, "height": 10,
          "style": {
            "fontSize": 15, "fontWeight": "bold",
            "color": "#000000", "textAlign": "left",
            "borderWidth": 0, "backgroundColor": null
          }
        },
        {
          "id": "uuid-2",
          "type": "image",
          "imageType": "qr",
          "x": 160, "y": 5, "width": 25, "height": 25,
          "style": {}
        },
        {
          "id": "uuid-3",
          "type": "variables_block",
          "x": 5, "y": 110, "width": 200, "height": 80,
          "style": { "fontSize": 8, "borderWidth": 1 }
        },
        {
          "id": "uuid-4",
          "type": "results_block",
          "x": 5, "y": 200, "width": 200, "height": 50,
          "style": { "fontSize": 8 }
        }
      ]
    }
  ]
}
```

---

## Fases de implementación

### Fase 1 — Backend: modelo y CRUD (1–2 días)

**Archivos:**
- `database/migrations/..._create_cert_layouts_table.php`
- `app/Models/CertLayout.php`
  - `norma_ids` cast a array
  - Scope `forNorma(Norma $norma)` — busca por norma_id en array, luego parent, luego default
- `app/Http/Controllers/Api/CertLayoutController.php`
  - CRUD estándar + `preview(CertLayout, Certificado)` → devuelve PDF como base64
- `app/Services/PdfFromLayoutService.php` — ver Fase 5
- Ruta nueva en `api.php`:
  ```php
  Route::apiResource('cert-layouts', CertLayoutController::class);
  Route::post('cert-layouts/{layout}/preview', [CertLayoutController::class, 'preview']);
  ```

**Cambio en `PdfCertificadoService`:**
```php
$layout = CertLayout::forNorma($cert->norma)->first();
if ($layout) {
    return app(PdfFromLayoutService::class)->generate($cert, $layout);
}
// fallback: Blade templates actuales
```

---

### Fase 2 — Angular: routing y lista (0.5 día)

**Estructura:**
```
admin/src/app/plantillas/
├── models/cert-layout.model.ts
├── services/cert-layout.service.ts
├── list/plantillas-list.component.ts + .html
├── designer/
│   ├── cert-layout-designer.component.ts + .html
│   ├── canvas/designer-canvas.component.ts
│   ├── palette/block-palette.component.ts
│   ├── properties/properties-panel.component.ts
│   └── utils/
│       ├── layout-serializer.ts   (mmToPx, pxToMm, export, import)
│       └── field-keys.ts          (lista de fieldKeys disponibles + labels)
└── plantillas.module.ts
```

**Rutas Angular:**
```
/plantillas              → PlantillasListComponent
/plantillas/nueva        → CertLayoutDesignerComponent
/plantillas/:id/editar   → CertLayoutDesignerComponent
```

---

### Fase 3 — Canvas con Fabric.js (2–3 días)

**Instalación:**
```bash
npm install fabric@6
npm install @types/fabric --save-dev
```

**Sistema de coordenadas:**
- Todo en mm internamente
- A4 portrait: 210mm × 297mm → canvas 630px × 891px (escala 1mm = 3px)
- A4 landscape: 297mm × 210mm → canvas 891px × 630px
- Conversión: `mmToPx(mm) = mm * 3` / `pxToMm(px) = px / 3`

**Layout del designer:**
```
┌─────────────────────────────────────────────────────────────┐
│  [Guardar]  [Preview PDF]  [Volver]     Nombre: ________    │
│  Normas: [ASME IX ✓] [API 1104 ✓] ...  Orient: [portrait]  │
├─────────────┬───────────────────────────┬───────────────────┤
│  PALETA     │    CANVAS A4 (escalado)   │   PROPIEDADES     │
│             │                           │                   │
│ Estáticos:  │  ┌─────────────────────┐  │  [según tipo]     │
│ ▣ Campo     │  │  bloque  bloque      │  │                  │
│ ▤ Texto     │  │                      │  │  fieldKey:        │
│ 🖼 Imagen   │  │  [variables_block]   │  │  ▾ soldador.ape.. │
│ ─ Línea     │  │                      │  │                  │
│             │  │  [results_block]     │  │  Font: 12pt       │
│ Dinámicos:  │  └─────────────────────┘  │  Bold: [ ]        │
│ ⊞ Variables │  Pág 1  |  Pág 2          │  Color: ■ #000    │
│ ✓ Resultados│                           │  Border: 0px      │
│ ≡ Pasadas   │                           │  Align: L C R     │
│ ◈ Junta     │                           │                   │
└─────────────┴───────────────────────────┴───────────────────┘
```

**Bloques dinámicos en canvas:**
- Se renderizan como rectángulo con ícono + label (placeholder)
- Redimensionables verticalmente para indicar altura estimada
- No editables internamente (el PHP los expande)

---

### Fase 4 — Serialización (0.5 día)

**Export canvas → JSON:**
```typescript
export function exportLayout(canvas: fabric.Canvas, meta: LayoutMeta): CertLayoutJson {
  return {
    pageSize: meta.pageSize,
    orientation: meta.orientation,
    marginMm: meta.marginMm,
    pages: [{
      pageNumber: 1,
      blocks: canvas.getObjects().map(obj => ({
        id:        obj.data.blockId,
        type:      obj.data.type,
        fieldKey:  obj.data.fieldKey ?? null,
        imageType: obj.data.imageType ?? null,
        content:   obj.data.content ?? null,
        x:         pxToMm(obj.left!),
        y:         pxToMm(obj.top!),
        width:     pxToMm(obj.getScaledWidth()),
        height:    pxToMm(obj.getScaledHeight()),
        style:     obj.data.style,
      })),
    }],
  };
}
```

**Import JSON → canvas:**
```typescript
export function loadLayout(layout: CertLayoutJson, canvas: fabric.Canvas): void {
  canvas.clear();
  for (const block of layout.pages[0].blocks) {
    addBlockToCanvas(canvas, block);
  }
}
```

---

### Fase 5 — PHP Renderer (2–3 días)

**`PdfFromLayoutService`** genera HTML con div absolutos:

```php
private function renderBlock(array $block, array $ctx, Certificado $cert): string
{
    $style = $this->buildAbsoluteStyle($block);
    $inner = match($block['type']) {
        'field'            => e(data_get($ctx, $block['fieldKey'] ?? '', '')),
        'text'             => $this->interpolate($block['content'] ?? '', $ctx),
        'image'            => $this->renderImage($block['imageType'], $cert),
        'line'             => $this->renderLine($block),
        'variables_block'  => $this->renderVariablesBlock($cert),
        'results_block'    => $this->renderResultsBlock($cert),
        'passes_block'     => $this->renderPassesBlock($cert),
        'joint_design_block' => $this->renderJointDesignBlock($cert),
        default => '',
    };
    return "<div style=\"{$style}\">{$inner}</div>";
}
```

**`renderVariablesBlock(Certificado $cert)`:**
- Obtiene `campos_norma` del catálogo para `cert.norma_id`
- Itera campos, filtra con lógica `shouldShow` (equivalente PHP del helper Angular)
- Renderiza tabla: label | valor (de `cert->variables[campo]`) | rango calificado

**`renderResultsBlock(Certificado $cert)`:**
- Carga `TestType::where('norma_id', cert->norma_id)->get()`
- Para cada TestType: checkbox (■/□) basado en `cert->tests->firstWhere('test_type_id', id)->resultado`

**`renderPassesBlock(Certificado $cert)`:**
- Carga `cert->passes()->with('proceso')->orderBy('orden')->get()`
- Columnas: etiqueta, proceso, clasificacion_aporte, diametro_aporte_mm, amperaje_min/max, voltaje_min/max, velocidad_avance_min/max (convertir mm/min → cm/min), polaridad, progresion, tipo_transferencia

**`buildDataContext(Certificado $cert)`** — array plano para interpolación:
```php
[
  'cert.numero'             => $cert->numero,
  'cert.codigo'             => $cert->codigo,
  'cert.fecha_calificacion' => $cert->fecha_calificacion?->format('d/m/Y') ?? '',
  'cert.fecha_vencimiento'  => $cert->fecha_vencimiento?->format('d/m/Y') ?? '',
  'cert.tipo'               => $cert->tipo,
  'cert.resultado'          => mb_strtoupper($cert->resultado ?? ''),
  'cert.proceso'            => mb_strtoupper($cert->proceso ?? ''),
  'cert.posicion'           => mb_strtoupper($cert->posicion ?? ''),
  // ... todos los fieldKeys definidos en la tabla de campos
  'soldador.apellido'       => mb_strtoupper($cert->soldador?->apellido ?? ''),
  'soldador.nombre'         => mb_strtoupper($cert->soldador?->nombre ?? ''),
  'soldador.dni'            => $cert->soldador?->dni ?? '',
  'soldador.ciudad'         => $cert->soldador?->ciudad ?? '',
  'empresa.nombre'          => $cert->empresa?->nombre ?? 'PARTICULAR',
  'inspector.nombre'        => $cert->inspector?->nombre ?? '',
  'inspector.certificacion' => $cert->inspector?->certificacion ?? '',
  'norma.nombre'            => $cert->norma?->nombre ?? '',
  'norma.edicion_texto'     => NormaEdicionHelper::text($cert->norma?->nombre ?? ''),
]
```

**HTML generado para mPDF — regla crítica descubierta en testing:**
```html
<html><body>
  <!-- PÁGINA 1: bloques son hijos DIRECTOS de <body>, sin wrapper -->
  <div style="position:absolute;left:10mm;top:10mm;...">RCS1_26</div>
  <div style="position:absolute;left:170mm;top:5mm;..."><img ...></div>

  <!-- SALTO DE PÁGINA: div vacío standalone, no wrapper alrededor del contenido -->
  <div style="page-break-before:always;"></div>

  <!-- PÁGINA 2: más bloques directos -->
  <div style="position:absolute;left:10mm;top:10mm;...">...</div>
</body></html>
```

> ⚠️ **Regla mPDF**: `position:absolute` solo respeta `left` correctamente en hijos directos de `<body>`. Cualquier div wrapper (incluso sin `position:relative`, incluso con `width/height` explícito) rompe el posicionamiento horizontal — todos los bloques colapsan a la izquierda. Probado y confirmado en 2026-06-29. `PdfFromLayoutService::buildHtml()` implementado con este patrón.
> 
> Implicación para el designer Angular: al serializar el canvas Fabric.js a JSON, las coordenadas `x,y` son las coordenadas absolutas de página (no relativas a un contenedor). En la preview del canvas, el viewport A4 se simula con `position:relative` en Angular — eso funciona en browser, no en mPDF.

---

### Fase 6 — Preview y thumbnail (1 día)

**Preview:**
- Botón "Preview PDF" → `POST /api/cert-layouts/{id}/preview`
- Payload: `{ "certificado_id": X }` (o usa cert demo si no se pasa)
- Respuesta: `{ "pdf": "data:application/pdf;base64,..." }`
- Angular abre en `<iframe>` modal

**Thumbnail:**
- Al guardar: `canvas.toDataURL('image/png', 0.5)` → campo `thumbnail`
- Lista de plantillas muestra thumbnail como preview

---

### Fase 7 — Seeds de los templates actuales + NAG (1 día)

**Seeder `CertLayoutSeeder`** crea 3 layouts iniciales:

| Nombre | norma_ids | Equivalente actual |
|---|---|---|
| `RCS ASME/AWS Estándar` | [ASME IX, AWS D1.1, AWS D1.6, API 650, ASME B31.3, B31.8, IRAM] | `rcs-aws-asme.blade.php` |
| `RCS API 1104` | [API 1104] | `rcs-api1104.blade.php` |
| `RCS NAG 105` | [NAG Cat.A, Cat.B, Cat.C, Cat.D] | heredado de ASME + campos_norma NAG |

Cada seed construye el JSON de bloques replicando el layout actual de los Blade templates.

---

### Fase 8 — Fix de discrepancias detectadas (0.5 día)

Durante el audit se detectaron inconsistencias entre DB y templates actuales:

| # | Problema | Fix |
|---|---|---|
| 1 | `velocidad_avance` en DB: mm/min; template API 1104 muestra cm/min | Convertir en `renderPassesBlock()`: `round(mm/min / 10, 1)` cm/min |
| 2 | Checkbox "Pipe/Caño" en template ASME/AWS usa `!$esCanio` — lógica invertida | Corregir en template Blade Y en `renderPassesBlock()` |
| 3 | `tipo_transferencia` en `certificate_passes` no está en ningún template | Agregar columna en `renderPassesBlock()` |
| 4 | NAG 105 no tiene template propio | Cubierto por Fase 7 |

---

## Resolución de layout por norma

```php
// CertLayout::forNorma(Norma $norma): Builder
public function scopeForNorma(Builder $query, Norma $norma): Builder
{
    return $query->where(function ($q) use ($norma) {
        // 1. Match directo por norma_id
        $q->whereJsonContains('norma_ids', $norma->id)
          // 2. Match por parent (NAG → ASME parent)
          ->orWhen($norma->parent_norma_id, fn($q2) =>
              $q2->whereJsonContains('norma_ids', $norma->parent_norma_id)
          );
    })->orWhere('es_default', true)
      ->orderByRaw("CASE WHEN norma_ids IS NOT NULL THEN 0 ELSE 1 END");
}
```

---

## Resumen de archivos

### Backend (Laravel) — nuevo/modificado

| Acción | Archivo |
|---|---|
| CREAR | `database/migrations/..._create_cert_layouts_table.php` |
| CREAR | `app/Models/CertLayout.php` |
| CREAR | `app/Http/Controllers/Api/CertLayoutController.php` |
| CREAR | `app/Services/PdfFromLayoutService.php` |
| CREAR | `app/Helpers/NormaEdicionHelper.php` (extrae lógica de edición de normas del Blade) |
| CREAR | `database/seeders/CertLayoutSeeder.php` |
| MODIFICAR | `app/Services/PdfCertificadoService.php` (lookup de layout antes del Blade fallback) |
| MODIFICAR | `routes/api.php` (agregar cert-layouts) |
| MODIFICAR | `database/seeders/DatabaseSeeder.php` (agregar CertLayoutSeeder) |
| BUGFIX | `resources/views/pdf/rcs-aws-asme.blade.php` (checkbox Pipe/Caño invertido) |

### Frontend Angular — nuevo

| Archivo | Rol |
|---|---|
| `app/plantillas/models/cert-layout.model.ts` | tipos TS |
| `app/plantillas/services/cert-layout.service.ts` | HTTP calls |
| `app/plantillas/list/plantillas-list.component.{ts,html}` | lista |
| `app/plantillas/designer/cert-layout-designer.component.{ts,html}` | shell |
| `app/plantillas/designer/canvas/designer-canvas.component.ts` | Fabric.js |
| `app/plantillas/designer/palette/block-palette.component.ts` | paleta drag |
| `app/plantillas/designer/properties/properties-panel.component.ts` | panel props |
| `app/plantillas/designer/utils/layout-serializer.ts` | export/import |
| `app/plantillas/designer/utils/field-keys.ts` | catálogo de fieldKeys |
| `app/plantillas/plantillas.module.ts` | NgModule |

### Modificar existente

| Archivo | Cambio |
|---|---|
| `app/app-routing.module.ts` | agregar rutas /plantillas |
| `app/shared/components/nav/` | agregar link Plantillas |

---

## Dependencias nuevas

```bash
# Angular admin
npm install fabric@6
npm install @types/fabric --save-dev

# Laravel — nada nuevo (mPDF, endroid/qr-code ya instalados)
```

---

## Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
|---|---|---|
| Gap visual canvas ↔ mPDF | Alto | Preview instantáneo como feedback loop; validar calibración desde Fase 1 |
| Bloques dinámicos con altura variable rompen layout | Alto | En el canvas el usuario define una altura *mínima*; PHP puede excederla (como en web normal) |
| `position:absolute` en mPDF inestable | Medio | Probar con página A4 simple en Fase 5 antes de construir la UI |
| NAG 105 `campos_norma` desconocidos | Medio | El bloque `variables_block` los consume automáticamente del catálogo |
| Norma sin layout configurado | Bajo | Fallback a Blade template garantizado |

---

## Orden de ejecución

1. **Fase 8** (bugfixes) — limpiar deuda antes de construir
2. **Fase 1** (backend CRUD) — tener API funcional
3. **Fase 5** (PHP renderer) — **validar mPDF + position:absolute TEMPRANO** — si no funciona bien, cambiar estrategia antes de construir toda la UI
4. **Fase 2** (routing + lista)
5. **Fase 3** (canvas Fabric.js)
6. **Fase 4** (serialización)
7. **Fase 6** (preview)
8. **Fase 7** (seeds de templates actuales)

> **Importante**: Fase 5 adelantada al inicio para validar la hipótesis de rendering mPDF. Si position:absolute no funciona bien → replantear a table-based rendering antes de invertir en la UI.

Tiempo estimado: **2.5–3 semanas** (1 dev, incluyendo Fase 8 y la complejidad extra de 8 normas).
