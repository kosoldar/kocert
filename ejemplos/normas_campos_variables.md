---
name: Variables y catálogos por norma
description: Listado completo de campos, opciones y catálogos requeridos para cada norma soportada en kocert. Referencia para implementar catalogo_norma_items y camposNorma por norma.
type: reference
---

# Variables y catálogos por norma

## Estado actual

| Norma | campos_norma | Catálogos DB |
|---|---|---|
| ASME IX | ✅ implementado | f_numbers, p_numbers, posiciones, tipos_electrodo |
| AWS D1.1 | ✅ implementado | f_numbers, grupos_base_metal_aws, tipos_electrodo |
| API 1104 | ✅ implementado | grupos_electrodo_api, posiciones |
| ASME B31.3 | ❌ sin campos | — alias a ASME IX |
| ASME B31.8 | ❌ sin campos | — alias a ASME IX |
| AWS D1.6 | ❌ sin campos | grupos específicos para inox |
| API 650 | ❌ sin campos | grupos similares a AWS D1.1 |
| IRAM | ❌ sin campos | grupos ISO completamente distintos |

---

## ASME IX (implementado — referencia)

**Alcance:** recipientes a presión, intercambiadores, piping de proceso.

| Campo | Tipo | Catálogo / Opciones | Req | Condición |
|---|---|---|---|---|
| proceso | catalog | procesos | ✓ | |
| posicion | catalog | posiciones | ✓ | |
| progresion | enum | ascendente, descendente | ✓ | |
| tipo_cupon | enum | caño, chapa | ✓ | |
| p_number | catalog | p_numbers | ✓ | |
| f_number | catalog | f_numbers | ✓ | |
| electrodo | catalog | tipos_electrodo | ✓ | |
| respaldo | enum | CON RESPALDO, SIN RESPALDO | ✓ | |
| corriente | enum | CCEP, CCEN, CA | ✓ | |
| espesor_cupon | decimal | — | ✓ | |
| diametro_cupon | decimal | — | ✓ | si tipo_cupon=caño |
| gas_proteccion | text | — | | si proceso=GTAW |
| gas_respaldo | text | — | | si proceso=GTAW |
| tungsteno | text | — | | si proceso=GTAW |
| resultado_vt | enum | aprobado, rechazado | ✓ | |
| resultado_bend | enum | aprobado, rechazado | ✓ | |
| resultado_rt | enum | aprobado, rechazado, NA | | alternativo al bend |

**Rangos calculados por RangeCalculatorService:** espesor (2t / ilimitado ≥13mm+3capas), diámetro (QW-452.3), posiciones (QW-461.9).

---

## AWS D1.1 (implementado — referencia)

**Alcance:** soldaduras estructurales en acero al carbono y de baja aleación.

| Campo | Tipo | Catálogo / Opciones | Req | Condición |
|---|---|---|---|---|
| proceso | catalog | procesos | ✓ | |
| tipo_junta | enum | RANURA, FILETE | ✓ | |
| posicion | catalog | posiciones | ✓ | |
| progresion | enum | ascendente, descendente | | |
| tipo_cupon | enum | caño, chapa | ✓ | |
| espesor_cupon | decimal | — | ✓ | |
| grupo_base_metal | catalog | grupos_base_metal_aws | ✓ | |
| f_number | catalog | f_numbers | ✓ | si proceso=SMAW |
| electrodo | catalog | tipos_electrodo | ✓ | |
| respaldo | enum | CON RESPALDO, SIN RESPALDO | ✓ | |
| temperatura_preheat | decimal | — | | |
| temperatura_interpass | decimal | — | | |
| gas_proteccion | text | — | | si proceso=GTAW,GMAW |
| resultado_vt | enum | aprobado, rechazado | ✓ | |
| resultado_bend | enum | aprobado, rechazado | | si tipo_junta=RANURA |
| resultado_rt | enum | aprobado, rechazado, NA | | si tipo_junta=RANURA |
| resultado_fractura_filete | enum | aprobado, rechazado | | si tipo_junta=FILETE |
| resultado_macro | enum | aprobado, rechazado | | si tipo_junta=FILETE |

**Grupos metal base (Table 5.3):**
- Grupo I: A36, A53 Gr.B, A500 Gr.A/B — fy ≤ 36 ksi (250 MPa)
- Grupo II: A441, A572 Gr.42/50, A588 — 42–65 ksi
- Grupo III: A514 < 63mm, A517, A709 Gr.100 — 46–70 ksi
- Grupo IV: A514 ≥ 63mm — 90–100 ksi

