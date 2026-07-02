---
name: Conocimiento técnico de normas de soldadura
description: Reglas, cláusulas y lógica de calificación extraídas de ASME IX, AWS D1.1 y API 1104. Referencia rápida para implementar RangeCalculatorService y cualquier lógica de negocio sin releer los PDFs.
type: reference
---

## Fuentes

| Norma | Documento | Ubicación local |
|---|---|---|
| ASME IX | ASME BPVC Section IX 2017/2021 (español) | `info/teoria/PADULA/ASME IX/` |
| AWS D1.1 | AWS D1.1:2020 Structural Welding Code — Steel (español) | `info/teoria/PADULA/Normas AWS/` |
| API 1104 | API Std 1104 Ed. 22 R2021 | `info/teoria/PADULA/API/` |

Para leer los PDFs: `pdftotext` disponible en PATH del sistema (mingw64).

---

## ASME IX — Welder Performance Qualification

### Variables esenciales del soldador (QW-350 / QW-360)
Las que invalidan la calificación si cambian:
- Cambio de proceso de soldadura (ej: SMAW → GTAW)
- Cambio de F-Number a uno superior no cubierto (ver jerarquía abajo)
- Cambio de P-Number del metal base (con excepciones)
- Eliminación del respaldo (backing) cuando se calificó con él
- Cambio de posición a una no cubierta por la calificación
- Cambio de progresión (uphill/downhill)
- Cambio de diámetro fuera del rango calificado (para pipe)

**Lo que NO es variable esencial (QW-402.1):** el tipo y dimensiones de la ranura (joint design). No afecta la calificación del soldador.

### Reglas de espesor — QW-452.1(b)

| Espesor ensayado (t) | Condición | Rango calificado |
|---|---|---|
| Cualquier t | — | hasta 2t |
| t ≥ 13 mm (1/2 in.) | ≥ 3 capas depositadas | ilimitado (unlimited) |

Mínimo calificado: siempre desde el espesor ensayado (no hay mínimo fijo).

### Reglas de diámetro — QW-452.3 (solo pipe/tubo)

| OD ensayado | Mínimo calificado | Máximo calificado |
|---|---|---|
| OD < 25.4 mm (1 in.) | desde OD ensayado | ilimitado |
| 25.4 ≤ OD ≤ 73 mm (2-7/8 in.) | 25.4 mm | ilimitado |
| OD > 73 mm | 73 mm | ilimitado |

### Posiciones calificadas — QW-461.9

**Groove welds (ranuras):**
| Posición ensayada | Posiciones calificadas |
|---|---|
| 1G (plana) | 1G |
| 2G (horizontal) | 1G, 2G |
| 3G (vertical) | 1G, 3G |
| 4G (sobre cabeza) | 1G, 4G |
| 3G + 4G | 1G, 3G, 4G |
| 5G (tubo horizontal fijo) | 1G, 3G, 4G (= F, V, O) |
| 6G (tubo 45° fijo) | todas las posiciones |
| 2G + 5G | todas las posiciones |

**Fillet welds (filetes):**
| Posición ensayada | Posiciones calificadas |
|---|---|
| 1F | 1F |
| 2F | 1F, 2F |
| 3F | 1F, 2F, 3F |
| 4F | 1F, 2F, 4F |
| 3F + 4F | todas |

### F-Numbers (QW-432) — Jerarquía de consumibles

| F-Number | Electrodos | Descripción |
|---|---|---|
| F-No. 1 | EXX20/24/27/28 | Bajo hidrógeno, solo plana/horizontal |
| F-No. 2 | EXX12/13/14 | Rutílicos, todas posiciones |
| F-No. 3 | EXX10/11 — E6010, E6011 | Celulósicos, todas posiciones |
| F-No. 4 | EXX15/16/18 — E7018 | Bajo hidrógeno, todas posiciones |
| F-No. 5 | EXXX(X)-15/16 | Inoxidable austenítico |
| F-No. 6 | ER70S, E71T | Todos los aceros GMAW/GTAW/FCAW/SAW |

**Jerarquía QW-433 (quién califica a quién):**
- F-No. 4 → califica también F-No. 3, F-No. 2, F-No. 1
- F-No. 3 → califica también F-No. 2, F-No. 1
- F-No. 2 → califica también F-No. 1
- F-No. 1 → solo F-No. 1
- F-No. 5 → solo F-No. 5
- F-No. 6 → solo F-No. 6

### P-Numbers (QW/QB-422) — Grupos de metal base

| P-Number | Materiales típicos | Resistencia |
|---|---|---|
| P-No. 1 Gr. 1 | A36, A53, A106B, API 5L A–X52 | fy ≤ 55 ksi |
| P-No. 1 Gr. 2 | A516 Gr.65/70, API 5L X56–X65 | 55–70 ksi |
| P-No. 1 Gr. 3 | — | 70–80 ksi |
| P-No. 3 Gr. 1 | 1/2Cr-1/2Mo, 1Cr-1/2Mo | alloy |
| P-No. 3 Gr. 2 | 1-1/4Cr-1/2Mo, 2-1/4Cr-1Mo | alloy |
| P-No. 4 Gr. 1 | 2Cr-1Mo, 5Cr-1/2Mo | alloy |
| P-No. 8 Gr. 1 | SS 304, 316, 347 | austenítico |

