# Sistema de Plantillas de Certificados — kocert

## Qué es

Motor para diseñar y renderizar certificados de soldadores (RCS) en PDF.
Reemplaza los Blade templates hardcodeados por layouts configurables visualmente.

## Stack

- **Backend**: Laravel 11, PHP 8.3, mPDF para renderizado
- **Frontend**: Angular 17+ (NgModule, no standalone), Fabric.js v6 para el canvas

---

## Modelo de datos

### `cert_layouts` (tabla)

| columna | tipo | descripción |
|---|---|---|
| `nombre` | string | Nombre visible del template |
| `descripcion` | string\|null | Descripción opcional |
| `norma_ids` | json (int[]) | IDs de normas que usan este layout |
| `es_default` | boolean | Fallback si no hay layout para la norma |
| `orientacion` | string | `'portrait'` \| `'landscape'` |
| `blocks` | json | Estructura completa (ver formato abajo) |
| `thumbnail` | text\|null | PNG data URI del canvas (miniatura) |

### Lookup de layout para un certificado

`CertLayout::scopeForNorma(Builder, Norma)` en `app/Models/CertLayout.php`:

1. Busca layout con `norma_id` de la norma del cert
2. Si no hay, busca layout con `parent_norma_id` de la norma
3. Si no hay, usa el layout con `es_default = true`
4. Prioriza norma directa sobre default vía `ORDER BY CASE WHEN norma_ids IS NOT NULL THEN 0 ELSE 1 END`

Normas actuales:

| id | nombre | parent |
|---|---|---|
| 1 | ASME IX | null |
| 2 | AWS D1.1 | null |
| 3 | AWS D1.6 | null |
| 4 | API 1104 | null |
| 5 | API 650 | null |
| 6 | ASME B31.3 | 1 (ASME IX) |
| 7 | ASME B31.8 | 1 (ASME IX) |
| 8 | IRAM | null |
| 9 | NAG 105 Cat. A | 1 (ASME IX) |
| 10 | NAG 105 Cat. B | 1 (ASME IX) |
| 11 | NAG 105 Cat. C | 4 (API 1104) |
| 12 | NAG 105 Cat. D | null |

---

## Formato del JSON de blocks

```json
{
  "pageSize": "A4",
  "orientation": "portrait",
  "marginMm": { "top": 0, "right": 0, "bottom": 0, "left": 0 },
  "pages": [
    {
      "pageNumber": 1,
      "blocks": [ /* array de LayoutBlock */ ]
    }
  ]
}
```

### LayoutBlock — campos comunes

```typescript
{
  "id": "uuid",          // crypto.randomUUID()
  "type": "...",         // ver tipos abajo
  "x": 5,               // mm desde borde izquierdo del papel
  "y": 10,              // mm desde borde superior del papel
  "width": 100,          // mm
  "height": 20,          // mm
  "style": {
    "fontSize": 10,      // pt
    "fontWeight": "normal" | "bold",
    "color": "#000000",
    "textAlign": "left" | "center" | "right",
    "backgroundColor": "#ffffff",
    "borderWidth": 0,
    "borderColor": "#cccccc"
  }
}
```

### Tipos de bloque

#### Estáticos (renderizados directamente en el HTML/PDF)

| type | campos extra | descripción |
|---|---|---|
| `field` | `fieldKey: string` | Valor de un campo del cert. Soporta interpolación via `fieldKey` |
| `text` | `content: string` | Texto fijo con interpolación `{{campo}}` |
| `image` | `imageType: 'logo'\|'norma_logo'\|'photo'\|'signature'\|'qr'` | Imagen del sistema |
| `line` | `direction: 'horizontal'\|'vertical'` | Línea separadora |

#### Dinámicos (bloques opacos — posición y tamaño solo)

| type | descripción |
|---|---|
| `variables_block` | Tabla de variables de soldadura del cert (desde `cert.variables` JSON) |
| `results_block` | Tabla de ensayos (VT, BT-F, BT-R, BT-S, RT, UT) con checkboxes ■/□ |
| `passes_block` | Tabla de pasadas con proceso, electrodo, amperaje, voltaje, velocidad |
| `joint_design_block` | Diagrama ASCII del diseño de junta |

---

## Field keys disponibles (`fieldKey` en bloques tipo `field`)