---

## API 1104 (implementado — referencia)

**Alcance:** ductos de transmisión y distribución (onshore).

| Campo | Tipo | Catálogo / Opciones | Req | Condición |
|---|---|---|---|---|
| linea_tipo | enum | LINEA REGULAR, LINEA ESPECIAL | ✓ | |
| cliente | text | — | ✓ | |
| obra | text | — | ✓ | |
| empresa_contratista | text | — | ✓ | |
| proceso | catalog | procesos | ✓ | |
| posicion | catalog | posiciones | ✓ | |
| progresion | enum | ascendente, descendente | ✓ | |
| tipo_cupon | enum | caño | ✓ | fijo: solo caño |
| espesor_cupon | decimal | — | ✓ | |
| diametro_cupon | decimal | — | ✓ | |
| grupo_electrodo | catalog | grupos_electrodo_api | ✓ | WF-1 a WF-6 |
| electrodo_raiz | text | — | ✓ | |
| electrodo_relleno | text | — | ✓ | |
| electrodo_terminacion | text | — | | |
| corriente_raiz | enum | CCEP, CCEN | ✓ | |
| corriente_relleno | enum | CCEP, CCEN | ✓ | |
| temperatura_preheat | decimal | — | | |
| num_pasadas | integer | — | | |
| velocidad_avance | text | — | | |
| resultado_vt | enum | aprobado, rechazado | ✓ | |
| resultado_bend | enum | aprobado, rechazado | ✓ | |
| resultado_nick_break | enum | aprobado, rechazado | ✓ | obligatorio (no alternativo) |

**Grupos electrodo WF (Table 4):** WF-1 SMAW celulósico, WF-2 SMAW bajo hidrógeno, WF-3 bajo hidrógeno vertical-down, WF-4 GMAW/GTAW wire, WF-5 OFW, WF-6 FCAW con gas.

---

## ASME B31.3 — Process Piping

**Alcance:** tuberías de proceso (refinería, petroquímica, industria química).

**Calificación:** remite textualmente a **ASME Section IX** (párrafo 328.2.1).
→ **campos_norma = alias exacto de camposAsme()**
→ mismos catálogos: f_numbers, p_numbers, posiciones, tipos_electrodo
→ mismos rangos: RangeCalculatorService aplica reglas ASME IX sin modificaciones

**Diferencia documental:** el RCS emitido bajo B31.3 debe referenciar la edición del código (B31.3-2022). Sin impacto en variables.

---

## ASME B31.8 — Gas Transmission and Distribution Piping

**Alcance:** gasoductos, sistemas de distribución de gas.

**Calificación:** remite textualmente a **ASME Section IX** (párrafo 817.1).
→ **campos_norma = alias exacto de camposAsme()**
→ mismos catálogos y rangos que ASME IX

**Diferencia práctica:** B31.8 agrega requisito de progresión descendente para ciertos gasoductos (similar a API 1104). El campo `progresion` ya está en camposAsme().

---

## AWS D1.6 — Structural Welding Code: Stainless Steel

**Alcance:** soldaduras estructurales en acero inoxidable.

**Relación con D1.1:** misma estructura de calificación (Table 6.10 posiciones, ensayos VT+bend). La diferencia está en los materiales y consumibles.

| Campo | Tipo | Catálogo / Opciones | Req | Condición |
|---|---|---|---|---|
| proceso | catalog | procesos | ✓ | SMAW, GTAW, GMAW, FCAW |
| tipo_junta | enum | RANURA, FILETE | ✓ | |
| posicion | catalog | posiciones | ✓ | |
| progresion | enum | ascendente, descendente | | |
| tipo_cupon | enum | caño, chapa | ✓ | |
| espesor_cupon | decimal | — | ✓ | |
| grupo_base_metal | catalog | **grupos_base_metal_d16** ⚠️ | ✓ | |
| clasificacion_aporte | catalog | **consumibles_d16** ⚠️ | ✓ | |
| respaldo | enum | CON RESPALDO, SIN RESPALDO | ✓ | |
| gas_proteccion | text | — | ✓ | obligatorio (inox requiere gas) |
| gas_respaldo | text | — | | si proceso=GTAW |
| temperatura_interpas | decimal | — | | temp máx interpass crítica en inox |
| resultado_vt | enum | aprobado, rechazado | ✓ | |
| resultado_bend | enum | aprobado, rechazado | | si tipo_junta=RANURA |
| resultado_fractura_filete | enum | aprobado, rechazado | | si tipo_junta=FILETE |
| resultado_macro | enum | aprobado, rechazado | | si tipo_junta=FILETE |

