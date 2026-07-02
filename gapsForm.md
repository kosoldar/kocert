# Gaps del Formulario de Certificados — Informe y Plan de Ataque

> Fecha: 2026-06-24  
> Alcance: `admin/src/app/certificados/form/` · `api/app/Http/Controllers/Api/CatalogoController.php` · `api/resources/views/pdf/`  
> Estado: **Solo lectura — sin modificaciones al código**

---

## PARTE 1 — INFORME DE GAPS

### Arquitectura del formulario

3 pasos (wizard):
1. **Solicitante** — soldador, empresa, norma, inspector, tipo, eps_numero, pqr_numero
2. **Variables** — campos dinámicos definidos por `camposXxx()` en CatalogoController; se almacenan en `certificates.variables` (JSON blob)
3. **Fechas** — fecha_calificacion, fecha_vencimiento, observaciones

Cuatro campos del blob suben a top-level al guardar (`TOP_LEVEL` set): `proceso`, `posicion`, `progresion`, `tipo_cupon`.

---

### GAP CRÍTICO #1 — Test results: dos sistemas que no se comunican

**Afecta: todas las normas.**

El formulario captura resultados en:
- `variables.resultado_vt`
- `variables.resultado_bend`
- `variables.resultado_rt`
- `variables.resultado_nick_break`
- `variables.resultado_fractura_filete`
- `variables.resultado_macro`

Los templates PDF leen resultados desde `$cert->tests` (modelo `CertificateTest`, relación `tests.testType`) con códigos `VT`, `BT-R`, `BT-F`, `BT-S`, `RT`, `NB`:

```php
// rcs-aws-asme.blade.php:95
$tr = fn($code) => $cert->tests->first(fn($t) => optional($t->testType)->code === $code)?->resultado ?? '';
```

No existe ningún código que transforme `variables.resultado_vt` → registro en `certificate_tests`.  
**Consecuencia**: todos los checkboxes y resultados de ensayo en el PDF aparecen vacíos.

El top-level `cert->resultado` sí se deriva correctamente via `deriveResultado()` en el helper Angular, pero los detalles no se renderizan.

---

### GAP CRÍTICO #2 — API 1104: tabla pass-by-pass sin UI de ingreso

El template `rcs-api1104.blade.php` renderiza una tabla de pasadas desde `$cert->passes` (modelo `CertificatePasse`), columnas: Secuencia, Proceso, Clasif. AWS, Diámetro[mm], Amperaje, Voltaje, Avance[cm/min], Polaridad, Progresión.

El formulario **no tiene UI para ingresar pasadas**. Los campos `electrodo_raiz`, `corriente_raiz`, `electrodo_relleno`, `corriente_relleno` van al blob `variables`, pero el PDF los lee desde la relación `passes`.

**Consecuencia**: la tabla de variables de soldadura (página 2) siempre dice `(sin pasadas registradas)`.

---

### GAP ALTO #3 — Campos usados en PDF no incluidos en `camposXxx()`

| Campo en PDF | `camposAsme()` | `camposAws()` | `camposApi()` | Impacto |
|---|---|---|---|---|
| `a_number` (comentario F-Number, QW-350) | ❌ | ❌ | N/A | A-Number obligatorio en ASME IX WPQ |
| `espesor_deposito` (calcula max calificado `2t`) | ❌ | ❌ | N/A | Rango de espesor incorrecto en PDF |
| `tipo` (manual / semi-auto) | ❌ | ❌ | ❌ | Celda en blanco en PDF |
| `metal_base` / `especificacion_metal_base` | ❌ | ❌ | ❌ | Especificación material vacía en PDF |
| `junta_calificada` (rango de junta calificada) | ❌ | ❌ | N/A | Columna "Rango Calificado" vacía |
| `grupo_electrodo` (API 1104) — mismatch con `grupo_consumible` | N/A | N/A | ❌ ¹ | Grupo consumible no aparece en PDF |
| `tiempo_p1_p2` / `tiempo_p2_rest` (API 1104) | N/A | N/A | ❌ ² | Tiempos entre pasadas vacíos |
| `limpieza_entre_pasadas` (API 1104) | N/A | N/A | ❌ | Muestra solo default hardcoded |
| `presentador` (API 1104) | N/A | N/A | ❌ | Muestra solo default 'EXTERNO' |