### Certificado
| key | valor |
|---|---|
| `cert.codigo` | RCS1_26 |
| `cert.proceso` | SMAW |
| `cert.posicion` | 6G |
| `cert.eps_numero` | EPS-2026-001 |
| `cert.fecha_calificacion` | 15/01/2026 |
| `cert.fecha_vencimiento` | 15/01/2028 |
| `cert.resultado` | APROBADO / REPROBADO |
| `cert.observaciones` | texto libre |

### Soldador
| key | valor |
|---|---|
| `soldador.apellido` | GONZÁLEZ |
| `soldador.nombre` | Carlos |
| `soldador.dni` | 28541032 |
| `soldador.ciudad` | Rosario |
| `soldador.provincia` | Santa Fe |

### Empresa
| key | valor |
|---|---|
| `empresa.nombre` | Petroquímica San Lorenzo S.A. |

### Inspector
| key | valor |
|---|---|
| `inspector.nombre` | Ricardo Kosik |
| `inspector.certificacion` | IRAM-IAS Nivel II - Cert. 4542 |

### Norma
| key | valor |
|---|---|
| `norma.nombre` | ASME IX |
| `norma.edicion_texto` | ASME Boiler and Pressure Vessel Code... |

---

## Interpolación en bloques `text`

```
"content": "DNI: {{soldador.dni}} — {{soldador.ciudad}}"
```

Los `{{key}}` se reemplazan con los mismos field keys de arriba.
El renderer usa `$ctx[$key]` (array plano, no anidado — `data_get()` no funciona aquí).

---

## API REST

Base: `http://127.0.0.1:8000/api` | Auth: Bearer token (Sanctum)

| método | endpoint | descripción |
|---|---|---|
| GET | `/cert-layouts` | Lista todos los layouts (sin blocks ni thumbnail) |
| GET | `/cert-layouts/{id}` | Layout completo con blocks |
| POST | `/cert-layouts` | Crear layout |
| PATCH | `/cert-layouts/{id}` | Actualizar layout |
| DELETE | `/cert-layouts/{id}` | Eliminar (204) |
| POST | `/cert-layouts/{id}/preview` | Generar PDF preview |

### POST /cert-layouts/{id}/preview

Body (opcional):
```json
{ "certificado_id": 1 }
```

Si no se pasa `certificado_id`, usa el cert más reciente no-draft.

Respuesta:
```json
{ "pdf": "data:application/pdf;base64,..." }
```

---

## Servicios clave (backend)

### `PdfFromLayoutService` (`app/Services/PdfFromLayoutService.php`)

```php
$svc = app(PdfFromLayoutService::class);

// Genera PDF y guarda en disco, retorna ruta
$path = $svc->generate($cert, $layout);

// Genera mPDF object (para stream/preview)
$mpdf = $svc->stream($cert, $layout);
```

**Gotcha crítico — mPDF:**
`position:absolute` solo funciona en hijos **directos** de `<body>`.
Cualquier `<div>` wrapper (aunque no tenga `position:relative`) rompe el `left`.
Los blocks se renderizan sin wrapper, como hijos directos del body.

**Multi-página:**
```html
<div style="page-break-before:always;"></div>
```
Como elemento **hermano** de los blocks, no dentro de ningún container.

### `PdfCertificadoService` (`app/Services/PdfCertificadoService.php`)

Punto de entrada original. Ahora hace lookup de layout primero:

```php
$cert->loadMissing(['norma.parent']);
if ($cert->norma && $layout = CertLayout::forNorma($cert->norma)->first()) {
    return app(PdfFromLayoutService::class)->generate($cert, $layout);
}
// fallback: Blade templates hardcodeados
```

---

## Frontend Angular

**Rutas:**
```
/plantillas              → PlantillasListComponent   (lista de templates)
/plantillas/nueva        → CertLayoutDesignerComponent (crear)
/plantillas/:id/editar   → CertLayoutDesignerComponent (editar)
```

**Módulo lazy:** `admin/src/app/plantillas/plantillas.module.ts`

### Designer — estructura de componentes

```
CertLayoutDesignerComponent       ← shell principal (toolbar + layout)
├── BlockPaletteComponent         ← lista de tipos arrastrables (izquierda, 180px)
├── DesignerCanvasComponent       ← canvas Fabric.js (centro, flex)
└── PropertiesPanelComponent      ← propiedades del bloque seleccionado (derecha, 220px)
```

### Canvas — coordenadas