**Grupos metal base D1.6 (Table 1.2):** ⚠️ catálogo nuevo
- Grupo A: Austenítico (304, 304L, 316, 316L, 321, 347) — tipo más común
- Grupo B: Ferrítico/Martensítico (409, 430, 410, 420)
- Grupo C: Dúplex (2205 / UNS S31803, 2304)
- Grupo D: Endurecible por precipitación (17-4 PH / S17400)

**Consumibles D1.6:** ⚠️ catálogo nuevo — clasificados por AWS A5.4 y A5.9
- ER308/ER308L — para Grupo A
- ER316/ER316L — para Grupo A (con Mo)
- ER309/E309 — disímiles / Grupo B sobre Grupo A
- E308L-XX, E316L-XX — SMAW inox

**Rangos:** mismas tablas de posición que D1.1. Espesor: Table 6.11 = idéntica a D1.1. Sin reglas de diámetro para plate.

---

## API 650 — Welded Tanks for Oil Storage

**Alcance:** tanques de almacenamiento para hidrocarburos (cuerpo, fondo, techo).

**Calificación:** Annex B (Appendix B) — remite a AWS D1.1 o API 1104 según el proceso y joint type.

| Campo | Tipo | Catálogo / Opciones | Req | Condición |
|---|---|---|---|---|
| proceso | catalog | procesos | ✓ | SMAW, GMAW, FCAW, SAW, GTAW |
| tipo_junta | enum | RANURA (butt), FILETE (fillet) | ✓ | |
| posicion | catalog | posiciones | ✓ | principalmente plate |
| tipo_cupon | enum | chapa | ✓ | fijo: solo chapa (plate) |
| espesor_cupon | decimal | — | ✓ | |
| grupo_base_metal | catalog | **grupos_base_metal_api650** ⚠️ | ✓ | |
| f_number | catalog | f_numbers | ✓ | si proceso=SMAW |
| electrodo | text | — | ✓ | |
| respaldo | enum | CON RESPALDO, SIN RESPALDO | ✓ | |
| temperatura_preheat | decimal | — | | |
| resultado_vt | enum | aprobado, rechazado | ✓ | |
| resultado_bend | enum | aprobado, rechazado | | si tipo_junta=RANURA |
| resultado_fractura_filete | enum | aprobado, rechazado | | si tipo_junta=FILETE |

**Grupos metal base API 650 (Table 7.1):** ⚠️ catálogo nuevo
- Grupo I: A36, A283 Gr.C/D, A285 Gr.A/B/C — bajo carbono
- Grupo II: A516 Gr.55/60/65/70, A537 Cl.1 — media resistencia
- Grupo III: A537 Cl.2, A633 Gr.C/D, A678 Gr.B — mayor resistencia
- Grupo IV: API 5L Gr.X42–X65 — para tanques especiales

**Nota:** API 650 Annex B para progresión: permite vertical-down solo con WPS calificado específicamente. El campo `progresion` aplica.

---

## IRAM — ISO 9606-1 (Calificación de Soldadores)

**Alcance:** norma argentina basada en ISO 9606-1:2012. Empleada en presión, estructural, general.

**Estructura completamente distinta** a las normas americanas. Codificación sistemática en el certificado.

| Campo | Tipo | Catálogo / Opciones | Req | Condición |
|---|---|---|---|---|
| proceso | catalog | **procesos_iso** ⚠️ | ✓ | código numérico ISO |
| tipo_producto | enum | P (chapa), T (tubería) | ✓ | |
| tipo_junta | enum | BW (ranura), FW (filete) | ✓ | |
| grupo_material | catalog | **grupos_material_iso** ⚠️ | ✓ | W01-W61 |
| grupo_consumible | catalog | **grupos_consumible_iso** ⚠️ | ✓ | FM1-FM6 |
| tipo_respaldo | enum | ss nb, ss mb, bs | ✓ | |
| posicion | catalog | **posiciones_iso** ⚠️ | ✓ | PA/PB/PC/PF/PG/PH/H-L045 |
| progresion | enum | ascendente (PF), descendente (PG) | | si posicion PF o PG |
| espesor_cupon | decimal | — | ✓ | |
| diametro_exterior | decimal | — | ✓ | si tipo_producto=T |
| resultado_vt | enum | aprobado, rechazado | ✓ | |
| resultado_bend | enum | aprobado, rechazado | ✓ | o RT alternativo |
| resultado_rt | enum | aprobado, rechazado, NA | | alternativo al bend |

