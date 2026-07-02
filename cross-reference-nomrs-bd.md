# Informe de Cross-Reference: glosarioNormas.md vs. Schema + Seeders

## Lo que está bien ✅

El esquema está sólidamente construido en su núcleo:

- **Posiciones:** 12 códigos correctos (1G→6G + 1F→5F); `QualifiedPositionSeeder` mapea QW-461.9 (ASME), §6.2.2(f) (API) y Tabla 6.10 (AWS) fielmente.
- **F-Numbers / WF-Numbers:** Los 6 F-Nos de ASME IX (F-1 a F-6) y los 6 WF de API 1104 están presentes con los consumibles más importantes.
- **Reglas de espesor:** API 1104 usa fórmulas proporcionales (Ed.22ª), ASME IX tiene las dos reglas de QW-452.1(b), AWS D1.1 cubre los rangos de Tabla 6.11.
- **Reglas de diámetro:** ASME IX QW-452.3 y API §6.2.2(d) con los 3 grupos y umbrales correctos (60.3/323.9 mm).
- **Test types:** Nick-break de API presente; RT como alternativo a bend en ASME/AWS; MT/PT para AWS D1.1. Correcto.
- **Materiales base:** P-No.1 (Gr.1/2/3) con API 5L A→X52; grupos AWS I/II; grupos API por SMYS. Los más comunes para el mercado gasoducto argentino están cubiertos.
- **Joint designs:** 12 diseños con SVG y parámetros — buen detalle para el PDF del certificado.
- **Multi-pass:** `certificate_passes` con sort_order, polarity, transfer_type, progression — correctamente diseñado.
- **`welder_events`:** La tabla existe con tipos qualification/renewal/activity\_record/suspension/revocation — la arquitectura es la adecuada.

---

## Gaps y Desfasajes

### CRÍTICO — rompen funcionalidad nuclear

#### 1. NAG ausente en su totalidad

Las normas NAG 100, NAG 105 y NAG 201 no existen en ninguna tabla ni seeder. El sistema no puede emitir un certificado NAG, ni validar una categoría, ni calcular vencimientos NAG.

**Lo que falta:**

| Elemento | Estado |
|---|---|
| `standards`: NAG 100, NAG 105, NAG 201 | ❌ no seeded |
| Campo `nag_category` (A/B/C/D) en `certificates` | ❌ sin migración |
| Inactividad 90 días (vs. 180 días ASME/AWS) | ❌ sin representación |
| Vigencia 2 años | ❌ `expiration_date` manual sin lógica por norma |
| Fórmula SMYS Cat.D: `i = (P×D×100)/(2×t×S) < 20%` | ❌ ningún campo |
| Credencial física (Form 513-780-0) — N° credencial | ❌ no hay campo en `welders` |
| Cat.D — límites máximos (OD≤323.8mm, t≤19mm, P≤25 kg/cm²) | ❌ sin validación |

**Gravedad:** Dado que NAG 105 es el marco regulatorio para gasoductos en Argentina (exactamente el mercado objetivo de Kosoldar), su ausencia total es el gap más crítico del sistema.

---

#### 2. No existe tabla de cross-qualification de F-Numbers (QW-433)

El seeder tiene el comentario `// Rule QW-433: F4 qualifies F3, F2, F1` pero no hay tabla en ninguna migración. La nota está en el código, pero no en la base de datos.

**Reglas que deben ser representadas:**

| F probado | Con backing | Sin backing |
|---|---|---|
| F-No. 4 | F-1, F-2, F-3, F-4 | F-4 solamente |
| F-No. 3 | F-1, F-2, F-3 | F-3 solamente |
| F-No. 2 | F-1, F-2 | F-2 solamente |
| F-No. 1 | F-1 | F-1 solamente |
| F-No. 6 | F-6 | F-6 |

Sin esta tabla, el `RangeCalculatorService` no puede determinar qué otros consumibles habilita un certificado — el rango queda incompleto.

---

#### 3. No existe tabla de cross-qualification de P-Numbers (QW-423)

QW-423 define que un soldador calificado en P-No.3 puede soldar P-No.1, pero no viceversa. Esta lógica no tiene representación en ninguna migración. El sistema puede saber en qué material se calificó el soldador, pero no puede calcular qué otros materiales quedan habilitados.

---

#### 4. `welder_events` no tiene `process_id`