```typescript
const MM_TO_PX = 3;   // 1mm = 3px
// A4 portrait = 630×891px en canvas
// A4 landscape = 891×630px en canvas
```

Grilla de 5mm. Snap a 0.5mm al soltar/exportar.

### Drag & drop

1. `BlockPaletteComponent.onDragStart()` — serializa defaults del bloque a `application/json` en DataTransfer
2. `DesignerCanvasComponent.onDrop()` — parsea JSON, calcula posición relativa al canvas, snap a mm, agrega al canvas

### Exportar layout

```typescript
// En CertLayoutDesignerComponent
const { blocks, thumbnail } = this.canvasRef.export({
  nombre: this.nombre,
  orientacion: this.orientacion,
  normaIds: this.normaIds,
});
```

`exportLayout()` en `layout-serializer.ts` itera los objetos Fabric, lee `obj.data` (LayoutBlock guardado en la propiedad `.data` de cada objeto), y recalcula x/y/w/h desde la posición actual en canvas a mm.

### Trampas Fabric.js v6

- Los objetos de tipo `Group` no tienen `sendObjectsToBack` (plural) — usar `sendObjectToBack` en loop
- Eventos de selección: `(e: any)` en lugar de tipos específicos (type mismatch en v6)
- Resize de Group no actualiza los hijos — cuando cambia size vía properties panel, se elimina el objeto y se recrea con `blockToFabric()`
- `fabric.TTextAlign` no existe — usar `as any`

---

## Seeders

`database/seeders/CertLayoutSeeder.php` — 3 layouts base:

| nombre | norma_ids | es_default |
|---|---|---|
| RCS ASME/AWS/API 650/IRAM | [1,2,3,5,6,7,8] | true |
| RCS API 1104 | [4] | false |
| RCS NAG 105 | [9,10,11,12] | false |

Re-ejecutar es idempotente: elimina por nombre antes de insertar.

```bash
php artisan db:seed --class=CertLayoutSeeder
```

---

## Archivos clave

```
api/
├── app/
│   ├── Models/CertLayout.php
│   ├── Http/Controllers/Api/CertLayoutController.php
│   └── Services/
│       ├── PdfFromLayoutService.php    ← renderer mPDF
│       └── PdfCertificadoService.php   ← punto de entrada (con lookup de layout)
├── database/
│   ├── migrations/2026_06_29_224632_create_cert_layouts_table.php
│   └── seeders/CertLayoutSeeder.php
└── routes/api.php

admin/src/app/plantillas/
├── models/cert-layout.model.ts
├── services/cert-layout.service.ts
├── list/
│   ├── plantillas-list.component.ts
│   └── plantillas-list.component.html
├── designer/
│   ├── cert-layout-designer.component.ts
│   ├── cert-layout-designer.component.html
│   ├── canvas/designer-canvas.component.ts
│   ├── palette/block-palette.component.ts
│   ├── properties/
│   │   ├── properties-panel.component.ts
│   │   └── properties-panel.component.html
│   └── utils/
│       ├── layout-serializer.ts        ← mm↔px, blockToFabric(), exportLayout(), loadLayout()
│       └── field-keys.ts              ← 28 field keys con label y grupo
└── plantillas.module.ts
```

---

## Tests

```bash
cd api && php artisan test
# 88/89 pass — 1 falla pre-existente: F-Number ASME seeder (QW-433)
```

El fallo pre-existente está en `TestsFeatureCatalogoCompatibilidadTest::test_asme_f4_califica_grupos_inferiores`.
No es regresión del trabajo de plantillas.

---

## Plan: Replicar estilo real de certificados en el GUI designer

Referencia completa de layouts reales: `info/RCS_LAYOUT_REFERENCE.md`

### Contexto

Los PDFs reales tienen 5 familias. El objetivo es F1 (KOSOLDAR QR 2025) como base de todos los
seeders. Las familias F2/F3/F4/F5 son variantes históricas; solo difieren en algunas filas de
variables y en la tabla de p2.

El sistema actual tiene 3 seeders abstractos. Necesitamos reemplazarlos con layouts que repliquen
el aspecto exacto de los PDFs reales.

---

### Paso 1 — Agregar field keys faltantes

**Archivo:** `admin/src/app/plantillas/designer/utils/field-keys.ts`

Agregar estos keys al array (con label y grupo apropiados):

