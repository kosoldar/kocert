# Problemas detectados — KoCert (graphify 2026-06-24)

Grafo: 918 nodos, 1419 edges, 133 comunidades. Fuente: `graphify-out/graph.json`.

---

## P1 — Crítico

### 1. God Component: `CertificadoFormComponent` (56 edges)
**Archivo:** `admin/src/app/certificados/form/certificado-form.component.ts`  
**Betweenness:** 0.045 — cruza 8 comunidades distintas.  
**Problema:** un componente hace todo:
- Carga datos de 3 entidades externas (`SoldadorService`, `EmpresaService`, `InspectorService`)
- Maneja lógica del wizard (`stepStatus`, `nextStep`, `revealStepIssues`, `cycleVencimiento`)
- Construye formulario dinámico por norma (`buildVariablesGroup`, `updateConditionalValidators`, `shouldShow`)
- Valida (`numeroAsyncValidator`, `applyServerErrors`)
- Construye payload y abre modal (`buildPayload`, `openReview`)

**Solución aplicada:**
| Archivo | Contenido |
|---|---|
| `certificado-form-data.service.ts` | todos los HTTP calls (5 deps sacados del component) |
| `certificado-form.helpers.ts` | funciones puras: `shouldShow`, `getCatalogOptions`, `buildCertPayload`, `buildDraftPayload`, `deriveResultado`, constantes |
| `certificado-form.component.ts` | wizard + reactive subscriptions + save flow: **703 → 380 líneas** |

---

### ~~2. Dos servicios de notificación duplicados~~ — DESCARTADO
**Veredicto:** no son duplicados. Responsabilidades distintas:
- `ToastService` → mensajes efímeros de UI
- `NotificationService` → polling de notificaciones del servidor (campana admin)
`NotificationService` depende de `ToastService` intencionalmente. Sin acción requerida.

---

## P2 — Importante

### 3. Duplicación masiva de List Components
**Archivos:**
- `admin/src/app/empresas/list/empresas-list.component.ts`
- `admin/src/app/inspectores/list/inspectores-list.component.ts`
- `admin/src/app/usuarios/list/usuarios-list.component.ts`
- `admin/src/app/soldadores/list/soldadores-list.component.ts`

**Problema:** graphify detectó similarity semántica 1.0 entre todos sus templates. Misma estructura: `SortHeader + Pagination + ConfirmDialog + CRUD`. ~75% código duplicado.  
**Solución:** `GenericEntityListComponent<T>` con `@Input() config` o clase base abstracta.

---

### ~~4. Duplicación en Form Components~~ — DESCARTADO
**Veredicto:** flujos distintos (Inspector guarda directo, Soldador usa review modal antes de guardar). Solo ~30% de lógica compartida. Abstracción no justificada. Sin acción.

---

### 5. `api/routes/api.php` aislado
**Archivo:** `api/routes/api.php`  
**Problema:** solo 1 conexión en el grafo. El AST no pudo enlazar `Route::apiResource()` con los controllers PHP. El grafo no puede auditar cobertura de endpoints.  
**Solución:** documentar rutas con PHPDoc/anotaciones, o agregar tests que validen cada endpoint.

---

## P3 — Deuda técnica / arquitectural

### 6. Comunidades con cohesión crítica (< 0.12)
| Comunidad | Cohesión | Nodos | Problema |
|---|---|---|---|
| API HTTP Controllers (C0) | 0.08 | 16 | 7+ controllers sin estructura entre ellos |
| User Management UI (C2) | 0.08 | 11 | Mezcla environments, DTOs, components |
| Certificados List CRUD (C3) | 0.08 | 9 | Enums (`CertEstado`, `CertTipo`) mezclados con components |

**Solución aplicada:** `meta` inline en los 4 model files unificada con `PageMeta` de `pagination.component.ts`. La baja cohesión del grafo es artefacto de topología — los enums en sus model files de dominio es práctica estándar.

---

### 7. Frontend público (`app/src`) casi sin implementar
**Directorio:** `app/src/` — solo 9 archivos, `AppModule` sin conexiones en el grafo.  
**Problema:** el admin (`admin/src`) tiene 79 archivos y funcionalidad completa. El app público parece un shell vacío.  
**Acción:** definir si es intencional o si hay features pendientes del frontend público.

---

## Confirmado sin problema

- **Cero ciclos de importación** — arquitectura limpia.
- **`ValidaCombinacionesNorma`** — cohesión 1.0, validación bien encapsulada.
- **Flujo de creación de certificado** — cadena clara: `CertificadoFormComponent` → `CertificadoService` → `CertificadoController` → `StoreCertificadoRequest` → `Certificado` + modelos relacionados.