ASME IX QW-322.1: la inactividad de 6 meses se aplica **por proceso**. Un soldador inactivo en SMAW mantiene su calificación GTAW. La tabla `welder_events` no tiene FK a `welding_processes`, por lo que el sistema sólo puede auditar inactividad global.

Para NAG, el límite es 90 días — y tampoco hay forma de saber bajo qué norma/proceso aplica el evento.

---

#### 5. `certificate_ranges.type` no cubre grupos de consumibles ni materiales base

El enum actual es `thickness | diameter | position`. El `RangeCalculatorService` necesita producir también:
- `filler_group` — qué F-Numbers habilita el cupón (ASME IX QW-433)
- `base_metal_group` — qué P-Numbers habilita (QW-423)

Sin estos tipos, el rango calculado es estructuralmente incompleto; falta más de la mitad de la información que contiene un certificado real.

---

#### 6. GMAW-S no es un proceso separado

ASME IX QW-410.26: el modo de transferencia de GMAW es una variable esencial. Un soldador calificado con spray/pulse **no** queda habilitado para short-circuit (GMAW-S) y viceversa. `certificate_passes.transfer_type` es un campo string sin restricción de valores. El proceso `GMAW` en `welding_processes` no distingue el modo de transferencia.

**Consecuencia:** el sistema puede registrar GMAW-S en `transfer_type`, pero no puede saber que eso restringe la calificación y no puede validarlo.

---

### ALTO — errores en cálculos o datos faltantes

#### 7. AWS D1.1: sin regla de espesor para T < 9.5 mm (3/8")

`ThicknessRuleSeeder` cubre `9.5–25.4 mm` y `≥25.4 mm`. Tabla 6.11 de AWS D1.1 dice: T < 3/8" → califica únicamente T (sólo el espesor ensayado, sin rango). Si un cupón tiene 6 mm, el sistema no tiene ninguna regla aplicable y el cálculo de rango fallará o producirá un resultado vacío.

---

#### 8. `certificates.process_id`, `.consumable_id` y `.current` son redundantes con `certificate_passes`

Para certificados multi-proceso (raíz GTAW + relleno SMAW), estos tres campos de nivel-certificado no tienen sentido. Son inconsistentes por diseño: el consumible y la polaridad correctos están en `certificate_passes`.

**Huérfanos identificados:**
- `certificates.current` (polarity) — duplicado por `certificate_passes.polarity`
- `certificates.consumable_id` — duplicado por `certificate_passes.filler_classification`
- `certificates.process_id` — ambiguo en multi-proceso

---

#### 9. Falta regla de `inactivity_limit_days` ligada a la norma

No hay forma de que el sistema sepa que ASME/AWS aplica 180 días y NAG 90 días. La lógica de `Certificate::getEffectiveStatusAttribute()` tiene el valor `6 meses` hardcodeado. Cuando se incorpore NAG, ese hardcode romperá los certificados NAG.

---

#### 10. No hay `certificate_id` en `welder_events`

Un `activity_record` mantiene la continuidad de uno o varios certificados específicos. Sin FK a `certificates`, el sistema no puede saber qué certificados se renuevan por un evento de actividad — se pierde trazabilidad fina.

---

#### 11. API 1104 Ed.22ª: sin campo `heat_input`

La Ed.22ª exige consignar el heat input (kJ/mm) en el registro de calificación. No existe campo ni en `certificates` ni en `certificate_passes`.

---

#### 12. Posición 6GR no existe

ASME IX QW-461.9 define la posición 6GR (6G con anillo de restricción) para conexiones de rama. Califica un conjunto distinto de posiciones que el 6G sin anillo. No está en `positions` ni en `qualified_positions`.

---

### MEDIO — datos incompletos en seeders

#### 13. StandardSeeder no incluye NAG ni ISO 9606-1 (IRAM)

`normas_campos_variables.md` contempla IRAM/ISO 9606-1 como norma futura. No hay fila en `standards`. Cuando se incorpore, se necesitan: procesos ISO (111, 131, 135, 141…), posiciones ISO (PA/PB/PC/PF/PG/PH/H-L045), grupos de material ISO/TR 15608 (W01-W61), grupos de consumible ISO (FM1-FM6).

---

#### 14. Consumables con gaps en seeders