```typescript
// Grupo: "Soldador"
{ key: 'soldador.cuño',       label: 'Cuño / Stamp',         grupo: 'Soldador' },

// Grupo: "Certificado"
{ key: 'cert.pqr_numero',           label: 'Nº PQR/RCP',              grupo: 'Certificado' },
{ key: 'cert.tipo_ensayo',          label: 'Tipo ensayo (cupón/prod)', grupo: 'Certificado' },
{ key: 'cert.metal_base',           label: 'Metal base (spec)',        grupo: 'Certificado' },
{ key: 'cert.diametro_espesor',     label: 'Diámetro / Espesor cupón', grupo: 'Certificado' },
{ key: 'cert.califica_rangos',      label: 'Rangos calificados',       grupo: 'Certificado' },
{ key: 'cert.progresion',           label: 'Progresión soldadura',     grupo: 'Certificado' },
{ key: 'cert.respaldo',             label: 'Tipo de respaldo',         grupo: 'Certificado' },
{ key: 'cert.metal_base_pnumber',   label: 'P-Number metal base',      grupo: 'Certificado' },
{ key: 'cert.tipo_gas',             label: 'Tipo de gas / Caudal',     grupo: 'Certificado' },
{ key: 'cert.tipo_corriente',       label: 'Tipo corriente/polaridad', grupo: 'Certificado' },
{ key: 'cert.num_pasadas',          label: 'Número de pasadas',        grupo: 'Certificado' },
{ key: 'cert.tiempo_entre_pasadas', label: 'Tiempo entre pasadas',     grupo: 'Certificado' },
{ key: 'cert.precalentamiento',     label: 'Precalentamiento (°C)',    grupo: 'Certificado' },

// Grupo: "Empresa"
{ key: 'empresa.ciudad', label: 'Ciudad empresa', grupo: 'Empresa' },

// Grupo: "Obra" (solo API 1104)
{ key: 'obra.nombre',    label: 'Obra / Proyecto',  grupo: 'Obra' },
{ key: 'cliente.nombre', label: 'Cliente',           grupo: 'Obra' },

// Grupo: "Norma"
{ key: 'norma.subtitulo', label: 'Subtítulo norma (ej: LINEA REGULAR)', grupo: 'Norma' },
{ key: 'norma.edicion',   label: 'Edición (año corto)',                  grupo: 'Norma' },
```

**Backend:** Agregar estos keys al `$ctx` en `PdfFromLayoutService.php` leyendo los campos
correspondientes del modelo `Certificado` (y relaciones). Los que no existan como columna deben
mapearse desde `cert->variables` JSON o dejarse como string vacío con fallback.

---

### Paso 2 — Diseñar los 3 layouts en el seeder

Reemplazar `CertLayoutSeeder.php` con layouts que reproduzcan F1.
Cada layout = JSON completo de blocks con coordenadas reales en mm.

**Layout 1: RCS ASME/AWS/IRAM (default)** — norma_ids: [1,2,3,5,6,7,8], es_default: true
**Layout 2: RCS API 1104** — norma_ids: [4,11]
**Layout 3: RCS NAG 105** — norma_ids: [9,10,12]

#### Estructura de blocks para Layout 1 (página 1)