¹ Form captura `grupo_consumible`, template lee `grupo_electrodo`. Nombres distintos → campo siempre vacío en PDF.  
² Form tiene `tiempo_entre_pasadas` (integer) que no mapea a ningún campo del template.

---

### GAP ALTO #4 — `joint_design_id` y `joint_detail` aceptados pero no capturados

`StoreCertificadoRequest` valida `joint_design_id` y `joint_detail`. Los templates PDF renderizan el SVG y detalle de junta si están presentes. El formulario Angular **no tiene controles para estos campos** — `buildBaseForm()` no los incluye y ningún paso los muestra.

---

### GAP MEDIO #5 — GMAW-S: `transfer_type` sin campo

Se agregó check constraint a `certificate_passes.transfer_type`. Variable esencial por QW-410.26 — soldador calificado con spray/pulse NO califica short-circuit. El formulario no tiene campo para `transfer_type`. Con proceso GMAW no es posible registrar la modalidad de transferencia.

---

### GAP MEDIO #6 — NAG 105 sin template propio y sin campos específicos

`PdfCertificadoService::resolveTemplate()` solo diferencia API 1104 vs. todo lo demás. NAG 105 usaría `rcs-aws-asme.blade.php`, que no muestra:
- Categoría NAG (A/B/C/D)
- Número de credencial física (Form 513-780-0)
- Campos `is_multi_process`, `nag_smys_pct` (migrations creados, no en form ni template)

Tampoco hay método `camposNag()` en CatalogoController.

---

### GAP BAJO #7 — `RangeCalculatorService` no calcula rangos filler/base_metal

Migrations extendieron `certificate_ranges.type` con `'filler_group'` y `'base_metal_group'`. `RangeCalculatorService` solo calcula `espesor`, `diametro`, `posicion`. Las tablas `consumable_group_qualifications` y `base_metal_group_qualifications` (nuevas) no se usan en ningún service. Los rangos de consumible y metal base nunca se escriben.

---

### GAP BAJO #8 — Bug en template API 1104: texto de certificación incorrecto

`rcs-api1104.blade.php` líneas 361–364 y 551–555 dicen:
```
"CODE ASME IX- Ed 2023"
```
Debe decir: `"API STANDARD 1104 Ed 2022"`.

---

### Resumen ejecutivo de gaps

| Categoría | # Gaps | Consecuencia directa |
|---|---|---|
| CRÍTICO | 2 | PDFs con datos de ensayo vacíos; tabla pasadas API 1104 vacía |
| ALTO | 2 | Campos en PDF en blanco; joint design inaccesible |
| MEDIO | 2 | Variable esencial GMAW-S sin registrar; NAG 105 sin certificado propio |
| BAJO | 2 | Rangos nuevos sin calcular; bug de texto en template |

---

---

## PARTE 2 — PLAN DE ATAQUE

Ordenado por impacto y dependencias. Cada fase es independiente y desplegable por separado.

---

### FASE 1 — Bridge test results (CRÍTICO #1) · Backend only

**Objetivo**: que `variables.resultado_xxx` genere automáticamente filas en `certificate_tests` al crear/actualizar un certificado.

**Archivos a modificar**:
- `api/app/Services/CertificateTestSyncService.php` (crear)
- `api/app/Http/Controllers/Api/CertificadoController.php` (inyectar y llamar)

**Implementación**:

1. Crear `CertificateTestSyncService`:

