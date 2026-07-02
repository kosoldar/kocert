---
name: TODO Generación de Certificados PDF
description: Plan de implementación para generación automática de RCS/WPQ en KoCert. Derivado del análisis de 58 PDFs de ejemplo y auditoría del código/DB existente.
type: project
---

# TODO: Generación de Certificados PDF (RCS/WPQ)

---

## Análisis — Resultados

> Análisis basado en 58 PDFs en `info/ejemplos/`, auditoría de migraciones, modelos, servicios y archivos de referencia en `ejemplos/`.

### Tipos de certificados encontrados

| # | Norma | Alcance | Estado |
|---|---|---|---|
| 1 | **AWS D1.1** Ed 2020 | Soldadura estructural acero al carbono y baja aleación | ✅ Activo — mayoría del volumen |
| 2 | **ASME IX** Ed 2023 | Recipientes a presión, intercambiadores, piping de proceso | ✅ Activo |
| 3 | **API 1104** Ed 2022 | Gasoductos / oleoductos onshore | ✅ Activo — formato completamente distinto |
| 4 | **AWS D1.3** Ed 2018 | Chapa delgada (sheet steel) | ⚠️ Sembrado en DB, sin ejemplos en los 58 PDFs |

**Sub-tipo transaccional** (corta los 3 normas): `inicial` / `renovacion` / `ampliacion`  
Se indica en el campo "Ampliación/Renovación del RCS/WPQ" del encabezado (checkbox).

---

### Patrones que definen cada tipo

#### AWS D1.1 vs ASME IX — mismo layout, diferencias puntuales

Ambos usan grilla bilingüe de 4 columnas (Variables de Soldadura | Condiciones de Ensayo | Rangos Calificados | Comentarios), 2 páginas A4.

| Elemento | AWS D1.1 | ASME IX |
|---|---|---|
| Logo central | Logo AWS D1.1 | Logo ASME |
| Encabezado tabla variables | Sin código de referencia | "(QW-350)" |
| Referencias en resultados pág 2 | Sin paréntesis | `QW-302.4`, `QW-462.3(a)`, `QW-191`, `QW-180`, `QW-183` |
| Rango posiciones calificadas | "VER TABLA 6.10" + tabla adjunta en pág 2 | Texto directo ("TODA POSICION - ASCENDENTE") |
| Rango P-Number | Solo "P1 Gr 1" en condición ensayo | "P-No.1 through P-No.15F P-No.34 and P-No.41 through P-No.49" en rangos |
| Texto certificación pie | "...AWS D1.1 Ed 2020" | "...Section IX of the ASME Code. Ed 2023" |

#### API 1104 — estructura completamente distinta

**Página 1:**
- Proceso (SMAW/GTAW) mostrado en tamaño grande como título secundario
- Layout campo:valor lineal (sin grilla de tabla)
- Incluye código PQR además de EPS (los otros solo EPS)
- Ciudad del soldador/empresa visible
- Metal base: especificación tensil completa con ksi y MPa (ej: "≥ API 5L X42 Minimum Specified Tensile, Psi 60.200 (415 MPa)")
- Tiempos entre pasadas (1ª→2ª, 2ª→restantes)
- Precalentamiento + temperatura máx entre pasadas en misma línea
- Fecha calificación + vencimiento destacados en rojo

**Página 2:**
- Tabla **VARIABLES DE SOLDADURA** pass-by-pass (columnas: Secuencia | Proceso | Clasif. AWS | Diámetro mm | Amperaje A | Voltaje V | Avance cm/min | Polaridad | Progresión)
- Foto DNI del soldador (abajo centro, grande)
- Test **Nick Break** explícito y obligatorio (las otras normas no lo tienen)

---

### Suficiencia de datos

#### Disponible en DB actual ✅