```json
{
  "pageSize": "A4",
  "orientation": "portrait",
  "marginMm": { "top": 0, "right": 0, "bottom": 0, "left": 0 },
  "pages": [
    {
      "pageNumber": 1,
      "blocks": [
        // --- HEADER ---
        { "type": "image",  "imageType": "logo",       "x": 5,   "y": 3,  "w": 45, "h": 14 },
        { "type": "text",   "content": "RICARDO KOSIK\nMóvil 3873 651982\nricardokosik@gmail.com",
          "x": 5, "y": 17, "w": 45, "h": 15, "style": { "fontSize": 7 } },
        { "type": "image",  "imageType": "norma_logo", "x": 78,  "y": 3,  "w": 30, "h": 12 },
        { "type": "text",   "content": "{{norma.nombre}} {{norma.edicion}}",
          "x": 65, "y": 16, "w": 80, "h": 5, "style": { "fontSize": 8, "textAlign": "center" } },
        { "type": "text",   "content": "REGISTRO DE CALIFICACION DE SOLDADORES\n(RCS)\nWELDER PERFOMANCE QUQLIFICATIONS (WPQ)",
          "x": 58, "y": 21, "w": 94, "h": 13, "style": { "fontSize": 9, "fontWeight": "bold", "textAlign": "center" } },
        { "type": "image",  "imageType": "qr",         "x": 162, "y": 3,  "w": 18, "h": 18 },
        { "type": "text",   "content": "RCS / WPQ\nNº {{cert.codigo}}",
          "x": 158, "y": 21, "w": 47, "h": 12, "style": { "fontSize": 9, "fontWeight": "bold", "textAlign": "center", "borderWidth": 1 } },
        { "type": "line",   "direction": "horizontal", "x": 5,   "y": 35, "w": 200, "h": 1 },

        // --- PROCESO ---
        { "type": "text",   "content": "Welding process(es): Type\nProceso(s) de Soldadura: Tipo",
          "x": 5, "y": 37, "w": 95, "h": 9, "style": { "fontSize": 7 } },
        { "type": "field",  "fieldKey": "cert.proceso",
          "x": 100, "y": 37, "w": 55, "h": 9, "style": { "fontSize": 14, "fontWeight": "bold", "textAlign": "center" } },
        { "type": "text",   "content": "MANUAL",
          "x": 155, "y": 37, "w": 50, "h": 9, "style": { "fontSize": 14, "fontWeight": "bold", "textAlign": "center" } },
        { "type": "line",   "direction": "horizontal", "x": 5, "y": 46, "w": 200, "h": 1 },

        // --- SOLDADOR ---
        { "type": "text",   "content": "Welder's name-\nNombre del Soldador\nStamp / CUÑO",
          "x": 5, "y": 47, "w": 95, "h": 10 },
        { "type": "field",  "fieldKey": "soldador.apellido",
          "x": 5, "y": 57, "w": 103, "h": 9, "style": { "fontSize": 20, "fontWeight": "bold" } },
        { "type": "field",  "fieldKey": "soldador.nombre",
          "x": 5, "y": 66, "w": 103, "h": 8, "style": { "fontSize": 16, "fontWeight": "bold" } },
        { "type": "image",  "imageType": "photo", "x": 111, "y": 47, "w": 48, "h": 55 },

        { "type": "text",   "content": "Company: EMPRESA:",
          "x": 5, "y": 75, "w": 35, "h": 7, "style": { "fontSize": 7 } },
        { "type": "field",  "fieldKey": "empresa.nombre",
          "x": 40, "y": 75, "w": 69, "h": 7, "style": { "fontSize": 12, "fontWeight": "bold" } },

        { "type": "text",   "content": "Identification - Nº documento :",
          "x": 5, "y": 83, "w": 60, "h": 7, "style": { "fontSize": 7 } },
        { "type": "field",  "fieldKey": "soldador.dni",
          "x": 65, "y": 83, "w": 44, "h": 7, "style": { "fontSize": 11, "fontWeight": "bold" } },

        { "type": "text",   "content": "Identification of WPS followed:\nIdentificación de EPS Aplicado:",
          "x": 5, "y": 91, "w": 60, "h": 9, "style": { "fontSize": 7 } },
        { "type": "field",  "fieldKey": "cert.eps_numero",
          "x": 65, "y": 91, "w": 44, "h": 9, "style": { "fontSize": 10, "fontWeight": "bold" } },

        { "type": "text",   "content": "Qualification Date\nFecha de Calificación",
          "x": 5, "y": 101, "w": 60, "h": 8, "style": { "fontSize": 7 } },
        { "type": "field",  "fieldKey": "cert.fecha_calificacion",
          "x": 65, "y": 101, "w": 44, "h": 8, "style": { "fontSize": 10, "fontWeight": "bold" } },

        { "type": "text",   "content": "Expiration date\nFECHA DE VENCIMIENTO",
          "x": 5, "y": 110, "w": 60, "h": 8, "style": { "fontSize": 7, "fontWeight": "bold" } },
        { "type": "field",  "fieldKey": "cert.fecha_vencimiento",
          "x": 65, "y": 110, "w": 44, "h": 10, "style": { "fontSize": 16, "fontWeight": "bold", "color": "#CC0000" } },

        { "type": "text",   "content": "Extension/Renewal of the RCS / WPQ — Ampliación/ Renovación del RCS / WPQ",
          "x": 5, "y": 121, "w": 200, "h": 6, "style": { "fontSize": 7, "textAlign": "center" } },
        { "type": "line",   "direction": "horizontal", "x": 5, "y": 127, "w": 200, "h": 1 },

        // --- CUPÓN / METAL BASE ---
        { "type": "text",
          "content": "Test coupon / Cupón de prueba  ☑      Production Weld / Soldadura de producción  ☐",
          "x": 5, "y": 129, "w": 85, "h": 7, "style": { "fontSize": 7 } },
        { "type": "text",   "content": "Specification of base metal(s)\nEspecificación del Metal/es) Base:",
          "x": 90, "y": 129, "w": 60, "h": 7, "style": { "fontSize": 7 } },
        { "type": "field",  "fieldKey": "cert.metal_base",
          "x": 150, "y": 129, "w": 55, "h": 7, "style": { "fontSize": 11, "fontWeight": "bold" } },

        { "type": "text",   "content": "Pipe/ Caño ☑    Sheet / Chapa ☐",
          "x": 5, "y": 137, "w": 85, "h": 7, "style": { "fontSize": 7 } },
        { "type": "text",   "content": "Diameter / Thickness\nEspesor:",
          "x": 90, "y": 137, "w": 30, "h": 7, "style": { "fontSize": 7 } },
        { "type": "field",  "fieldKey": "cert.diametro_espesor",
          "x": 120, "y": 137, "w": 40, "h": 7, "style": { "fontSize": 9, "fontWeight": "bold" } },
        { "type": "field",  "fieldKey": "cert.califica_rangos",
          "x": 160, "y": 137, "w": 45, "h": 7, "style": { "fontSize": 9, "fontWeight": "bold" } },
        { "type": "line",   "direction": "horizontal", "x": 5, "y": 144, "w": 200, "h": 1 },

        // --- TABLA VARIABLES ---
        { "type": "variables_block", "x": 5, "y": 145, "w": 200, "h": 120 },

        // --- FOOTER P1 ---
        { "type": "text",
          "content": "We certify that the statements in this record are correct and that the test coupons were prepared, welded, and tested in accordance with the requirements of {{norma.edicion_texto}}\nNosotros certificamos que las declaraciones en este registro son correctas y que los cupones de la prueba fueron preparados, soldados y ensayados de acuerdo a los requisitos de {{norma.edicion_texto}}",
          "x": 5, "y": 268, "w": 200, "h": 12, "style": { "fontSize": 6 } },
        { "type": "text",   "content": "VERIFICAR LA AUTENTICIDAD DE ESTE DOCUMENTO ESCANEANDO EL CODIGO QR",
          "x": 5, "y": 281, "w": 185, "h": 6, "style": { "fontSize": 7, "fontWeight": "bold", "textAlign": "center" } },
        { "type": "text",   "content": "Página 1 de 2",
          "x": 170, "y": 287, "w": 35, "h": 5, "style": { "fontSize": 7, "textAlign": "right" } }
      ]
    },
    {
      "pageNumber": 2,
      "blocks": [
        // header idéntico a p1 (mismos blocks repetidos)
        // ...

        // --- PASADAS ---
        { "type": "passes_block", "x": 5, "y": 36, "w": 200, "h": 50 },

        // --- RESULTS ---
        { "type": "results_block", "x": 5, "y": 90, "w": 200, "h": 95 },

        // --- FIRMA / QR ---
        { "type": "image",  "imageType": "signature",  "x": 65,  "y": 190, "w": 40, "h": 18 },
        { "type": "text",   "content": "RICARDO KOSIK\nINSPECTOR IRAM -IAS NIVEL II - CERT. 4542",
          "x": 55, "y": 209, "w": 100, "h": 10, "style": { "fontSize": 8, "fontWeight": "bold", "textAlign": "center" } },
        { "type": "text",   "content": "Firma y Documento CODIFICADOS - Ley 25.506 régimen internacional de reconocimiento de documentos con firma digital y firma electrónica",
          "x": 20, "y": 220, "w": 170, "h": 7, "style": { "fontSize": 7, "color": "#CC6600", "textAlign": "center" } },
        { "type": "image",  "imageType": "qr",         "x": 157, "y": 188, "w": 45, "h": 45 },

        // footer p2 igual a p1 pero "Página 2 de 2"
        { "type": "text", "content": "Página 2 de 2",
          "x": 170, "y": 287, "w": 35, "h": 5, "style": { "fontSize": 7, "textAlign": "right" } }
      ]
    }
  ]
}
```