```php
// Mapeo variable → test_type code según norma
private const VAR_TO_CODE = [
    'resultado_vt'              => 'VT',
    'resultado_bend'            => 'BT-R',   // groove; BT-F para filete
    'resultado_rt'              => 'RT',
    'resultado_nick_break'      => 'NB',
    'resultado_fractura_filete' => 'BT-F',
    'resultado_macro'           => 'MC',
];

public function sync(Certificado $cert): void
{
    $vars      = $cert->variables ?? [];
    $tipoJunta = $vars['tipo_junta'] ?? null;  // 'RANURA' | 'FILETE'

    $cert->tests()->delete();

    foreach (self::VAR_TO_CODE as $var => $code) {
        $val = $vars[$var] ?? null;
        if ($val === null) continue;

        // resultado_bend → BT-R (ranura) o BT-F (filete) según tipo_junta
        if ($var === 'resultado_bend' && $tipoJunta === 'FILETE') {
            $code = 'BT-F';
        }

        $testTypeId = DB::table('test_types')->where('code', $code)->value('id');
        if (!$testTypeId) continue;

        $cert->tests()->create([
            'test_type_id' => $testTypeId,
            'resultado'    => $val,
        ]);
    }
}
```

2. Inyectar en `CertificadoController`:
   - `store()` → llamar `$this->testSync->sync($certificado)` después de `rangeCalc->calculate()`
   - `update()` → llamar si no borrador
   - `renovar()` y `ampliar()` → llamar después de calculate

**Prerequisito**: verificar que existan filas en `test_types` con los codes `VT`, `BT-R`, `BT-F`, `RT`, `NB`, `MC`. Si no, agregar seeder `TestTypeSeeder`.

---

### FASE 2 — Fix campos faltantes en `camposXxx()` (ALTO #3) · Backend only

**Archivos a modificar**:
- `api/app/Http/Controllers/Api/CatalogoController.php`
- `admin/src/app/certificados/form/certificado-form.helpers.ts`

**Campos a agregar por norma**:

#### `camposAsme()` — agregar:
```php
['campo' => 'tipo',             'tipo' => 'enum',    'opciones' => ['MANUAL', 'SEMI-AUTO', 'AUTOMATICO'], 'requerido' => false],
['campo' => 'metal_base',       'tipo' => 'text',                                              'requerido' => false],
['campo' => 'espesor_deposito', 'tipo' => 'decimal',                                           'requerido' => false],
['campo' => 'a_number',         'tipo' => 'text',    'si_proceso' => ['SMAW'],                'requerido' => false],
['campo' => 'junta_calificada', 'tipo' => 'text',                                              'requerido' => false],
```

#### `camposAws()` — agregar:
```php
['campo' => 'tipo',             'tipo' => 'enum',    'opciones' => ['MANUAL', 'SEMI-AUTO', 'AUTOMATICO'], 'requerido' => false],
['campo' => 'metal_base',       'tipo' => 'text',                                              'requerido' => false],
['campo' => 'espesor_deposito', 'tipo' => 'decimal',                                           'requerido' => false],
['campo' => 'a_number',         'tipo' => 'text',    'si_proceso' => ['SMAW'],                'requerido' => false],
['campo' => 'junta_calificada', 'tipo' => 'text',                                              'requerido' => false],
```

#### `camposApi()` — correcciones:
```php
// Renombrar grupo_consumible → grupo_electrodo para alinear con template PDF
['campo' => 'grupo_electrodo',      'tipo' => 'catalog', 'catalogo' => 'grupos_consumible', 'requerido' => true],
['campo' => 'metal_base',           'tipo' => 'text',                                        'requerido' => false],
['campo' => 'tiempo_p1_p2',         'tipo' => 'integer',                                     'requerido' => false],
['campo' => 'tiempo_p2_rest',       'tipo' => 'integer',                                     'requerido' => false],
['campo' => 'limpieza_entre_pasadas','tipo'=> 'text',                                         'requerido' => false],
['campo' => 'presentador',          'tipo' => 'enum',    'opciones' => ['EXTERNO', 'INTERNO'], 'requerido' => false],
// Eliminar tiempo_entre_pasadas (no mapea a nada en template)
```