| Dato visible en PDFs | Origen en DB |
|---|---|
| Nombre, apellido, DNI, cuño, ciudad | `soldadores` |
| Empresa | `empresas` via FK |
| Norma, edición | `normas` |
| Número RCS, año, revisión, tipo transacción | `certificados` |
| Código EPS / PQR | `certificados.eps_numero`, `pqr_numero` |
| Proceso, posición, progresión, tipo cupón | `certificados` (columnas indexadas) |
| Respaldo, P-number/grupo, F-number, electrodo, corriente, gas | `certificados.variables` (JSONB) |
| Diseño de junta (SVG) + detalle junta | `joint_designs.svg` via `joint_design_id` |
| Espesor y diámetro ensayado | `variables.espesor_cupon`, `variables.diametro_cupon` |
| Fechas calificación / vencimiento | `certificados.fecha_calificacion`, `fecha_vencimiento` |
| Rangos calificados (espesor, diámetro, posiciones) | `certificate_ranges` — calculados por `RangeCalculatorService` |
| Resultados ensayos (VT, Plegado, RT, Nick Break, Macro) | `certificate_tests` |
| Pasadas pass-by-pass (API 1104) | `certificate_passes` |
| Token QR verificación | `certificados.qr_token` (UUID generado en `store()`) |
| Ruta PDF almacenado | `certificados.pdf_path` |

#### Faltante — brechas críticas ❌

| Dato visible en PDFs | Problema | Impacto |
|---|---|---|
| Inspector (nombre, cert. IRAM-IAS, firma imagen) | No existe tabla `inspectors` ni `inspector_id` en `certificados` en ninguna migración actual | 🔴 Aparece en TODOS los certificados |
| Foto DNI del soldador | No existe `foto_path` en `soldadores` | 🔴 Aparece en TODOS los certificados |
| Diseño de junta en PDF | `joint_design_id` + `joint_detail` están en `$fillable` del modelo pero **ausentes en la migración** `recreate_certificados_table` — confirmar si existe la columna en DB real | 🔴 El SVG de junta es elemento visual clave |
| Librería PDF | `barryvdh/laravel-dompdf` no instalada; `CertificadoController::pdf()` devuelve 501 | 🔴 Sin esto no hay generación |
| Librería QR | `endroid/qr-code` no instalada; `qr_token` existe pero no se genera imagen | 🟡 El QR impreso es parte del certificado |
| Pasadas API 1104 en formulario | `certificate_passes` tiene estructura pero no hay UI/request para cargarlas | 🟡 Solo afecta API 1104 |

#### Suficiencia estimada por norma

| Norma | Datos disponibles | Brecha principal |
|---|---|---|
| AWS D1.1 | ~85% | Inspector + foto soldador + joint_design en migración |
| ASME IX | ~85% | Idem |
| API 1104 | ~80% | Idem + formulario pasadas pass-by-pass |

---

## Estado de brechas críticas

| Brecha | Impacto | Estado |
|---|---|---|
| No existe tabla `inspectors` ni FK en `certificados` | 🔴 Crítico | Pendiente |
| No existe `foto_path` en `soldadores` | 🔴 Crítico | Pendiente |
| `joint_design_id` + `joint_detail` ausentes en migración `certificados` | 🔴 Crítico | Pendiente |
| Librería PDF no instalada (`mpdf/mpdf`) | 🔴 Crítico | Pendiente |
| Librería QR no instalada (`endroid/qr-code`) | 🟡 Medio | Pendiente |
| `certificate_passes` sin UI/request para cargarse | 🟡 Medio | Pendiente |
| `CertificadoController::pdf()` devuelve 501 | 🔴 Crítico | Pendiente |

---

## Fase 1 — Cerrar brechas de datos

- [ ] **Migration:** crear tabla `inspectors`
  ```
  id, nombre, certificacion (string, ej: "IRAM-IAS Nivel II - Cert. 4542"),
  firma_path (string, nullable), email, telefono, activo (bool, default true),
  timestamps
  ```
- [ ] **Migration:** agregar `inspector_id` FK a `certificados` (nullable, constrained inspectors)
- [ ] **Migration:** agregar `foto_path` (string, nullable) a `soldadores`
- [ ] **Verificar en DB real:** ¿existen columnas `joint_design_id` y `joint_detail` en `certificados`?
  Si no → migration que las agrega (FK nullable a `joint_designs`, string 200 nullable)
- [ ] **Model:** agregar relación `inspector()` a `Certificado`
- [ ] **Model:** agregar `inspector_id`, `foto_path` a `$fillable` correspondientes
- [ ] **withRelations()** en `CertificadoController`: agregar `inspector`, `jointDesign`, `tests`, `passes`
- [ ] **Seeder:** cargar al menos un inspector (Ricardo Kosik, IRAM-IAS Nivel II, Cert. 4542)

---

## Fase 2 — Infraestructura PDF