---

### Paso 3 — Diferencias Layout 2 (API 1104)

Cambios respecto a Layout 1:

1. **Header center**: agregar `field: norma.subtitulo` (bold, grande) debajo del logo norma
2. **Fila extra en datos soldador**: añadir bloque `text` con 3 cols "Company | Client | Work"
   - `field: empresa.nombre`, `field: cliente.nombre`, `field: obra.nombre`
3. **variables_block**: el bloque dinámico necesita modo API 1104 (3 cols, filas distintas)
   — ver sección "Variantes de variables_block" abajo
4. **Tabla CARACTERISTICAS ELECTRICAS**: bloque tipo `passes_block` va en **p1** (no p2)
5. **results_block p2**: modo API (VT / Gammagrafiado / Plegado / Nick Break + tabla detalle)

---

### Paso 4 — Variantes de bloques dinámicos requeridas

#### `variables_block` — 2 variantes

| Variante | Columnas | Filas extra |
|---|---|---|
| `asme_aws` | 4 (Testing / Rango / Comentarios) | Tungsteno GTAW, Gas tipo/backing |
| `api_1104` | 3 (Condiciones / Comentarios, sin Rango) | Num pasadas, Tiempo entre pasadas, Precalentamiento |

Propuesta: agregar campo `variant: 'asme_aws' | 'api_1104'` al block de tipo `variables_block`.

