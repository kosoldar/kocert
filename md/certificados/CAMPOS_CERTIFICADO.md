# Campos del certificado — referencia completa

Extraído de `rcs-aws-asme.blade.php` y `rcs-api1104.blade.php`.

---

## Campos compartidos (ASME IX / AWS D1.x / API 650 / IRAM + API 1104)

### Header / identificación

| Campo | Origen |
|---|---|
| Logo Kosoldar | imagen estática del servidor (`public/images/logos/kosoldar.png`) |
| Logo de norma | seleccionado por `norma.nombre` (asme.png / aws-d11.png / api-1104.png) |
| Código QR | generado desde `cert.qr_token` → URL de verificación |
| N° RCS | `cert.numero` + `cert.anio` + `cert.revision` |
| Edición de norma | texto fijo según `norma.nombre` |

### Inspector

| Campo | Origen |
|---|---|
| Nombre | `inspector.nombre` |
| Teléfono | `inspector.telefono` |
| Email | `inspector.email` |
| Certificación | `inspector.certificacion` |
| Firma | `inspector.firma_path` (imagen) |

### Soldador

| Campo | Origen |
|---|---|
| Apellido | `soldador.apellido` |
| Nombre | `soldador.nombre` |
| Cuño | `soldador.cuño` (opcional) |
| DNI | `soldador.dni` |
| Foto DNI | `soldador.foto_path` (imagen, opcional) |
| Empresa | `empresa.nombre` (o "PARTICULAR") |

### Certificado

| Campo | Origen |
|---|---|
| EPS / WPS | `cert.eps_numero` |
| PQR / RCP | `cert.pqr_numero` (opcional) |
| Fecha calificación | `cert.fecha_calificacion` (formato DD/MES/YYYY) |
| Fecha vencimiento | `cert.fecha_vencimiento` (formato DD-MM-YY) |
| Proceso | `cert.proceso` |
| Posición | `cert.posicion` |
| Progresión | `cert.progresion` (ascendente / descendente) |
| Tipo cupón | `cert.tipo_cupon` (caño / chapa) |
| Resultado final | `cert.resultado` → sello APROBADO / RECHAZADO |
| Observaciones | `cert.observaciones` |

### Variables de soldadura (`cert.variables` JSON)

| Campo | Clave en JSON |
|---|---|
| Tipo de uso | `variables.tipo` (MANUAL / SEMI-AUTO…) |
| Respaldo | `variables.respaldo` |
| P-Number (metal base) | `variables.p_number` |
| Especificación metal base | `variables.metal_base` / `variables.especificacion_metal_base` |
| Electrodo | `variables.electrodo` |
| F-Number | `variables.f_number` |
| A-Number | `variables.a_number` |
| Tungsteno (GTAW) | `variables.tungsteno` |
| Gas protección | `variables.gas_proteccion` |
| Gas respaldo | `variables.gas_respaldo` |
| Corriente / polaridad | `variables.corriente` |
| Espesor cupón [mm] | `variables.espesor_cupon` |
| Espesor depositado [mm] | `variables.espesor_deposito` |
| Diámetro cupón [mm] | `variables.diametro_cupon` |

### Diseño de junta (relación `cert.jointDesign`)

| Campo | Origen |
|---|---|
| SVG del diseño | `cert.jointDesign.svg` |
| Nombre de junta | `cert.jointDesign.nombre` |
| Detalle | `cert.joint_detail` |
| Junta calificada | `variables.junta_calificada` |

### Rangos calificados (relación `cert.ranges`, calculados por `RangeCalculatorService`)

| Rango | `ranges.type` |
|---|---|
| Espesor calificado | `espesor` |
| Diámetro calificado | `diametro` |
| Posiciones calificadas | `posicion` (puede haber múltiples) |
| Grupo metal base calificado | `grupo_base_metal` |
| Grupo consumible calificado | `grupo_consumible` |

### Ensayos / resultados (relación `cert.tests`)

Cada registro tiene `testType.code` y `resultado` (aprobado / rechazado / na).

| Ensayo | Código |
|---|---|
| Examen visual | `VT` |
| Plegado transversal raíz/cara | `BT-R` |
| Plegado cara | `BT-F` |
| Plegado lateral | `BT-S` |
| Radiografía (alternativa) | `RT` |
| Nick Break | `NB` (API 1104) |