- [ ] `composer require mpdf/mpdf`
- [ ] `composer require endroid/qr-code`
- [ ] Crear `config/mpdf.php` o configurar inline: paper A4, modo portrait, fuente DejaVu/Arial
- [ ] Guardar logos en `public/images/logos/`: `aws-d11.png`, `asme.png`, `api.png`, `kosoldar.png`
- [ ] Crear directorio storage: `storage/app/pdfs/` (agregar a `.gitignore` el contenido, no el dir)

> **Por qué mPDF sobre DomPDF/FPDF:**
> - SVG nativo — crítico para `joint_designs.svg`
> - HTML + CSS como input — layouts mantenibles vs coordenadas X/Y manuales
> - Acepta rutas de archivo para imágenes (sin base64 obligatorio)
> - Activamente mantenido (FPDF sin updates desde 2011, DomPDF SVG parcial/buggy)

---

## Fase 3 — Templates Blade

### Template 1: AWS D1.1 / ASME IX (compartido)
- [ ] Crear `resources/views/pdf/rcs-aws-asme.blade.php`
- [ ] Layout: 2 páginas A4, fuente Arial/Helvetica, CSS inline (DomPDF no soporta externos)
- [ ] **Página 1:**
  - Header: logo norma (centro), logo Kosoldar + datos inspector (izquierda), QR (derecha)
  - Bloque identificación: nombre soldador, cuño, empresa, DNI + foto DNI, EPS, fechas
  - Bloque cupón: tipo (caño/chapa checkbox), espesor, rangos calificados
  - Tabla variables 4 columnas bilingüe: tipo proceso, respaldo, P-number, electrodo, F-number,
    tungsteno, diseño junta (SVG embebido), espesor depositado, posición/progresión, gas, corriente
  - Texto certificación bilingüe al pie + "VERIFICAR AUTENTICIDAD"
- [ ] **Página 2:**
  - Header idéntico pág 1
  - Sección RESULTS/RESULTADOS: checkboxes VT, Bend (raíz/cara/lateral), RT, Filete, Macro
  - **Si norma = AWS D1.1:** adjuntar Table 6.10 (hardcoded, es tabla normativa fija)
  - **Si norma = ASME IX:** mostrar referencias QW-xxx en paréntesis, sin Table 6.10
  - Notas estándar (custodia 72hs, cliente no solicitó presenciar)
  - "SOLDADOR APROBADO / APPROVED WELDER" condicional en resultado
  - Firma inspector + nombre + certificación (Ley 25.506)
- [ ] Condicional `@if($norma === 'ASME IX')` para refs QW-xxx vs D1.1
- [ ] Condicional `tipo_cupon` para mostrar diámetro solo si es caño

### Template 2: API 1104
- [ ] Crear `resources/views/pdf/rcs-api1104.blade.php`
- [ ] Layout completamente diferente — NO grilla 4-col
- [ ] **Página 1:**
  - Header: logo API + "API STANDARD 1104 Ed 2022" (centro), logo Kosoldar + inspector (izq), QR (der)
  - Proceso prominente (SMAW / GTAW) en tamaño grande
  - Número RCS top right
  - Bloque soldador: nombre, cuño, DNI, empresa, ciudad — layout horizontal simple
  - Campo cupón: checkbox caño/chapa, calidad metal base
  - Bloques campo:valor lineales:
    - Materiales base calificados (DE/A con especificaciones tensil/yield en ksi y MPa)
    - Diámetro calificado
    - Espesor de cañería
    - Diseño de junta
    - Material de aporte (raíz / relleno / terminación con clasificaciones)
    - Características eléctricas: "Ver hoja 2"
    - Posición + progresión
    - Número de soldadores
    - Tiempos entre pasadas
    - Limpieza
    - Precalentamiento + temperatura entre pasadas
    - Gas protector
    - Presentador (EXTERNO / INTERNO)
  - Fecha calificación + fecha vencimiento destacados (color rojo en ejemplo)
  - Texto certificación + "VERIFICAR AUTENTICIDAD"
- [ ] **Página 2:**
  - Header idéntico
  - Tabla **VARIABLES DE SOLDADURA** (desde `certificate_passes`):
    columnas: Secuencia, Proceso, Clasif. AWS, Diámetro [mm], Amperaje [A], Voltaje [V], Avance [cm/min], Polaridad, Progresión
  - Sección RESULTS/RESULTADOS: VT, Plegado + Transversal raíz, Nick Break (obligatorio)
  - Notas estándar
  - "SOLDADOR APROBADO / APPROVED WELDER"
  - Foto DNI soldador (grande, centro)
  - Firma inspector + nombre + certificación
  - Fecha calificación + vencimiento al pie (repetida)