#### `results_block` — 2 variantes

| Variante | Contenido extra |
|---|---|
| `asme_aws` | Lista ASME/AWS completa (QW refs) |
| `api_1104` | Nick Break, tabla Plegado/Nick detallada |

Propuesta: agregar `variant: 'asme_aws' | 'api_1104'` al block.

---

### Paso 5 — Cargar los layouts en el GUI y ajustar

1. Correr `php artisan db:seed --class=CertLayoutSeeder` con los nuevos layouts
2. Abrir `/plantillas/1/editar` en el GUI
3. Verificar visualmente que el canvas refleja la distribución real
4. Ajustar coordenadas que no coincidan (sobre todo el bloque foto y variables_block height)
5. Usar `POST /cert-layouts/1/preview` con un cert real para comparar PDF output vs PDF ejemplo

---

### Paso 6 — Verificar campos en PdfFromLayoutService

Revisar `$ctx` en `app/Services/PdfFromLayoutService.php`:
- Agregar todos los keys nuevos del Paso 1
- Para `cert.tipo_corriente`, `cert.respaldo`, etc.: leer de `cert->variables` JSON con `data_get()`
  o de la relación correspondiente
- Para `obra.nombre` y `cliente.nombre`: verificar si existe relación en modelo `Certificado`

---

### Orden de ejecución recomendado

```
1. field-keys.ts         → agregar 18 keys nuevos (frontend solo, sin riesgo)
2. PdfFromLayoutService  → mapear los keys nuevos al $ctx (backend)
3. CertLayoutSeeder      → reemplazar con Layout 1 (ASME/AWS) con coordenadas reales
4. Test preview          → php artisan db:seed && POST /cert-layouts/1/preview
5. Ajuste fino GUI       → abrir designer, mover bloques hasta calzar
6. Layout 2 API 1104     → repetir pasos 3-5
7. Layout 3 NAG 105      → igual que Layout 1 (NAG Cat A/B usan ASME IX base)
```

---

### Riesgos y gotchas

- **mPDF position:absolute**: los `variables_block`/`passes_block`/`results_block` son HTML
  independiente — si el bloque dinámico tiene más contenido que `h` declarado, el PDF se rompe.
  Calcular `h` con margen o usar `overflow: visible` en el renderer.
- **Foto DNI**: si el soldador no tiene foto, el bloque `image: photo` debe renderizar un
  placeholder vacío, no romper el layout.
- **variables_block con variant**: cambio breaking — el renderer actual no conoce `variant`.
  Implementar con fallback a `asme_aws` si no se provee.
- **Tabla 6.10 AWS D1.1**: es una imagen estática de la norma. Puede ir como `image` block
  con `imageType: 'norma_tabla'` (nuevo tipo) o como `text` block con la tabla en HTML.
- **Header repetido en p2**: los blocks del header se duplican manualmente en cada página del
  JSON. El GUI designer ya soporta multi-página; asegurarse de que al agregar p2 se puedan
  copiar-pegar los blocks del header de p1.