**Nota de migración de datos**: `grupo_consumible` → renombrar a `grupo_electrodo` en `camposApi()` requiere también actualizar datos existentes en `certificates.variables` (`grupo_consumible` → `grupo_electrodo`). Crear migration de data si hay registros en producción.

#### `CAMPO_LABELS` en helpers.ts — agregar:
```typescript
tipo:                 'Tipo (manual/semi-auto)',
metal_base:           'Especificación metal base',
espesor_deposito:     'Espesor depositado (mm)',
a_number:             'A-Number',
junta_calificada:     'Junta calificada',
grupo_electrodo:      'Grupo electrodo/consumible',
tiempo_p1_p2:         'Tiempo máx. entre 1ª y 2ª pasada (min)',
tiempo_p2_rest:       'Tiempo máx. entre 2ª y restantes (min)',
limpieza_entre_pasadas: 'Limpieza entre pasadas',
presentador:          'Presentador',
```

---

### FASE 3 — Joint design en formulario (ALTO #4) · Frontend + Backend

**Objetivo**: capturar `joint_design_id` y `joint_detail` en el formulario.

**Archivos a modificar**:
- `admin/src/app/certificados/form/certificado-form.component.ts`
- `admin/src/app/certificados/form/certificado-form.component.html`
- `admin/src/app/certificados/form/certificado-form-data.service.ts`
- `admin/src/app/core/services/catalogo.service.ts`
- `api/app/Http/Controllers/Api/CatalogoController.php` (endpoint diseños)

**Implementación**:

1. Agregar endpoint `GET /api/catalogos/joint-designs` en `CatalogoController` (ya existe modelo `JointDesign`).

2. Agregar controls en `buildBaseForm()`:
```typescript
joint_design_id: [null],
joint_detail:    [null],
```

3. En el paso "Variables" (o nuevo sub-panel "Diseño de junta"), mostrar:
   - Select de `joint_designs` (thumbnail del SVG + nombre)
   - Input text `joint_detail`
   - Solo para normas que usen diseño de junta (ASME IX, AWS D1.1, AWS D1.6)

4. Incluir en `buildCertPayload()`:
```typescript
joint_design_id: v['joint_design_id'] || null,
joint_detail:    v['joint_detail']    || null,
```

---

### FASE 4 — API 1104: paso de pasadas (CRÍTICO #2) · Frontend + Backend

**Objetivo**: permitir ingresar 1..N pasadas para certificados API 1104.

**Archivos a modificar**:
- `admin/src/app/certificados/form/certificado-form.component.ts` (nuevo step condicional)
- `admin/src/app/certificados/form/certificado-form.component.html` (nuevo paso)
- `admin/src/app/certificados/form/certificado-form.helpers.ts` (nuevo step type)
- `admin/src/app/certificados/form/certificado-form-data.service.ts` (POST /passes)
- `api/app/Http/Controllers/Api/CertificadoController.php` (manejo de passes)

**Estrategia**:

Agregar el step `'pasadas'` al array `steps` condicionalmente, solo cuando la norma seleccionada sea API 1104:

```typescript
get steps() {
  const base = [
    { key: 'solicitante', ... },
    { key: 'variables',   ... },
    { key: 'fechas',      ... },
  ];
  if (this.isApi1104) {
    base.splice(2, 0, { key: 'pasadas', label: 'Pasadas', helper: 'Variables de soldadura por pasada.' });
  }
  return base;
}

get isApi1104(): boolean {
  return !!this.normas.find(n => n.id === +this.f('norma_id').value)
                      ?.nombre?.toLowerCase().includes('1104');
}
```