### Continuidad de calificación — QW-322.1
- Si un soldador no suelda durante **6 meses consecutivos**, pierde todas las calificaciones.
- Para mantenerlas: debe soldar un cupón de prueba en ese proceso, o demostrar actividad reciente.
- El empleador puede extender la calificación mediante declaración escrita de actividad continua.
- Modelado en kocert con `welder_events.event_type = 'activity_record'`.

### Ensayos requeridos — QW-452
- **Plate coupon:** Visual (VT) + Bend tests (root bend + face bend, o side bend si t ≥ 10mm)
- **Pipe coupon:** ídem, generalmente 2 root bend + 2 face bend (o 4 side bend)
- RT puede sustituir bend tests bajo condiciones específicas

### Joint design — no variable esencial
ASME IX define 13 tipos estándar de ranura (QW-469 / Fig. QW-469.1):
single-V, double-V, single-bevel, double-bevel, single-U, double-U, single-J, double-J,
flare-V, flare-bevel, square, backing weld, consumable insert.
El tipo no afecta la calificación del soldador — es dato informativo en el WPQ.

---

## API 1104 — Welder Qualification

### Grupos de material base (§5.3 — por SMYS)

| Código kocert | Grados | SMYS |
|---|---|---|
| API 5L A/B | API 5L Grade A y B | ≤ 42 ksi (290 MPa) |
| API 5L X42–X52 | X42, X46, X52 | 42–52 ksi |
| API 5L X56–X65 | X56, X60, X65 | 56–65 ksi |
| API 5L X70–X80 | X70, X80 | ≥ 70 ksi |

Un soldador calificado con un grado cubre todos los grados de igual o menor SMYS.

### Reglas de espesor — Table 5

| t ensayado | Mínimo calificado | Fórmula máximo |
|---|---|---|
| t < 3.9 mm | desde t ensayado | max(3.9, 1.5t) |
| 3.9 ≤ t < 19 mm | 3.9 mm | max(19.0, 1.5t) |
| t ≥ 19 mm | 19.0 mm | ilimitado |

### Reglas de diámetro — §6.2.2(d)

| Grupo | OD ensayado | Mínimo calificado | Máximo calificado |
|---|---|---|---|
| G1 | OD < 60.3 mm | desde OD ensayado | 60.3 mm |
| G2 | 60.3 ≤ OD ≤ 323.9 mm | desde OD ensayado | 323.9 mm |
| G3 | OD > 323.9 mm | desde OD ensayado | ilimitado |

G3 califica G1+G2+G3. G2 califica G1+G2. G1 califica solo G1.

### Posiciones — §6.2.2(b)

| Posición ensayada | Califica |
|---|---|
| Rolled (1G — tubo giratorio) | solo rolled |
| Fixed horizontal (5G) | rolled + fixed horizontal + fixed vertical (F, V, O) |
| Fixed 45° (6G) | todas |

### WF-Groups (Table 4) — Grupos de consumibles

| WF | Proceso/Electrodo | AWS Classification |
|---|---|---|
| WF-1 | SMAW celulósico | EXX10, EXX11 (A5.1/A5.5) |
| WF-2 | SMAW bajo hidrógeno | EXX15/16/18 (A5.1/A5.5) |
| WF-3 | SMAW bajo hidrógeno vertical-down | E8045/9045/10045 |
| WF-4 | GMAW/GTAW wire | ERXXS-X (A5.18/A5.28) |
| WF-5 | Oxifuel (OFW) | RG60, RG65 (A5.2) |
| WF-6 | FCAW con gas | E71T-1C/M, E71T-9C/M (A5.20/A5.36) |

### Ensayos requeridos — §6.4 (obligatorios TODOS)
1. **Visual (VT)** — §6.4.1
2. **Nick Break** — §6.4.2 — corte y examen de la fractura
3. **Root Bend** — §6.4.3 — doblez de raíz
4. Opcionalmente RT puede reemplazar nick break + bend bajo §6.5

### Diseño de junta — §7.4
Parámetros típicos para tuberías:
- Ángulo de bisel: 30° ± 2.5° (single-V es el estándar)
- Apertura de raíz: 1.6 mm ± 0.8 mm
- Cara de raíz (root face): 0 – 1.6 mm
No es variable esencial para calificación del soldador.

### Continuidad — §6.2
Similar a ASME IX: inactividad de 6 meses invalida la calificación.

---

## AWS D1.1 — Structural Welding Code (Steel)

### Grupos de metal base — Table 5.3 (prequalified)