| Grupo | Estado |
|---|---|
| ASME IX F-No.1, F-No.2, F-No.5 | ❌ sin `consumables` asociados |
| API 1104 WF-3 (bajo-H vertical-down) | ❌ sin consumables (E8045/9045/10045) |
| API 1104 WF-5 (OFW — RG60/RG65) | ❌ sin consumables |
| AWS D1.1 F1, F2 | ❌ sin consumables (EXX20/24/27/28 y EXX12/13/14) |

---

#### 15. BaseMetalGroupSeeder: AWS Groups III y IV sin materiales base

`base_metal_groups` tiene los 4 grupos AWS D1.1, pero `BaseMaterialSeeder` sólo carga materiales para Groups I y II. Groups III (A514, A517) y IV (A514 ≥63mm) están vacíos.

---

#### 16. P-Numbers de ASME IX incompletos

Sólo P-No.1 (Gr.1/2/3), P-No.3, P-No.4 y P-No.8. Faltan:
- P-No.2 (aceros Mn-V y Ni) — relevante para recipientes a presión
- P-No.5A/5B/5C (Cr-Mo de alta temperatura: 5Cr-0.5Mo, 9Cr-1Mo)
- P-No.6 (SS martensítico), P-No.7 (SS ferrítico)
- P-No.9 (aceros al Ni para baja temperatura)

Estos sólo son prioridad si el sistema va a calificar soldadores en esos materiales (recipientes a presión, refinería).

---

#### 17. `test_types.required` es boolean estático