---

## Campos exclusivos API 1104

| Campo | Origen |
|---|---|
| Ciudad del soldador | `soldador.ciudad` |
| Grupo consumible | `variables.grupo_consumible` |
| Electrodo raíz | `variables.electrodo_raiz` |
| Electrodo relleno | `variables.electrodo_relleno` |
| Electrodo terminación | `variables.electrodo_terminacion` |
| Corriente raíz | `variables.corriente_raiz` |
| Corriente relleno | `variables.corriente_relleno` |
| Precalentamiento [°C] | `variables.temperatura_preheat` |
| Temperatura interpass [°C] | `variables.temperatura_interpass` |
| Velocidad de avance | `variables.velocidad_avance` |
| Tiempo máx. entre pasada 1 y 2 | `variables.tiempo_p1_p2` |
| Tiempo máx. entre pasada 2 y resto | `variables.tiempo_p2_rest` |
| Limpieza entre pasadas | `variables.limpieza_entre_pasadas` |
| Presentador | `variables.presentador` |
| Material backing | `variables.backing_material` |
| Tipo de línea | `variables.linea_tipo` |
| Material base rango desde | `variables.material_base_rango_desde` |
| Material base rango hasta | `variables.material_base_rango_hasta` |
| Equivalente ASME IX | `variables.material_asme_equivalente` |

### Tabla de pasadas — API 1104 (relación `cert.passes`)

Una fila por pasada, columnas:

| Columna | Origen |
|---|---|
| Secuencia / etiqueta | `pass.etiqueta` o "Pasada N" |
| Proceso | `pass.proceso.nombre` |
| Clasificación AWS | `pass.clasificacion_aporte` |
| Diámetro aporte [mm] | `pass.diametro_aporte_mm` |
| Amperaje [A] | `pass.amperaje_min` / `pass.amperaje_max` |
| Voltaje [V] | `pass.voltaje_min` / `pass.voltaje_max` |
| Velocidad avance [cm/min] | `pass.velocidad_avance_min` / `pass.velocidad_avance_max` |
| Polaridad | `pass.polaridad` |
| Progresión | `pass.progresion` |

---

## Checkboxes — lógica de marcado

| Checkbox | Marcado cuando | Template |
|---|---|---|
| Pipe / Caño | `cert.tipo_cupon === 'caño'` | ASME/AWS ⚠️ código actual tiene `!$esCanio` — posible bug |
| Sheet / Chapa | `cert.tipo_cupon === 'chapa'` | ASME/AWS |
| Pipe / Caño | hardcodeado `true` | API 1104 (siempre es caño) |
| Renovación / Ampliación | `cert.tipo !== 'inicial'` | ASME/AWS |
| Presentador externo | `variables.presentador === 'externo'` | API 1104 |
| VT (examen visual) | `tests['VT'].resultado === 'aprobado'` | ambos |
| Bend test (global) | `tests['BT-R'] === 'aprobado'` OR `tests['BT-F'] === 'aprobado'` | ambos |
| Bend transversal (BT-R) | `tests['BT-R'].resultado === 'aprobado'` | ambos |
| Bend lateral (BT-S) | `tests['BT-S'].resultado === 'aprobado'` | ASME/AWS |
| Radiografía RT | `tests['RT'].resultado === 'aprobado'` | ASME/AWS |
| Nick Break (NB) | `tests['NB'].resultado === 'aprobado'` | API 1104 |
| Fillet weld / Macro | hardcodeado `false` | ASME/AWS (no aplica) |

El helper PHP: `$chk = fn(bool $c) => $c ? '&#9632;' : '&#9633;'`
- ■ `&#9632;` = marcado
- □ `&#9633;` = vacío

---

## Relaciones del modelo `Certificado`

```
Certificado
├── soldador        → Soldador (foto_path, ciudad solo en API 1104)
├── empresa         → Empresa
├── norma           → Norma
├── inspector       → Inspector (firma_path)
├── usuario         → User
├── jointDesign     → JointDesign (svg, nombre)
├── tests[]         → CertTest → testType (code, nombre)
├── passes[]        → CertPass → proceso (API 1104)
└── ranges[]        → CertRange (type, descripcion, min_value, max_value)
```