**Procesos ISO (catálogo nuevo):** ⚠️
- 111 — SMAW (Electrodo revestido)
- 114 — FCAW autoprotegido (sin gas)
- 121 — SAW (Arco sumergido, alambre simple)
- 131 — GMAW MIG (gas inerte)
- 135 — GMAW MAG (gas activo)
- 136 — FCAW con gas protector
- 141 — GTAW TIG
- 311 — OFW (Oxigas)

**Grupos de material ISO/TR 15608 (catálogo nuevo):** ⚠️
- W01 — Aceros al carbono, fy ≤ 355 MPa (S235, S355, P265GH, API 5L A-X52)
- W02 — Aceros de alta resistencia, 355 < fy ≤ 460 MPa (S420, S460)
- W03 — Aceros de grano fino normalizado, fy ≤ 460 MPa
- W04 — Aceros Cr-Mo de baja aleación (Cr ≤ 0.75%)
- W11 — Aceros ferríticos resistentes al creep (1–12% Cr)
- W21 — Aceros inoxidables austeníticos Cr 18–20 / Ni 8–12 (304, 316)
- W22 — Aceros inoxidables austeníticos alta aleación (309, 310)
- W31 — Aleaciones de níquel (Inconel, Monel)
- W41 — Aluminio y aleaciones
- W51 — Cobre y aleaciones
- W61 — Titanio y aleaciones

**Grupos consumible ISO 9606-1 Anexo B (catálogo nuevo):** ⚠️
- FM1 — Rutílico de escoria lenta (E XX R, E XX RR) — todas posiciones con escoria viscosa
- FM2 — Básico / celulósico de escoria rápida (E XX B, E XX C) — para 3G/4G
- FM3 — Rutílico de escoria rápida (E XX RC) — posición vertical
- FM4 — Cualquier otro tipo (ácido, oxidante)
- FM5 — Sin aporte (autógena, sold. por resistencia)
- FM6 — Aporte sólido o tubular (GMAW/GTAW/FCAW wire)

**Posiciones ISO (catálogo nuevo):** ⚠️
- PA — Plana (= 1G/1F)
- PB — Horizontal para filete (= 2F)
- PC — Horizontal para ranura (= 2G)
- PD — Sobrecabeza para filete (= 4F)
- PE — Sobrecabeza para ranura (= 4G)
- PF — Vertical ascendente (= 3G up)
- PG — Vertical descendente (= 3G down)
- PH — Tubo horizontal fijo ascendente (= 5G up)
- PJ — Tubo horizontal fijo descendente (= 5G down)
- H-L045 — Tubo inclinado 45° (= 6G)

**Rangos calificados ISO 9606-1:**
- Espesor: t < 3mm → solo t; 3 ≤ t < 12mm → 3mm a 2t; t ≥ 12mm → 3mm a ilimitado
- Diámetro: D ≤ 25mm → solo D; 25 < D ≤ 76mm → 0.5D a 2D; D > 76mm → 0.5D
- Posición: tabla específica por proceso y tipo de junta (más restrictiva que ASME)

---

## Resumen de catálogos nuevos necesarios

| Catálogo | Norma | N° items est. | Prioridad |
|---|---|---|---|
| grupos_base_metal_d16 | AWS D1.6 | 4 grupos | Media |
| consumibles_d16 | AWS D1.6 | ~8 items | Media |
| grupos_base_metal_api650 | API 650 | 4 grupos | Media |
| procesos_iso | IRAM | 8 items | Alta |
| grupos_material_iso | IRAM | ~10 grupos | Alta |
| grupos_consumible_iso | IRAM | 6 grupos | Alta |
| posiciones_iso | IRAM | 10 items | Alta |

**Total estimado:** ~50 filas nuevas en `catalogo_norma_items`.

---

## Plan de implementación propuesto

1. **Migración** — crear tabla `catalogo_norma_items (id, norma_id, catalogo, codigo, descripcion, orden)`
2. **Seeders** — cargar los 7 catálogos nuevos (tabla arriba)
3. **CatalogoController** — `porNorma()` incluye los items filtrados por norma en la respuesta
4. **camposNorma PHP** — agregar casos para D1.6, API 650, IRAM en el `match()`; B31.3 y B31.8 como alias de ASME IX
5. **Angular** — `getCatalogOptions()` maneja el nuevo catálogo genérico
6. **RangeCalculatorService** — agregar estrategias para D1.6 (=D1.1), ISO 9606-1 (reglas distintas)