El paso "Pasadas" muestra una tabla editable con FormArray:
```typescript
pasadas: this.fb.array([])
```

Cada fila: `etiqueta`, `proceso_id`, `clasificacion_aporte`, `diametro_aporte_mm`, `amperaje_min`, `amperaje_max`, `voltaje_min`, `voltaje_max`, `velocidad_avance_min`, `velocidad_avance_max`, `polaridad`, `progresion`.

Al confirmar, las pasadas se envían junto con el certificado o en un segundo POST a `/api/certificados/{id}/passes`.

**Opción simplificada** (si la tabla completa es demasiado para esta iteración): auto-generar una sola pasada desde los campos `electrodo_raiz`, `corriente_raiz`, `electrodo_relleno`, `corriente_relleno` del blob `variables` en el backend, en `CertificadoController::store()`.

---

### FASE 5 — GMAW-S transfer_type (MEDIO #5) · Backend + Frontend

**Archivos a modificar**:
- `api/app/Http/Controllers/Api/CatalogoController.php`
- `admin/src/app/certificados/form/certificado-form.helpers.ts`

**Implementación**:

Agregar a `camposAsme()` y `camposAws()`:
```php
['campo' => 'transfer_type',
 'tipo'  => 'enum',
 'opciones' => ['spray', 'pulse', 'short_circuit', 'globular', 'na'],
 'si_proceso' => ['GMAW', 'GMAW-S'],
 'requerido' => false],
```

Agregar label en helpers.ts:
```typescript
transfer_type: 'Modo de transferencia (GMAW)',
```

La validación de cross-qualification (soldador spray no califica short-circuit) se implementa en `ValidaCombinacionesNorma` como check #4.

---

### FASE 6 — NAG 105: template + campos (MEDIO #6) · Backend

**Archivos a crear/modificar**:
- `api/resources/views/pdf/rcs-nag.blade.php` (crear)
- `api/app/Services/PdfCertificadoService.php` (actualizar `resolveTemplate()`)
- `api/app/Http/Controllers/Api/CatalogoController.php` (agregar `camposNag()`, actualizar `catalogosPorNorma()`)

**Implementación**:

1. `resolveTemplate()`:
```php
private function resolveTemplate(string $normaName): string
{
    return match(true) {
        $normaName === 'API 1104'                       => 'pdf.rcs-api1104',
        str_contains($normaName, 'NAG 105')             => 'pdf.rcs-nag',
        default                                         => 'pdf.rcs-aws-asme',
    };
}
```

2. `catalogosPorNorma()` — agregar case:
```php
str_contains($slug, 'nag 105') => [
    'posiciones'        => $this->posicionesNorma('asme'),  // delega a parent
    'grupos_base_metal' => GrupoBaseMetal::where('norma_id', $effective->id)->orderBy('orden')->get(),
    'grupos_consumible' => GrupoConsumible::where('norma_id', $effective->id)->orderBy('orden')->get(),
    'consumibles'       => $this->consumiblesPorNorma($effective),
    'campos_norma'      => $this->camposNag($norma->nombre),
],
```

3. `camposNag(string $categoria)` — retorna campos del parent más campos NAG adicionales:
```php
private function camposNag(string $nombreNorma): array
{
    // NAG 105 Cat.A y Cat.B → delega a ASME IX
    // NAG 105 Cat.C        → delega a API 1104
    // NAG 105 Cat.D        → solo campos básicos EPS N°1

    $esD = str_contains($nombreNorma, 'Cat. D');
    $esC = str_contains($nombreNorma, 'Cat. C');

    if ($esD) {
        return [
            ['campo' => 'proceso',       'tipo' => 'catalog', 'catalogo' => 'procesos',   'requerido' => true],
            ['campo' => 'tipo_cupon',    'tipo' => 'enum',    'opciones' => ['caño'],      'requerido' => true],
            ['campo' => 'espesor_cupon', 'tipo' => 'decimal',                              'requerido' => true],
            ['campo' => 'diametro_cupon','tipo' => 'decimal',                              'requerido' => true],
            ['campo' => 'posicion',      'tipo' => 'catalog', 'catalogo' => 'posiciones',  'requerido' => true],
            ['campo' => 'electrodo',     'tipo' => 'text',                                 'requerido' => true],
            ['campo' => 'resultado_vt',  'tipo' => 'enum',    'opciones' => ['aprobado', 'rechazado'], 'requerido' => true],
        ];
    }

    // Cat.A, Cat.B → campos ASME + campo credencial (en soldador, no en cert)
    // Cat.C        → campos API 1104
    return $esC ? $this->camposApi() : $this->camposAsme();
}
```