| Grupo | Materiales | fy |
|---|---|---|
| Group I | A36, A53 Grade B, A500 Grade A/B | ≤ 36 ksi (250 MPa) |
| Group II | A441, A572 Gr.42/50, A588 | 42–65 ksi |
| Group III | A514 Gr.< 2.5in, A517, A709 Gr.100 | 46–70 ksi |
| Group IV | A514 Gr.≥ 2.5in | 90–100 ksi |

### Reglas de espesor — Table 6.11

| T ensayado | Mínimo calificado | Máximo calificado |
|---|---|---|
| T = 9.5 mm (3/8 in.) — limited | 3.2 mm (1/8 in.) | 19.0 mm (3/4 in.) |
| 9.5 ≤ T < 25.4 mm | 3.2 mm | 2T |
| T ≥ 25.4 mm (1 in.) | 3.2 mm | ilimitado |

### Posiciones calificadas — Table 6.10

| Posición ensayada | Posiciones calificadas (groove) |
|---|---|
| 1G | 1G |
| 2G | 1G, 2G |
| 3G | 1G, 2G, 3G |
| 4G | 1G, 2G, 4G |
| 3G + 4G | todas (1G, 2G, 3G, 4G) |
| 6G | todas |

### F-Groups — Table 6.13 (equivale a ASME F-Numbers)

| F | Electrodos | Equivale ASME |
|---|---|---|
| F1 | EXX20/24/27/28 | F-No. 1 |
| F2 | EXX12/13/14 | F-No. 2 |
| F3 | EXX10/11 | F-No. 3 |
| F4 | EXX15/16/18 | F-No. 4 |

Misma jerarquía que ASME: F4 califica F3/F2/F1.

### Ensayos requeridos — §6.5
- Plate: Visual + Bend (root + face, o side si t ≥ 10mm)
- RT puede sustituir bend tests

### Joint designs precalificados — Table 3
AWS D1.1 define juntas precalificadas con ángulos específicos:
- Single-V: 60° total (30° por lado)
- Single-bevel: 45°
- Double-V: 60° total
Fuera de estos, el WPS debe ser calificado con PQR.
Para calificación del soldador (Welder Qualification), el diseño de junta NO es variable esencial.

---

## Comparativa rápida entre normas

| Aspecto | ASME IX | AWS D1.1 | API 1104 |
|---|---|---|---|
| Alcance | Pressure vessels y piping | Structural steel | Onshore pipelines |
| Espesor máximo | 2t → unlimited con ≥3 capas | 2T → unlimited ≥ 25.4mm | max(19, 1.5t) → unlimited |
| Diámetro | 3 grupos (25.4/73mm) | No aplica (plate) | 3 grupos (60.3/323.9mm) |
| Consumibles | F-Numbers (1-6) | F-Groups (1-4) | WF-Groups (1-6) |
| Metal base | P-Numbers | Groups I-IV | SMYS directo |
| Nick Break | No requerido | No requerido | **Obligatorio** |
| 6 meses inactividad | QW-322.1 | Sí (análogo) | §6.2 |
| Joint design esencial | **No** (QW-402.1) | No | No |
| Posición tubo 45° | 6G → todas | 6G → todas | Fixed-45° → todas |

---

## Lógica del RangeCalculatorService

### Algoritmo general (por norma)

```
Para un certificado dado:
1. Leer coupon_type (plate/pipe)
2. Leer tested_thickness_mm
3. Buscar en thickness_rules WHERE standard_id = X
   AND coupon_type IN (coupon_type_certificado, 'both')
   AND thickness_from_mm <= tested_thickness_mm
   AND (thickness_to_mm IS NULL OR thickness_to_mm >= tested_thickness_mm)
   ORDER BY thickness_from_mm DESC, min_layers ASC
4. Evaluar qualifies_max_formula:
   - 'unlimited' → max = null
   - '2t' → max = 2 * tested_thickness_mm
   - 'max(X,Yt)' → max = max(X, Y * tested_thickness_mm)
5. Para pipe: ídem con diameter_rules
6. Posiciones: buscar qualified_positions WHERE standard_id = X
   AND tested_position_id = position_id del certificado
7. Guardar en certificate_ranges + actualizar ranges_calculated_at + rules_version
```

### Casos especiales conocidos

**ASME IX — múltiples reglas superpuestas:**
La regla "t ≥ 13mm con ≥3 capas → unlimited" tiene PRIORIDAD sobre "2t".
El servicio debe evaluar la regla más específica (con min_layers) primero.

**API 1104 — diámetro acumulativo:**
G3 califica G1+G2+G3 → max = unlimited.
G2 califica G1+G2 → max = 323.9mm.
G1 califica solo G1 → max = 60.3mm.
La lógica NO es "desde OD ensayado hasta max_formula" sino "desde 0 hasta max_formula del grupo".

**AWS D1.1 — limited qualification:**
T = 9.5mm exacto puede ser "limited" (max 19mm) o "normal" (max 2T = 19mm).
La diferencia: limited viene de una figura específica (6.20/6.21) vs figura 6.16/6.17.
En la práctica el resultado es igual; la distinción es documental.
