# KOCERT — Arquitectura Técnica Completa

> Generado: 2026-07-02

---

## Stack General

| Capa | Tecnología | Versión |
|------|-----------|---------|
| Backend API | Laravel | 13.8 |
| Runtime PHP | PHP | 8.3+ |
| Auth | Laravel Sanctum | 4.3 |
| PDF | mPDF | 8.3 |
| QR | endroid/qr-code | 6.1 |
| Frontend Admin | Angular | 21.2.0 |
| UI Framework | Bootstrap | 5.3.8 |
| UI Components | ng-bootstrap | 20.0.0 |
| Canvas/Designer | Fabric.js | 6.9.1 |
| Testing | Vitest | 4.0.8 |
| DB default | SQLite / MySQL | — |

---

## Estructura de Directorios

```
kocert/
├── api/                  Laravel REST API
├── admin/                Angular — portal administrativo (principal)
├── app/                  Angular — portal público (sin desarrollar)
├── ejemplos/             Documentación de referencia y esquemas
├── md/                   Documentación organizada del proyecto
└── graphify-out/         Grafo de conocimiento del codebase
```

---

## Backend Laravel (`api/`)

### Rutas API (`routes/api.php`)

**Públicas (sin auth):**
- `POST /login`
- `GET /verificar/{token}` — verificación QR pública

**Protegidas (Sanctum token):**
- `POST /logout`, `GET /me`
- `GET /catalogos/norma/{id}`, `/posiciones`, `/normas`, `/procesos`, `/next-numero`, `/check-numero`, `/joint-designs`
- CRUD `/soldadores` + `POST /{id}/foto`
- CRUD `/empresas`
- CRUD `/procesos`
- CRUD `/normas`
- CRUD `/materiales`
- CRUD `/certificados` + `POST /renovar`, `POST /ampliar`, `POST /recalcular`, `GET /{id}/pdf`
- CRUD `/inspectores` + `POST /{id}/firma`
- CRUD `/cert-layouts` + `POST /{id}/preview`, `POST /preview-block`
- CRUD `/usuarios`
- `GET /dashboard`

### Controllers (13)

| Controller | Responsabilidad |
|-----------|----------------|
| AuthController | Login / logout / me |
| CatalogoController | Datos para selects/dropdowns |
| CertificadoController | CRUD + renovar / ampliar / recalcular / PDF |
| CertLayoutController | Templates + preview |
| DashboardController | Estadísticas |
| EmpresaController | Empresas CRUD |
| InspectorController | Inspectores + firma upload |
| MaterialController | Materiales CRUD |
| NormaController | Normas CRUD |
| ProcesoController | Procesos CRUD |
| SoldadorController | Soldadores CRUD + foto upload |
| UserController | Usuarios CRUD |
| VerificacionController | Verificación QR pública |

### Modelos (22)

```
Soldador
├── nombre, apellido, dni, fecha_nacimiento, nacionalidad, cuño, foto_path, activo
└── hasMany: Certificado, WelderEvent

Certificado (dominio central)
├── numero, anio, revision, tipo [inicial|renovacion|ampliacion]
├── resultado [aprobado|rechazado]
├── estado [vigente|vencido|suspendido|borrador]
├── fecha_calificacion, fecha_vencimiento
├── proceso, posicion, progresion, tipo_cupon [chapa|caño]
├── variables (JSON: espesor_cupon, diametro_cupon, num_pasadas)
├── eps_numero, pqr_numero, joint_design_id, joint_detail
├── pdf_path, qr_token (unique), ranges_calculated_at, rules_version
├── FKs: soldador_id, empresa_id, proceso_id, norma_id, material_id, usuario_id, inspector_id
└── hasMany: CertificatePasse, CertificateTest, CertificateRange

Norma
├── parent_norma_id (jerarquía padre-hijo)
└── hasMany: Certificado, GrupoBaseMetal, GrupoConsumible

Empresa          — nombre, cuit, contacto, activo
Proceso          — nombre, descripcion
Posicion         — codigo, descripcion, tipo, es_tuberia, califica_ranura[], califica_filete[]
Material         — nombre, descripcion

GrupoBaseMetal   — norma_id, codigo, descripcion, orden
GrupoConsumible  — norma_id, codigo, descripcion, orden
Consumible       — clasificacion, sfa, proceso, descripcion

CertLayout
├── nombre, descripcion, norma_ids (JSON), es_default
├── orientacion [portrait|landscape]
├── blocks (JSON: { pages[{ blocks[] }] })
└── thumbnail (data URI PNG)

CertificatePasse
├── certificado_id, orden, etiqueta, proceso_id
├── clasificacion_aporte, diametro_aporte_mm, polaridad
└── amperaje/voltaje/velocidad_avance [min/max], tipo_transferencia, progresion

CertificateTest  — certificado_id, test_type_id, resultado, notas, detalles (JSON)
TestType         — norma_id, code, nombre, requerido
CertificateRange — certificado_id, type [espesor|diametro], descripcion, min_value, max_value

ThicknessRule    — norma_id, coupon_type, thickness_from/to_mm, min_layers, qualifies_min/max_formula
DiameterRule     — norma_id, coupon_type, diameter_from/to_mm, qualifies_min/max_formula
QualifiedPosition — norma_id, tested_posicion_id, qualified_posicion_id, joint_type
JointDesign      — code, nombre, svg, parametros (JSON)

Inspector        — nombre, certificacion, firma_path, email, telefono, activo
WelderEvent      — soldador_id, event_type, event_date, notas, created_by (audit trail)
User             — name, email, password, activo (Sanctum auth)
```