El campo `required` en `test_types` no puede representar condiciones dinámicas:
- BT-S sólo es requerido cuando t ≥ 9.5 mm (ASME/AWS)
- BT-F en API sólo cuando OD > 323.9 mm (12.75")
- BT-S en API sólo cuando t > 12.7 mm (0.5")

Actualmente todos están seeded con `required=false` para los opcionales, lo que es correcto como default, pero el sistema necesita lógica en el servicio — no en el boolean del seeder.

---

### BAJO / Desfasaje con documento interno

#### 18. Desfasaje entre `normas_campos_variables.md` y el schema real

`normas_campos_variables.md` describe un catálogo unificado `catalogo_norma_items` que **no existe en las migraciones**. El esquema real usa tablas dedicadas (`consumable_groups`, `base_metal_groups`, etc.). Las referencias a `f_numbers`, `grupos_base_metal_aws` en ese documento son nombres lógicos de una arquitectura anterior — si ese documento se usa como spec para la UI, hay inconsistencia con el schema real.

---

## Resumen ejecutivo de hallazgos

| Área | Estado | Severity |
|---|---|---|
| NAG 100/105/201 | ❌ ausente total | CRÍTICO |
| F-Number cross-qual (QW-433) | ❌ sin tabla | CRÍTICO |
| P-Number cross-qual (QW-423) | ❌ sin tabla | CRÍTICO |
| `welder_events` sin `process_id` | ❌ falta FK | CRÍTICO |
| `certificate_ranges` sin filler_group/base_metal_group | ❌ enum incompleto | CRÍTICO |
| GMAW-S como proceso distinto | ❌ no distinguible | CRÍTICO |
| AWS D1.1 sin regla t < 9.5 mm | ❌ crash potencial | ALTO |
| certificates.{process\_id, consumable\_id, current} redundantes | ⚠️ huérfanos multi-proceso | ALTO |
| Sin `inactivity_limit_days` por norma | ❌ hardcoded | ALTO |
| Sin `certificate_id` en welder_events | ⚠️ trazabilidad incompleta | ALTO |
| Sin `heat_input` para API 1104 Ed.22ª | ❌ campo faltante | ALTO |
| Posición 6GR ausente | ❌ no representable | ALTO |
| Consumables incompletos en varios grupos | ⚠️ datos faltantes | MEDIO |
| AWS D1.1 Groups III/IV sin materiales | ⚠️ datos faltantes | MEDIO |
| P-Numbers ASME incompletos (P-2, P-5, P-6...) | ⚠️ scope limitado | MEDIO |
| `test_types.required` estático | ⚠️ lógica debe ir en servicio | MEDIO |
| Desfasaje `normas_campos_variables.md` vs schema real | ⚠️ doc desactualizado | BAJO |

---

## Sugerencias concretas

**1. Incorporar NAG como norma de primera clase**
- Seed `standards`: NAG 105 Cat.C (proceso → API 1104), NAG 105 Cat.D (proceso → EPS N°1 interno), NAG 100/201 como marco regulatorio
- Agregar columna `nag_category ENUM('A','B','C','D') NULLABLE` a `certificates`
- Agregar `credential_number VARCHAR` a `welders` para número de credencial física NAG
- Agregar `inactivity_limit_days SMALLINT` a `standards` (180 para ASME/AWS, 90 para NAG)
- Agregar `validity_years TINYINT NULLABLE` a `standards` (NULL = sin vencimiento fijo, 2 para NAG)

**2. Crear tabla `consumable_group_qualifications`**
```sql
id, standard_id, tested_group_id FK→consumable_groups,
qualifies_group_id FK→consumable_groups,
requires_backing BOOLEAN DEFAULT FALSE
```
Seed con las reglas QW-433. El `RangeCalculatorService` la consulta para calcular rangos de consumibles.

**3. Crear tabla `base_metal_group_qualifications`**
```sql
id, standard_id, tested_group_id FK→base_metal_groups,
qualifies_group_id FK→base_metal_groups
```
Seed con reglas QW-423.

**4. Extender `certificate_ranges.type`**
Agregar valores al enum: `filler_group`, `base_metal_group`. O bien crear tabla `certificate_group_ranges(certificate_id, type, group_id FK→consumable_groups|base_metal_groups)` separada si se prefiere tipado fuerte.

**5. Agregar `process_id` y `certificate_id` a `welder_events`**
```php
$table->foreignId('process_id')->nullable()->constrained('welding_processes')->nullOnDelete();
$table->foreignId('certificate_id')->nullable()->constrained()->nullOnDelete();
```
Esto permite auditar inactividad por proceso y vincular renewals a certificados específicos.

**6. Resolver la ambigüedad de campos multi-proceso en `certificates`**
Opción recomendada: mantener `process_id` y `consumable_id` como "proceso principal / raíz" y documentarlo en código. Agregar `is_multi_process BOOLEAN DEFAULT FALSE`. Deprecar `certificates.current` (polarity) — derivar siempre de `certificate_passes`.

**7. Agregar GMAW-S a `welding_processes`** con código `GMAW-S`. Alternativamente, convertir `certificate_passes.transfer_type` en un enum (`short_circuit | spray | pulse | globular | na`) con una migración ALTER.

**8. Agregar regla de espesor faltante para AWS D1.1 T < 9.5 mm** en `ThicknessRuleSeeder`:
```php
['standard_id' => $aws, 'coupon_type' => 'plate',
 'thickness_from_mm' => 0, 'thickness_to_mm' => 9.5,
 'qualifies_min_mm' => null, 'qualifies_max_formula' => 't',
 'notes' => 'AWS D1.1 Table 6.11: T < 3/8 in. — qualifies tested thickness only']
```

**9. Agregar posición 6GR** a `positions` y sus calificaciones en `qualified_positions` (= todo lo que califica 6G más prueba con anillo de restricción para 5G con restricción).

**10. Agregar `heat_input_kj_mm DECIMAL(8,3) NULLABLE`** a `certificates` o a `certificate_passes` (nivel de pasada).

**11. Completar seeders de consumables vacíos**: WF-3, WF-5 de API; F1/F2 de AWS D1.1; F-No.5 de ASME IX.

**12. Actualizar `normas_campos_variables.md`**: ese documento describe un diseño anterior con `catalogo_norma_items`. Reemplazar las referencias a catálogos genéricos con los nombres reales de tablas del schema actual, o marcarlo como deprecado.

---

## Prioridad de implementación sugerida

| Prioridad | Ítem | Esfuerzo |
|---|---|---|
| 1 | NAG standards + nag_category + inactivity_limit_days + validity_years | Migración + Seeder |
| 2 | `consumable_group_qualifications` + seed QW-433 | Migración + Seeder |
| 3 | `base_metal_group_qualifications` + seed QW-423 | Migración + Seeder |
| 4 | `welder_events`: agregar process_id + certificate_id | Migración |
| 5 | `certificate_ranges.type`: extender enum | Migración |
| 6 | GMAW-S: proceso separado o enum transfer_type | Migración pequeña |
| 7 | ThicknessRuleSeeder: regla AWS T < 9.5mm | Seeder solamente |
| 8 | Posición 6GR + qualified_positions | Seeder |
| 9 | `heat_input_kj_mm` en certificate_passes | Migración |
| 10 | Completar consumables vacíos (WF-3, WF-5, F1, F2, F-No.5) | Seeder |
| 11 | Base materials AWS Groups III/IV | Seeder |
| 12 | `credential_number` en welders | Migración |