---

## Fase 4 — Servicio PDF

- [ ] Crear `app/Services/PdfCertificadoService.php`
- [ ] Método principal:
  ```php
  public function generate(Certificado $cert): string
  // Retorna path relativo al archivo PDF guardado
  ```
- [ ] Lógica de selección de template por norma:
  ```
  ASME IX, ASME B31.3, ASME B31.8  →  rcs-aws-asme (con flag $normaCode)
  AWS D1.1, AWS D1.3               →  rcs-aws-asme (con flag $normaCode)
  API 1104                         →  rcs-api1104
  ```
- [ ] Generación QR como PNG base64 inline (apunta a `/api/verificar/{qr_token}`)
- [ ] Nombre de archivo: `RCS{numero}_{anio}-{apellido}-{dni}-{proceso}-{norma}-{empresa}-{fecha}-REV{revision}.pdf`
- [ ] Guardar en `storage/app/pdfs/` y actualizar `certificados.pdf_path`

---

## Fase 5 — Controller + routes

- [ ] Reemplazar body de `CertificadoController::pdf()` (actual 501) con:
  ```php
  $pdf = $this->pdfService->generate($certificado);
  return response()->download(storage_path("app/{$pdf}"));
  // o: return response()->file(...) para visualizar en browser
  ```
- [ ] Inyectar `PdfCertificadoService` en constructor de `CertificadoController`
- [ ] Auto-generar PDF en `store()` tras `$this->rangeCalc->calculate()`
- [ ] Auto-generar PDF en `renovar()` y `ampliar()` también
- [ ] Agregar `StoreCertificadoRequest` fields: `inspector_id` (required, exists:inspectors)
- [ ] Agregar `UpdateCertificadoRequest` campo `inspector_id`

---

## Fase 6 — Verificación QR

- [ ] Revisar `VerificacionController::show()`: ¿carga inspector + soldador + empresa + ranges?
- [ ] Si no → agregar relaciones faltantes a la query
- [ ] Test manual: generar RCS de prueba, escanear QR, verificar respuesta JSON pública

---

## Fase 7 — UI Angular (después del backend)

- [ ] Formulario de inspectores (CRUD básico): nombre, certificación, firma (upload)
- [ ] Campo upload foto DNI en formulario de soldadores
- [ ] Botón "Generar PDF" en detalle de certificado (llama GET `/api/certificados/{id}/pdf`)
- [ ] Tabla pass-by-pass (API 1104): agregar sección en formulario de certificado cuando norma = API 1104

---

## Notas de implementación

**mPDF y SVG:** `joint_designs.svg` se embebe directamente en el HTML pasado a mPDF — soporte SVG nativo. Testear con los SVGs más complejos de `joint_designs` antes de dar por resuelto.

**Table 6.10 (AWS D1.1):** Es la tabla normativa de posiciones calificadas. Puede ir hardcoded en el template como HTML table o como imagen PNG del estándar.

**Foto DNI:** Los PDFs de ejemplo muestran la imagen del documento de identidad argentino (RENAPER). Almacenar como JPEG/PNG en `storage/app/soldadores/{id}/foto.jpg`. En el PDF se embebe como base64.

**Firma inspector:** Almacenar como PNG con fondo transparente. Embeber en base64 en el PDF.

**Tipografía:** Los ejemplos usan Arial/Helvetica. DomPDF incluye DejaVu por defecto — suficiente para el layout. Para mayor fidelidad usar `mpdf` (mejor soporte tipográfico) si DomPDF falla en reproducir el layout.

**Norma API 1104 Ed:** Los ejemplos muestran "Ed 2022" en el template pero "ASME IX Ed 2023" en el texto de certificación. La edición viene del campo `normas.edicion` en DB.

---

## Orden de ejecución recomendado

```
1  → Fase 1 completa (datos — sin esto no hay contenido)
2  → Fase 2 instalación librerías
3  → Fase 4 PdfCertificadoService (esqueleto, sin template todavía)
4  → Fase 3 Template AWS D1.1/ASME IX (cubre ~80% del volumen real)
5  → Fase 5 wiring controller
6  → Fase 6 verificación QR
7  → Fase 3 Template API 1104
8  → Fase 7 UI Angular
```