### Services

| Service | Función |
|---------|---------|
| RangeCalculatorService | Calcula rangos de espesor/diámetro calificados según norma |
| PdfCertificadoService | Genera PDF con mPDF (QR + logos + firmas + layout clásico) |
| PdfFromLayoutService | Genera PDF desde CertLayout designer (nuevo pipeline) |
| CertificateTestSyncService | Sincroniza resultados de tests con rangos calculados |

### Migraciones (40 total)

Tablas principales en orden cronológico:
1. users, cache, jobs, personal_access_tokens
2. soldadores, empresas, procesos, normas, materiales, certificados
3. posiciones, grupos_base_metal, grupos_consumible, consumibles
4. thickness_rules, diameter_rules, qualified_positions
5. test_types, certificate_tests, certificate_passes, certificate_ranges
6. joint_designs, welder_events
7. cert_layouts (2026-06-29)
8. Campos NAG (2026-06-25)
9. Draft support (2026-06-21)
10. Inspector integration (2026-06-23)

### CI/CD (GitHub Actions)

- `tests.yml` — PHPUnit
- `pull-requests.yml` — PR checks
- `issues.yml` — Issue automation
- `update-changelog.yml` — Release notes

---

## Frontend Admin Angular (`admin/`)

### Módulos y Features

| Módulo | Componentes | Servicios |
|--------|------------|----------|
| auth | login-form | auth.service |
| certificados | certificados-list, certificado-form | certificado.service, certificado-form-data.service |
| soldadores | soldadores-list, soldador-form | soldador.service |
| empresas | empresas-list, empresa-form | empresa.service |
| inspectores | inspectores-list, inspector-form | inspector.service |
| usuarios | usuarios-list, usuario-form | usuario.service |
| plantillas | plantillas-list + designer completo | cert-layout.service |
| dashboard | stats component | — |

### Layout Designer (Fabric.js)

```
plantillas/designer/
├── cert-layout-designer     — componente raíz
├── designer-canvas          — canvas Fabric.js
├── block-palette            — panel izquierdo, bloques arrastrables
├── properties-panel         — panel derecho, propiedades del bloque seleccionado
└── block-editors/
    ├── cabecera-editor      — bloque encabezado
    ├── cabecera-palette
    ├── junta-editor         — bloque diseño de junta
    ├── pasadas-editor       — bloque pasadas de soldadura
    ├── resultados-editor    — bloque resultados de ensayos
    └── variables-editor     — bloque variables del certificado
```

Layout se serializa a JSON (`CertLayout.blocks`) y se persiste en DB.

### Shared

- Services: `confirm`, `notification`, `toast`, `theme`, `catalogo`
- Components: `confirm-dialog`, `data-review-modal`, `pagination`, `searchable-select`, `sort-header`, `toast`
- Base: `base-paged-list.component`
- Guards: `auth.guard`
- HTTP interceptor (token injection)

---

## Frontend App Angular (`app/`)

Angular 21.2 — scaffolding mínimo sin rutas ni features. Rol: portal público para soldadores (sin desarrollar).

---

## Flujo de Negocio — Ciclo del Certificado

```
1. CREAR/BORRADOR
   Soldador + Empresa + Norma + Proceso + Material + variables (espesor, diámetro, pasadas)

2. CALCULAR RANGOS
   RangeCalculatorService → ThicknessRule + DiameterRule por norma → CertificateRange[]

3. REGISTRAR PASADAS
   CertificatePasse[] — parámetros por pasada (amperaje, voltaje, velocidad, etc.)

4. REGISTRAR ENSAYOS
   CertificateTest[] — resultado por TestType requerido según norma

5. GENERAR PDF
   PdfCertificadoService (clásico) OR PdfFromLayoutService (CertLayout designer)
   → mPDF + QR único (qr_token)

6. VERIFICACIÓN PÚBLICA
   GET /verificar/{qr_token} — endpoint sin auth para escaneo QR

7. RENOVAR / AMPLIAR
   POST /renovar | POST /ampliar → nuevo Certificado vinculado al original

8. RECALCULAR
   POST /recalcular → re-aplica ThicknessRule/DiameterRule (útil al actualizar norma)
```

---

## Dominio de Normas

- Jerarquía padre-hijo en `normas` (ej: ASME IX → variantes)
- Cada norma define:
  - `GrupoBaseMetal` + `GrupoConsumible` (calificación de materiales)
  - `Posicion` con mapeo de calificación (`QualifiedPosition`)
  - `ThicknessRule` + `DiameterRule` (reglas de rangos)
  - `TestType[]` (ensayos requeridos)
- Campos NAG integrados (migración 2026-06-25)

---

## Autenticación y Seguridad

- Sanctum tokens (stateless API)
- Angular `auth.guard` + HTTP interceptor (Bearer token)
- `User.activo` flag para deshabilitar usuarios
- `BCRYPT_ROUNDS=12`
- Único endpoint público: `GET /api/verificar/{token}`