4. Crear `rcs-nag.blade.php`: copia de `rcs-aws-asme.blade.php` con sección NAG (categoría, credencial del soldador, vigencia 2 años, regla 90 días inactividad).

---

### FASE 7 — RangeCalculatorService: rangos filler/base_metal (BAJO #7) · Backend

**Archivo a modificar**: `api/app/Services/RangeCalculatorService.php`

**Implementación**: agregar métodos `fillerGroupRange()` y `baseMetalGroupRange()` que consulten las nuevas tablas `consumable_group_qualifications` y `base_metal_group_qualifications`.

```php
// En calculate():
$fillerCode = $vars['f_number'] ?? $vars['grupo_consumible'] ?? $vars['grupo_electrodo'] ?? null;
if ($fillerCode) {
    $r = $this->fillerGroupRange($cert->norma_id, $fillerCode);
    if ($r) $created->push($cert->ranges()->create(array_merge(['type' => 'filler_group'], $r)));
}

$metalCode = $vars['p_number'] ?? $vars['grupo_base_metal'] ?? null;
if ($metalCode) {
    $r = $this->baseMetalGroupRange($cert->norma_id, $metalCode);
    if ($r) $created->push($cert->ranges()->create(array_merge(['type' => 'base_metal_group'], $r)));
}
```

Consultas usando `consumable_group_qualifications` / `base_metal_group_qualifications` para obtener el array de grupos calificados y formatear como descripción.

---

### FASE 8 — Fix bug texto API 1104 (BAJO #8) · Backend trivial

**Archivo**: `api/resources/views/pdf/rcs-api1104.blade.php`

Reemplazar en líneas 361–364 y 551–555:
```
CODE ASME IX- Ed 2023
```
por:
```
API STANDARD 1104 Ed 2022
```

---

## Prioridad de implementación

| Fase | Gap | Impacto | Esfuerzo estimado | Orden |
|---|---|---|---|---|
| 1 | Test results bridge | CRÍTICO | Bajo (backend only) | 1° |
| 8 | Fix bug cert text API 1104 | BAJO | Trivial | 1° (junto a Fase 1) |
| 2 | Campos faltantes camposXxx() | ALTO | Bajo-medio | 2° |
| 3 | Joint design en form | ALTO | Medio | 3° |
| 5 | GMAW-S transfer_type | MEDIO | Bajo | 3° (junto a Fase 3) |
| 4 | API 1104 pasadas UI | CRÍTICO | Alto | 4° |
| 6 | NAG 105 template + campos | MEDIO | Alto | 5° |
| 7 | RangeCalculator filler/base | BAJO | Medio | 6° |

---

## Dependencias entre fases

```
Fase 1 (test bridge) → independiente
Fase 2 (campos) → prerequisito para Fase 5 (transfer_type entra aquí)
Fase 3 (joint design) → necesita endpoint catalogo joint-designs
Fase 4 (pasadas UI) → puede simplificarse con auto-generate desde Fase 2
Fase 6 (NAG) → necesita Fase 2 completa (reutiliza camposAsme/camposApi)
Fase 7 (ranges) → necesita Fase 2 para campo nombres alineados
```
