# GLOSARIO DE NORMAS — CALIFICACIÓN DE SOLDADORES
## Base de conocimiento comparativa: ASME IX · API 1104 · AWS D1.1 · NAG 100/105/201

> Generado: 2026-06-24 | Fuente: PDFs originales en `info/teoria/PADULA/`

---

## ÍNDICE

1. [Normas cubiertas y alcance](#1-normas-cubiertas-y-alcance)
2. [Procesos de soldadura reconocidos](#2-procesos-de-soldadura-reconocidos)
3. [Variables esenciales del soldador](#3-variables-esenciales-del-soldador)
4. [Posiciones de soldadura](#4-posiciones-de-soldadura)
5. [Espesores calificados](#5-espesores-calificados)
6. [Diámetros calificados](#6-diámetros-calificados)
7. [Materiales base — Grupos P y clasificaciones](#7-materiales-base--grupos-p-y-clasificaciones)
8. [Metales de aporte — Grupos F y clasificaciones](#8-metales-de-aporte--grupos-f-y-clasificaciones)
9. [Ensayos requeridos — tipo y cantidad](#9-ensayos-requeridos--tipo-y-cantidad)
10. [Criterios de aceptación por ensayo](#10-criterios-de-aceptación-por-ensayo)
11. [Vigencia y renovación de calificación](#11-vigencia-y-renovación-de-calificación)
12. [Formularios y registros obligatorios](#12-formularios-y-registros-obligatorios)
13. [Sistema de categorías NAG (Argentina)](#13-sistema-de-categorías-nag-argentina)
14. [Tabla comparativa cruzada](#14-tabla-comparativa-cruzada)

---

## 1. NORMAS CUBIERTAS Y ALCANCE

### ASME Sección IX
- **Edición de referencia:** 2013 (español); 2015, 2021, 2023 (inglés) — en carpeta PADULA
- **Alcance:** Calificación de procedimientos y habilidades de soldadores/operadores para calderas, recipientes a presión y cañerías de proceso en todo el mundo
- **Partes clave:** Artículo III (QW-300 a QW-399) = calificación de soldadores; Artículo IV (QW-400 a QW-499) = datos de variables
- **Distinción fundamental:** Calificación de **habilidades** (soldador) ≠ calificación de **procedimiento** (WPS/PQR)
- **Jerarquía de documentos:** WPS (Especificación de Procedimiento) → PQR (Registro de Calificación de Procedimiento) → WPQ/WPQR (Registro de Calificación del Soldador)

### API 1104
- **Edición de referencia:** Ed. 21ª (español, 2013/2014); Ed. 22ª (inglés, 2021) — en carpeta PADULA
- **Alcance:** Soldadura de oleoductos, gasoductos y facilidades relacionadas. Tubería de acero al carbono
- **Sección clave:** Sección 6 (calificación de soldadores); Sección 5 (calificación de procedimientos)
- **Particularidad:** El soldador que completa satisfactoriamente el ensayo de calificación de procedimiento puede ser considerado soldador calificado si se retiran y aprueban las muestras de la Sección 6.5

### AWS D1.1
- **Edición de referencia:** 2020 (inglés); 2015 (español) — en carpeta PADULA
- **Alcance:** Soldadura estructural de acero (no tubería de proceso)
- **Sección clave:** Cláusula 6 en D1.1:2020 (= Sección 4 en D1.1:2015)
- **Personal cubierto:** Soldadores, operarios de soldadura, soldadores punteadores

### NAG 100 (GE-N-100)
- **Año:** 1976 / actualizada — Gas del Estado Argentina / ENARGAS
- **Alcance:** Transporte y distribución de gas natural. Parte E: soldadura de acero en cañerías
- **Remite a:** API 1104 (alta presión ≥20% TFME) y NAG 105 (GE-N1-105) para calificación de soldadores

### NAG 105 (GE-N1-105)
- **Año:** 1980 + Modificación 1 de 1991 — Gas del Estado (Disposición Interna N° 1952)
- **Alcance:** Gasoductos, ramales, redes de distribución, plantas de tratamiento, compresoras, almacenaje GLP, instalaciones domiciliarias industriales — todo elemento bajo presión en industria del gas argentina
- **Sistema propio:** 4 categorías (A, B, C, D) con referencias a ASME IX y API 1104

### NAG 201
- **Año:** 1985 — Gas del Estado Argentina / ENARGAS
- **Alcance:** Instalaciones industriales de gas natural (entre línea municipal y equipos consumidores). Aplica también a redes de media presión con distribución interna > 19 mbar M.
- **Remite a:** ASME IX (Sección IX), API 1104 (Sección e), NAG 105 para categorías de soldadores

---

## 2. PROCESOS DE SOLDADURA RECONOCIDOS

| Proceso | Sigla | ASME IX | API 1104 | AWS D1.1 | NAG 105 |
|---------|-------|---------|----------|----------|---------|
| Arco metálico protegido (electrodo revestido) | SMAW | QW-353 | Sí | Sí | Cat. A, C, D |
| Arco de tungsteno en gas (TIG) | GTAW | QW-356 | Sí | Sí (requiere calif.) | Cat. A (semiaut.) |
| Arco metálico en gas (MIG) | GMAW | QW-355 | Sí | Sí | Cat. A (semiaut.) |
| Arco metálico en gas — cortocircuito | GMAW-S | QW-355 + QW-409.2 | Ed.22ª: var. esencial | Proceso separado | — |
| Arco con núcleo de fundente | FCAW | QW-355 | Sí | Sí | — |
| Arco sumergido | SAW | QW-354 | Sí | Sí | Cat. B |
| Oxigas / Oxiacetilénico | OFW | QW-352 | Sí (RG60/RG65) | — | — |
| Plasma | PAW | QW-357 | — | — | — |
| Electroescoria | ESW | QW-361 (operador) | — | Sí (requiere calif.) | — |
| Electrogas | EGW | QW-361 (operador) | — | Sí (requiere calif.) | — |

**Notas importantes:**
- **GMAW-S (cortocircuito):** En ASME IX, el modo de transferencia es variable esencial (QW-409.2); en API 1104 Ed. 22ª, el soldador calificado con GMAW-S solo puede usarlo en producción para las mismas pasadas que en calificación; en AWS D1.1, GMAW-S se considera **proceso separado** y no califica con RT
- **Procesos automáticos vs. manuales:** ASME IX distingue expresamente "soldador" (manual/semiautomático) de "operador de soldadura" (automático/máquina) con variables esenciales distintas (QW-350 vs QW-360)
- **NAG 105 Categoría B:** Solo operadores de soldadura por arco sumergido u otros procesos automáticos

---

## 3. VARIABLES ESENCIALES DEL SOLDADOR

> Un cambio en cualquiera de estas variables invalida la calificación y requiere nueva prueba.

### 3.1 ASME IX — Variables esenciales por proceso (QW-350)

| Variable | Código QW | OFW | SMAW | SAW | GMAW/FCAW | GTAW | PAW |
|----------|-----------|-----|------|-----|-----------|------|-----|
| Cambio en Número P del metal base | 403.18 | Sí | Sí | Sí | Sí | Sí | Sí |
| Cambio en Número F del metal de aporte | 404.15 | Sí | Sí | Sí | Sí | Sí | Sí |
| Adición de posición no calificada | 405.1 | Sí | Sí | Sí | Sí | Sí | Sí |
| Retiro del respaldo (backing) | 402.4 | — | Sí | — | Sí | Sí | Sí |
| Cambio en diámetro de cañería | 403.16 | — | Sí | Sí | Sí | Sí | Sí |
| Cambio en progresión vertical | 405.3 | — | Sí | — | Sí | Sí | Sí |
| ± Metal de aporte | 404.14 | Sí | — | — | — | Sí | Sí |
| Cambio en modo de transferencia | 409.2 | — | — | — | Sí (GMAW) | — | — |
| Cambio en corriente/polaridad | 409.4 | — | — | — | — | Sí | — |
| Tipo de gas combustible | 408.7 | Sí | — | — | — | — | — |
| Remoción gas de respaldo inerte | 408.8 | — | — | — | Sí | Sí | Sí |
| ± Insertos consumibles | 404.22 | — | — | — | — | Sí | Sí |
| Cambio en forma del metal de aporte | 404.23 | — | — | — | — | Sí | Sí |
| Límite espesor GMAW-S | 404.32 | — | — | — | Sí (GMAW) | — | — |
| Espesor máx. calificado (solo cupón) | 403.2 | Sí | — | — | — | — | — |
| Espesor del depósito metálico | 404.30 / 404.31 | 404.31 | 404.30 | 404.30 | 404.30 | 404.30 | 404.30 |

### 3.2 API 1104 Ed. 21ª — Variables esenciales del soldador (6.2.2)

| # | Variable | Descripción detallada |
|---|----------|-----------------------|
| a | Proceso de soldadura | Cambio a otro proceso o combinación |
| b | Dirección vertical | Ascendente ↔ Descendente |
| c | Grupo de metal de aporte | Cambio entre grupos Tabla 1 (ver Sección 8.1) |
| d | Grupo de diámetro exterior (OD) | Cambio entre Grupo 1/2/3 (ver Sección 6) |
| e | Grupo de espesor de pared | Cambio entre los 3 grupos (ver Sección 5) |
| f | Posición fija ↔ rotación | Cambio de rotación a fijo (no viceversa para descalificación) |
| g | Posición con eje a 45° | Califica todas las posiciones — cambio pierde esa amplitud |
| h | Diseño de junta | Eliminación de backing strip; cambio bisel V→U |

**API 1104 Ed. 22ª — Variables adicionales:**

| # | Variable nueva | Descripción |
|---|----------------|-------------|
| i | Diseño de junta (redefinido) | Cambio en diseño de junta (nota d de Tabla 1) |
| j | Backing strip | Eliminación de backing strip o metal de soldadura |
| k | Pasadas por proceso (multi-proceso) | Cambio en las pasadas soldadas cuando se usa más de un proceso; procesos con < 3 pasadas en test no pueden usarse para > 2 pasadas en producción |
| — | Dirección vertical expandida | También: cambio de vertical ↔ horizontal |

### 3.3 AWS D1.1 — Variables esenciales del soldador (Tabla 6.12)

| # | Variable | Soldadores | Operarios | Punteadores |
|---|----------|-----------|-----------|-------------|
| 1 | Cambio a proceso no calificado (GMAW-S = proceso separado) | X | X | X |
| 2 | Cambio a electrodo SMAW con F-number más ALTO | X | X | — |
| 3 | Cambio a posición no calificada | X | X | X |
| 4 | Cambio a diámetro/espesor no calificado | X | X | — |
| 5 | Cambio en progresión vertical (asc. ↔ desc.) | X | — | — |
| 6 | Omisión del respaldo (si se usó en WPQR) | X | X | — |
| 7 | Cambio a múltiples electrodos (si se usó uno solo) | — | X | — |

### 3.4 NAG 105 — Variables esenciales por categoría

| Variable | Cat. A/B (ASME IX) | Cat. C (API 1104) | Cat. D (EPS N°1) |
|----------|--------------------|-------------------|------------------|
| Proceso | Cambio = recalificación | Cambio = recalificación | SMAW electrodo revestido fijo |
| Posición | Ver rangos ASME IX | Toda posición | Caño fijo eje horizontal |
| Dirección | Asc./Desc. son variables esenciales | Asc./Desc. son variables esenciales | Descendente (cañerías) o Ascendente (Plantas Reg.) |
| Material base | N° P (cambio = recalif.) | Acero al carbono | Acero al carbono |
| Metal de aporte | N° F (cambio = recalif.) | Clasificación AWS (Tabla 1) | E-6010 (desc.) / E-6015 (asc.) |
| Diámetro | Según ASME IX QW-452 | < 50 mm / 50–305 mm / > 305 mm | Hasta 323,8 mm (12 3/4") |
| Espesor | Según ASME IX QW-452 | 3 grupos API (< 4,8 / 4,8–19,1 / > 19,1 mm) | Hasta 19 mm (3/4") |
| Respaldo | Con/sin = variable esencial | Con/sin = variable esencial | — |

---

## 4. POSICIONES DE SOLDADURA

### 4.1 ASME IX — Definiciones de posición (QW-461)

#### Ranura (Groove Weld)

| Posición | Símbolo | Inclinación del eje | Rotación de la cara |
|----------|---------|--------------------|--------------------|
| Plana | 1G / A | 0° a 15° | 150° a 210° |
| Horizontal | 2G / B | 0° a 15° | 80° a 150° o 210° a 280° |
| Vertical | 3G / D | 15° a 80° | 80° a 280° |
| Sobre la cabeza | 4G / C | 0° a 80° | 0° a 80° o 280° a 360° |
| Tubo fijo horizontal | 5G / E | 80° a 90° | 0° a 360° |
| Tubo fijo 45° | 6G | — | Eje inclinado 45° respecto a horizontal |

**Tolerancia:** ±15° en inclinación del eje y ±15° en rotación de la cara (QW-303.3)

#### Filete (Fillet Weld)

| Posición | Símbolo | Inclinación del eje | Rotación de la cara |
|----------|---------|--------------------|--------------------|
| Plana | 1F / A | 0° a 15° | 150° a 210° |
| Horizontal | 2F / B | 0° a 15° | 125° a 150° o 210° a 235° |
| Vertical | 3F / D | 15° a 80° | 125° a 235° |
| Sobre la cabeza | 4F / C | 0° a 80° | 0° a 125° o 235° a 360° |
| Tubo fijo | 5F / E | 80° a 90° | 0° a 360° |

### 4.2 ASME IX — Alcance de calificación por posición de ensayo (QW-461.9)

#### Ranura en tubo

| Cupón de ensayo | Califica posiciones (placa y tubo > 24"/610mm OD) | Califica posiciones (tubo ≤ 24"/610mm OD) | Califica filetes |
|-----------------|--------------------------------------------------|------------------------------------------|-----------------|
| 1G | F | F | F |
| 2G | F, H | F, H | F, H |
| 5G | F, V, O | F, V, O | Todas |
| 6G | Todas | Todas | Todas |
| 2G + 5G | Todas | Todas | Todas |

#### Ranura en plancha

| Cupón de ensayo | Califica posiciones (plancha y tubo > 24") | Califica filetes |
|-----------------|--------------------------------------------|-----------------|
| 1G | F | F |
| 2G | F, H | F, H |
| 3G | F, V | F, H, V |
| 4G | F, O | F, H, O |
| 3G + 4G | F, V, O | Todas |
| 2G + 3G + 4G | Todas | Todas |

**Regla clave ASME IX:** Calificación en ranura → califica filetes de CUALQUIER tamaño, TODOS espesores, TODOS diámetros (QW-303.1)

### 4.3 API 1104 — Posiciones y alcance de calificación

| Posición de ensayo | Posiciones calificadas |
|-------------------|----------------------|
| Fija, eje horizontal | Horizontal fija + rotación horizontal |
| Fija, eje vertical | Vertical fija |
| Fija, eje inclinado ≤ 45° | **TODAS las posiciones** (la más amplia) |
| Rotada | Solo rotación |

**Calificación múltiple (6.3) — OD ≥ 12,750 pulg. (323,9 mm):**
- Butt weld + Branch connection con OD ≥ 12,750 pulg. → Califica: **TODAS las posiciones, TODOS espesores, TODOS diseños de junta, TODOS diámetros**

**API 1104 Ed. 22ª — Posiciones para conexiones de derivación (Tabla 6):**

| Posición del branch en ensayo | Relación Ø branch / Ø run | Posiciones calificadas | Ángulo diedro calificado |
|------------------------------|--------------------------|----------------------|--------------------------|
| Top (arriba) | < 0,75 | Flat | 75°–90° |
| Top (arriba) | ≥ 0,75 | Flat, Vertical | 0°–90° |
| Side (costado) | Todas | Flat, Vertical, Overhead | 0°–90° |
| Bottom (abajo) | < 0,75 | Overhead, Flat | 75°–90° |
| Bottom (abajo) | ≥ 0,75 | Flat, Vertical, Overhead | 0°–90° |

**Definición API 1104 de posición en circunferencia:**
- Flat: 0° a ~15° (12:00 a 12:30)
- Vertical: ~15° a 105° (12:30 a 3:30)
- Overhead: ~105° a 180° (3:30 a 6:00)

### 4.4 AWS D1.1 — Posiciones calificadas (Tabla 6.10)

| Ensayo | Posición | Califica placa | Califica tubería |
|--------|----------|---------------|-----------------|
| Ranura | 1G | F | F |
| Ranura | 2G | F, H | F, H |
| Ranura | 3G | F, H, V | F, H, V |
| Ranura | 4G | F, OH | F, OH |
| Ranura | 3G + 4G | ALL | ALL |
| Filete | 1F | F | F |
| Filete | 2F | F, H | F, H |
| Filete | 3F | F, H, V | F, H, V |
| Filete | 4F | F, H, OH | F, H, OH |
| Filete | 3F + 4F | ALL | ALL |

**Tubulares AWS D1.1 (Cláusula 10):**

| Posición | Descripción |
|----------|-------------|
| 1G | Tubo horizontal, rotando — soldadura plana |
| 2G | Tubo vertical, sin rotar — soldadura horizontal |
| 5G | Tubo horizontal, sin rotar — soldadura en todas las posiciones |
| 6G | Tubo inclinado 45°, sin rotar — califica todas las posiciones |
| 6GR | 6G con restricción (obstrucción física) |

### 4.5 NAG 105 — Posiciones por categoría

**Categorías A y B (según ASME IX):**

| Posición aprobada | Califica para |
|-------------------|--------------|
| 2G, 3G o 4G | Califica 1G |
| 5G | Califica 1G, 3G, 4G |
| 2G + 5G | Califica TODAS las posiciones |
| 6G | Califica TODAS las posiciones |

**Relación chapa/caño (NAG 105):**
- Calificación en chapa califica caño solo en posiciones 1G y 2G (con/sin respaldo)
- Calificación en caño SIEMPRE califica chapa
- **Excepción:** calificación en chapa califica caños de > 609,6 mm (24") de diámetro

**Categoría C:** Todas las posiciones (eje fijo horizontal, vertical, inclinado 45°)

**Categoría D:** Posición fija, eje horizontal (caño horizontal fijo)

---

## 5. ESPESORES CALIFICADOS

### 5.1 ASME IX — Rangos de espesor calificado (QW-452)

#### Para soldaduras de ranura en plancha (QW-452.1)

| Espesor metal depositado (t) del cupón | Espesor calificado para producción |
|---------------------------------------|-----------------------------------|
| Cualquier t | Hasta **2t** (doble del cupón) |
| ≥ 1/2" (13 mm) con mínimo 3 capas | **Ilimitado** |

#### Muestras de doblez requeridas (QW-452.1a)

| Espesor metal depositado (t) | Doblez de lado | Doblez de cara | Doblez de raíz |
|-----------------------------|----------------|----------------|----------------|
| < 3/8" (10 mm) | Ninguno | 1 | 1 |
| 3/8" a < 3/4" (10–19 mm) | 2 (o 1 cara + 1 raíz) | — | — |
| ≥ 3/4" (19 mm) | 2 | Ninguno | Ninguno |

**Para tubos en posiciones 5G o 6G:** total 4 muestras de doblez
**Para combinación 2G + 5G:** total 6 muestras de doblez

### 5.2 API 1104 — Grupos de espesor

**Ed. 21ª (3 grupos fijos):**

| Grupo | Espesor de pared especificado |
|-------|-------------------------------|
| Grupo 1 | < 0,188 pulg. (4,8 mm) |
| Grupo 2 | 0,188 pulg. a 0,750 pulg. (4,8–19,1 mm) |
| Grupo 3 | > 0,750 pulg. (19,1 mm) |

**Ed. 22ª (rangos proporcionales — Tabla 5):**

| Espesor ensayado (t) | Rango calificado |
|----------------------|-----------------|
| t < 0,154 pulg. (3,9 mm) | t hasta máx. de: 0,154 pulg. (3,9 mm) ó 1,5t |
| 0,154 pulg. ≤ t < 0,75 pulg. (3,9–19 mm) | 0,154 pulg. hasta máx. de: 0,75 pulg. (19 mm) ó 1,5t |
| t ≥ 0,75 pulg. (19 mm) | 0,75 pulg. (19 mm) hasta **ilimitado** |

### 5.3 AWS D1.1 — Rangos de espesor calificado (Tabla 6.11)

| Espesor placa de ensayo T | Tipo de doblez | Espesor calificado mínimo | Espesor calificado máximo |
|--------------------------|----------------|--------------------------|--------------------------|
| 3/8" (10 mm) | Face + Root bend (1+1) | 1/8" (3 mm) | 3/4" (20 mm) máx. |
| 3/8" < T < 1" (10–25 mm) | Side bend (2) | 1/8" (3 mm) | 2T |
| ≥ 1" (25 mm) | Side bend (2) | 1/8" (3 mm) | Ilimitado |

**Recalificación por lapso:** Usar placa de 3/8" (10 mm) → califica espesores ≥ 1/8" (3 mm)

### 5.4 NAG 105 — Límites de espesor

| Categoría | Espesor máximo calificado |
|-----------|--------------------------|
| A | Según ASME IX (QW-452) |
| B | Según ASME IX (QW-452) |
| C | Según API 1104 (grupos Ed. 21ª) |
| D | Hasta **19 mm (3/4")** (EPS N°1) |

---

## 6. DIÁMETROS CALIFICADOS

### 6.1 ASME IX — Rangos de OD calificado (QW-452.3)

| OD del cupón de ensayo | OD mínimo calificado | OD máximo calificado |
|------------------------|---------------------|---------------------|
| < 1" (25,4 mm) | Mismo OD del cupón | Ilimitado |
| 1" a 2-7/8" (25,4–73 mm) | 1" (25,4 mm) | Ilimitado |
| > 2-7/8" (73 mm) | 2-7/8" (73 mm) | Ilimitado |

**Nota:** 2-7/8" OD ≡ NPS 2-1/2" (DN 65)

**Para filetes en tubo de diámetro pequeño (QW-452.4 y 452.5):**
- Para OD ≥ 2-7/8" (73 mm): califica **todos los espesores, todos los tamaños de filete**
- Para OD < 2-7/8" (73 mm): solo hasta 2T del metal base, filete máx. = T

**Calificación de ranura → califica filete para CUALQUIER OD (QW-452.6)**

### 6.2 API 1104 — Grupos de diámetro exterior

| Grupo | Rango OD |
|-------|---------|
| Grupo 1 | OD < 2,375 pulg. (60,3 mm) |
| Grupo 2 | OD 2,375 pulg. a 12,750 pulg. (60,3–323,9 mm) |
| Grupo 3 | OD > 12,750 pulg. (323,9 mm) |

**Alcance por grupo de ensayo:**
- Calificación en Grupo 1 → solo Grupo 1
- Calificación en Grupo 2 → Grupos 1 y 2
- Calificación en Grupo 3 → Todos los grupos

**Calificación múltiple con OD ≥ 12,750 pulg. (butt + branch):** todos los diámetros

**Para conexiones de derivación:** el diámetro de la tubería de ejecución (run pipe) NO es variable esencial

### 6.3 AWS D1.1 — Diámetros calificados

- Las posiciones de Tabla 6.10 que especifican "tubería" aplican para tubería ≥ 24" (600 mm) OD con respaldo o retroceso
- Sin restricción de diámetro mínimo para placa
- Para tubulares < 24" OD: ver Tabla 10.12 de Cláusula 10

### 6.4 NAG 105 — Rangos de diámetro por categoría

| Categoría | Diámetro máximo | Alcance de calificación (subrangos) |
|-----------|-----------------|-------------------------------------|
| C | Sin límite especificado | < 50,8 mm (2") / 50,8–304,8 mm (2"–12") / > 304,8 mm (12") |
| D | 323,8 mm (12 3/4") | Ídem a categoría C pero con límite máximo |

**Condición adicional Categoría D:** La presión NO debe producir tensión ≥ 20% SMYS

*Fórmula:* `i = (P × D × 100) / (2 × t × S)` donde i debe ser < 20%

Ejemplo verificación: P=25 kg/cm²M, D=219,1 mm, t=5,56 mm, S=2110 kg/cm² → i=23,34% → **NO puede soldar un soldador Cat. D**

---

## 7. MATERIALES BASE — GRUPOS P Y CLASIFICACIONES

### 7.1 ASME IX — Números P (QW-420)

| N° P | Material |
|------|---------|
| P-No. 1 a 15E | Aceros al carbono y aleados |
| P-No. 21 a 26 | Aluminio y aleaciones |
| P-No. 31 a 35 | Cobre y aleaciones |
| P-No. 41 a 49 | Níquel y aleaciones |
| P-No. 51 a 53 | Titanio y aleaciones |
| P-No. 61 a 62 | Zirconio y aleaciones |

### 7.2 ASME IX — Calificaciones cruzadas entre P-Numbers (QW-423)

| Calificado con | Califica para producción en |
|---------------|----------------------------|
| P-No. 1 a 15E, P-No. 34, o P-No. 41–49 | P-No. 1–15E, P-No. 34, P-No. 41–49 |
| P-No. 21 a 26 | P-No. 21–26 |
| P-No. 51–53 o P-No. 61 o P-No. 62 | P-No. 51–53, 61, 62 |

**Restricciones de calificación por END (QW-304):**
- NO permitido END para P-No. 21–26, 51–53, 61–62 (deben usar ensayos mecánicos)
- **Excepción:** P-No. 21–26 y 51–53 **con proceso GTAW** sí pueden usar END volumétrico

### 7.3 API 1104 — Grupos de material base (5.4.2.2)

| Grupo | Condición de SMYS |
|-------|------------------|
| Grupo A (bajo) | SMYS ≤ SMYS de API 5L Grado X42 |
| Grupo B (medio) | SMYS > X42 pero < X65 |
| Grupo C (alto) | SMYS ≥ X65 → cada grado requiere calificación separada |

**Regla:** Al soldar materiales de dos grupos diferentes, se usa el procedimiento del grupo de mayor resistencia

### 7.4 AWS D1.1 — Grupos de acero (Tabla 5.3 / 6.8)

| Grupo | Descripción general |
|-------|---------------------|
| Grupo I | Aceros de resistencia baja a media |
| Grupo II | Aceros de mayor resistencia |
| Grupo III | Aceros de alta resistencia (quenched and tempered) |
| Grupo IV | Aceros de muy alta resistencia |

**Alcance de calificación por grupos (Tabla 6.8):**
- Grupo I + Grupo I → califica Grupo I + Grupo I
- Grupo II + Grupo II → califica todo lo anterior + Grupo II + Grupo II
- Acero específico Grupo III + Grupo I → solo ese acero + Grupo I
- Grupos III o IV combinados → solo la combinación específica del PQR

### 7.5 NAG 105 / NAG 201 — Materiales habilitados

**Caños de acero (NAG 201):** API 5L, API 5LX, API 5LS, ASTM A 53
**Accesorios:** IRAM 2607 o ANSI B 16.9 y B 16.28 — acero al carbono grados A o B

---

## 8. METALES DE APORTE — GRUPOS F Y CLASIFICACIONES

### 8.1 ASME IX — Números F (QW-432)

| F-No. | Tipo | Especificaciones/clasificaciones clave |
|-------|------|----------------------------------------|
| F-1 | Acero — especiales | SFA-5.1: EXX20, EXX22, EXX24, EXX27, EXX28; SFA-5.5: EXX20-X, EXX27-X |
| F-2 | Acero — rutílicos | SFA-5.1: EXX12, EXX13, EXX14, EXX19 |
| F-3 | Acero — celulósicos | SFA-5.1: EXX10, EXX11; SFA-5.5: EXX10-X, EXX11-X |
| F-4 | Acero — bajo hidrógeno | SFA-5.1: EXX15, EXX16, EXX18, EXX18M, EXX48; SFA-5.4: EXXX-15, -16, -17; SFA-5.5: EXX15-X, -16-X, -18-X |
| F-5 | Acero — inoxidable (austenít./dúplex) | SFA-5.4: austeníticos/dúplex EXXX15, -16, -17 |
| F-6 | Todos los alambres/tubulares de acero | SFA-5.2, 5.9, 5.17, 5.18, 5.20, 5.22, 5.23, 5.25, 5.26, 5.28, 5.29, 5.30 |
| F-21 a 25 | Aluminio | SFA-5.3 (electrodos), SFA-5.10 (alambres) |
| F-31 a 37 | Cobre | SFA-5.6 (electrodos), SFA-5.7 (alambres) |
| F-41 a 46 | Níquel | SFA-5.11, SFA-5.14, SFA-5.30 |
| F-51 a 56 | Titanio | SFA-5.16 |
| F-61 | Zirconio | SFA-5.24 |
| F-71 a 72 | Recubrimiento duro | SFA-5.13 (electrodos), SFA-5.21 (alambres) |

### 8.2 ASME IX — Calificaciones cruzadas por F-Number (QW-433)

| Calificado con | Con respaldo → califica para | Sin respaldo → califica para |
|---------------|------------------------------|------------------------------|
| F-1 | F-1, 2, 3, 4, 5 | Solo F-1 |
| F-2 | F-2, 3, 4, 5 | Solo F-2 |
| F-3 | F-3, 4, 5 | Solo F-3 |
| F-4 | F-4, 5 | Solo F-4 |
| F-5 | Solo F-5 | Solo F-5 |
| F-6 | Todos los F-6 | Todos los F-6 |
| F-21 a 25 (Al) | Todos F-21 a 25 | Todos F-21 a 25 |
| F-41 a 46 (Ni) | F-34 + todos F-41 a 46 | F-34 + todos F-41 a 46 |
| F-51 a 55 (Ti) | Todos F-51 a 55 | Todos F-51 a 55 |

**Regla mnemotécnica ASME:** F-número más bajo = más amplia cobertura. Sin respaldo = sin sustitución.

### 8.3 API 1104 Ed. 21ª — Grupos de metal de aporte (Tabla 1)

| Grupo | Proceso | Especif. AWS | Clasificaciones |
|-------|---------|-------------|-----------------|
| 1 | SMAW | A5.1 / A5.5 | E6010, E6011, E7010, E7011 |
| 2 | SMAW | A5.5 | E8010, E8011, E9010 |
| 3 | SMAW | A5.1 / A5.5 | E7015, E7016, E7018, E8015, E8016, E8018, E9018 |
| 4 | SAW | A5.17 | EL8, EL8K, EL12, EM5K, EM12K, EM13K, EM15K + fundentes F6X0, F7X0, etc. |
| 5 | GMAW/GTAW | A5.18 / A5.28 | ER70S-2, ER70S-6, ER80S-D2, ER90S-G (requiere gas de protección) |
| 6 | OFW | A5.2 | RG60, RG65 |
| 7 | FCAW | A5.20 | E61T-GS, E71T-GS (solo pasada de raíz) |
| 8 | FCAW | A5.29 | E71T8-K6 |
| 9 | FCAW | A5.29 | E91T8-G |

**Regla de cambio:** Cambio Grupo 1/2 → cualquier otro = variable esencial; Grupo 3–9 → Grupo 1/2 = variable esencial

### 8.4 API 1104 Ed. 22ª — Grupos de metal de aporte para soldadores (Tabla 4: WF-1 a WF-6)

| Grupo | Proceso | Descripción | Clasificaciones clave |
|-------|---------|-------------|----------------------|
| WF-1 | SMAW | Electrodos celulósicos | EXX10-X(X), EXX11-X(X) (A5.1 o A5.5) |
| WF-2 | SMAW | Bajo hidrógeno | Cualquier EXX15, EXX16, EXX18 (A5.1 o A5.5) |
| WF-3 | SMAW | Bajo hidrógeno vertical descendente | E8045-XX, E9045-XX, E10045-XX (A5.5) |
| WF-4 | GMAW/GTAW | Alambres sólidos (requiere gas) | ERXXS-X; ERXX(X)S-XX(X) (A5.18, A5.28) |
| WF-5 | OFW | Varillas de oxigas | RG60, RG65 (A5.2) |
| WF-6 | FCAW | Alambres tubulares (requiere gas) | EXXT-1C/M, EXXT-9C/M; E(X)XX-1XX(X)M (A5.20, A5.29, A5.36) |

### 8.5 AWS D1.1 — Grupos F para SMAW (Tabla 6.13)

| Grupo | Clasificaciones |
|-------|----------------|
| F-4 | EXX15, EXX16, EXX18, EXX48, -X variantes (bajo hidrógeno) |
| F-3 | EXX10, EXX11, -X variantes (celulósicos) |
| F-2 | EXX12, EXX13, EXX14, -X variantes (rutílicos) |
| F-1 | EXX20, EXX24, EXX27, EXX28, -X variantes |

**Regla AWS D1.1:** F-number más alto califica F-numbers más bajos. Cambio a F-number MÁS ALTO = variable esencial.

### 8.6 NAG 105 Categoría D — Metal de aporte específico (EPS N°1)

| Dirección de soldadura | Electrodo | Diámetros | Tensión (V) | Intensidad (A) |
|-----------------------|-----------|-----------|------------|----------------|
| Descendente | E-6010 (celulósico sódico) | ø 3,2 mm | 24–26 V | 90–130 A |
| Descendente | E-6010 | ø 3,96 mm | 28 V | 120–160 A |
| Descendente | E-6010 | ø 4,76 mm | 28–30 V | 140–220 A |
| Ascendente | E-6015 (básico) | ø 3,2 mm | 22–24 V | 110–130 A |
| Ascendente | E-6015 | ø 3,96 mm | 24–26 V | 135–200 A |
| Ascendente | E-6015 | ø 4,76 mm | 24–26 V | 160–240 A |

**Fuente:** Corriente continua, polaridad inversa (electrodo al +)

---

## 9. ENSAYOS REQUERIDOS — TIPO Y CANTIDAD

### 9.1 ASME IX — Ensayos para calificación de soldador

**Para ranura en plancha — Muestras de doblez (QW-452.1a):**

| Espesor metal depositado (t) | Doblez cara | Doblez raíz | Doblez lateral |
|-----------------------------|-------------|-------------|----------------|
| < 3/8" (10 mm) | 1 | 1 | — |
| 3/8" a < 3/4" (10–19 mm) | — | — | 2 (o 1 cara + 1 raíz) |
| ≥ 3/4" (19 mm) | — | — | 2 |

**Para ranura en tubo — Muestras de doblez (QW-452.3):**
- 1G o 2G: 2 muestras de doblez
- 5G o 6G: 4 muestras de doblez
- 2G + 5G combinado: 6 muestras de doblez

**Inspección visual:** obligatoria para TODAS las superficies antes de cortar (QW-302.4)

**Examinación volumétrica como alternativa (QW-304):**
- Mínimo 6" (150 mm) de soldadura examinada por RT o UT (QW-191)
- Para posiciones 5G/6G: circunferencia completa
- Diámetros pequeños: máximo 4 circunferencias consecutivas
- No permitida para: P-No. 21–26, 51–53, 61–62 (excepto GTAW), ni para GMAW-S con RT

**Para filetes (QW-452.5):**
- 1 muestra de macro-ataque + 1 muestra de fractura
- Para OD ≥ 2-7/8" (73 mm) o espesor ≥ 3/16" (5 mm)

### 9.2 API 1104 — Ensayos para calificación de soldador

**Tabla de tipo y número de muestras (Tabla 3 Ed. 21ª / Tabla 7 Ed. 22ª):**

#### Ed. 21ª (Español) — Para espesores de pared ≤ 0,500 pulg. (12,7 mm):

| OD | Tracción | Mella (NB) | Doblez raíz | Doblez cara | Doblez lateral | Total |
|----|----------|-----------|-------------|-------------|----------------|-------|
| < 2,375 pulg. (60,3 mm) | 0 | 2 | 2 | 0 | 0 | 4 |
| 2,375 a 4,500 pulg. (60,3–114,3 mm) | 0 | 2 | 2 | 0 | 0 | 4 |
| > 4,500 a 12,750 pulg. (>114,3–323,9 mm) | 2 | 2 | 2 | 0 | 0 | 6 |
| > 12,750 pulg. (>323,9 mm) | 4 | 4 | 2 | 2 | 0 | 12 |

#### Ed. 21ª — Para espesores de pared > 0,500 pulg. (12,7 mm):

| OD | Tracción | Mella (NB) | Doblez raíz | Doblez cara | Doblez lateral | Total |
|----|----------|-----------|-------------|-------------|----------------|-------|
| ≤ 4,500 pulg. (≤114,3 mm) | 0 | 2 | 0 | 0 | 2 | 4 |
| > 4,500 a 12,750 pulg. | 2 | 2 | 0 | 0 | 2 | 6 |
| > 12,750 pulg. | 4 | 4 | 0 | 0 | 4 | 12 |

#### Ed. 22ª (Inglés) — Para espesores ≤ 0,500 pulg.:

| OD | NB | Root Bend | Face Bend | Side Bend | Total |
|----|-----|-----------|-----------|-----------|-------|
| < 2,375 pulg. | 2 | 2 | 0 | 0 | 4 |
| 2,375 a 4,500 pulg. | 2 | 2 | 0 | 0 | 4 |
| > 4,500 a 12,750 pulg. | 2 | 2 | 0 | 0 | **4** ← reducción vs. Ed. 21ª |
| > 12,750 pulg. | 4 | 2 | 2 | 0 | **8** ← reducción vs. Ed. 21ª |

**Nota sobre tracción:** Para calificación del soldador, el ensayo de tracción puede omitirse — en ese caso las muestras asignadas a tracción se someten a ensayo de mella (Ed. 21ª). Ed. 22ª: elimina la tracción de la tabla de soldadores para diámetros medios.

**Para conexiones de derivación (6.3):** 4 muestras de mella en ubicaciones de Figura 10

**NDT como alternativa (6.6):**
- La compañía puede examinar la soldadura de calificación por RT o AUT en lugar de ensayos destructivos
- Si se usa NDT: se toman radiografías (o AUT) de cada soldadura de ensayo
- Ed. 22ª: si la compañía usa TANTO NDT como destructivos, el fallo en cualquiera = fallo total
- **Prohibición:** No usar NDT para localizar áreas sólidas y luego ensayar solo esas áreas

### 9.3 AWS D1.1 — Ensayos para calificación de soldador (Tabla 6.11)

| Cupón de ensayo | Tipo de junta | Espesor T | Cara | Raíz | Lado | Macro | Total |
|-----------------|---------------|----------|------|------|------|-------|-------|
| Placa | Ranura limit. | 3/8" (10 mm) | 1 | 1 | — | — | 2 |
| Placa | Ranura ilimit. | 3/8" < T < 1" | — | — | 2 | — | 2 |
| Placa | Ranura ilimit. | ≥ 1" (25 mm) | — | — | 2 | — | 2 |
| Placa | Filete Op.1 | 1/2" (12 mm) | — | — | — | 1+1 fractura | 2 |
| Placa | Filete Op.2 | 3/8" (10 mm) | — | — | — | 2 macro | 2 |
| ESW/EGW | Ranura | < 1-1/2" (38 mm) | — | — | 2 | — | 2 |

**RT en lugar de doblez (6.17.1.1):**
- Válido excepto para GMAW-S
- Operario: primeros 15" (380 mm) de soldadura de producción
- Excluir 1-1/4" (32 mm) en cada extremo (soldador) o 3" (75 mm) (operario)

**Soldadores punteadores:**
- Cordón punteo máx. 1/4" (6 mm), aprox. 2" (50 mm) de longitud
- Ensayo de rotura de filete (Figura 6.27)

### 9.4 NAG 105 Categoría D — Ensayos requeridos (EPS N°1)

**Tiempo máximo de prueba:** 3 horas desde entrega de niples al postulante

**Opción 1 — Caños 51 mm (2") ø, 4,37 mm espesor:**
- 2 soldaduras → 4 probetas:
  - 2 probetas: **doblado guiado de raíz**
  - 2 probetas: **ensayo de entalladura (nick-break)**

**Opción 2 — Niples 152 mm (6") ø, 6,35 mm espesor:**
- 2 niples → 6 probetas:
  - 2 probetas: **tracción**
  - 2 probetas: **entalladura**
  - 2 probetas: **doblado guiado de raíz**

**Prueba adicional obligatoria (derivación):**
- 2 caños de distintos diámetros; relación diámetros ≥ 50%; ejes a 90°; posición horizontal
- Evaluación: sin socavadura, fusión completa, sin penetración incompleta

### 9.5 NAG 100 Apéndice C — Ensayos baja presión (< 20% TFME)

**Sección I (Prueba básica):**
- Caño horizontal fijo, ≤ 300 mm (12") ø
- **4 probetas** de curvado de raíz (cuadrantes)

**Sección II (conexiones de servicio a cañería principal):**
- Accesorio de conexión soldado a caño del mismo diámetro
- Ensayo de rotura (break test)

**Sección III (líneas de servicio ≤ 2" / 50 mm, ensayos periódicos):**
- 2 muestras de 200 mm de longitud (soldadura al centro)
- Ensayo 1: doblado guiado alrededor de cuña, 50 mm de cada lado de soldadura
- Ensayo 2: tracción de sección completa (o segundo doblado si no hay máquina)

---

## 10. CRITERIOS DE ACEPTACIÓN POR ENSAYO

### 10.1 ASME IX

**Inspección visual (QW-302.4):**
- Todas las superficies antes de cortar muestras
- Tubo: interior y exterior de la circunferencia completa
- Grietas, porosidad visible, falta de fusión → FALLA

**Ensayo de doblez guiado (QW-160):**
- Cara, raíz o lateral según corresponda
- Sin grietas > 1/8" (3 mm) en cualquier dirección después del doblez completo

**Macro-ataque (QW-462.4 / QW-183):**
- Fusión completa en raíz del filete
- Metal de soldadura y HAZ libres de grietas

**Examinación volumétrica (QW-191):**
- RT: per QW-191.1
- UT: per QW-191.2

### 10.2 API 1104

**Inspección visual (6.4):**

| Parámetro | Límite de aceptación |
|-----------|---------------------|
| Grietas | No se permiten |
| Penetración inadecuada | No se permite |
| Socavado externo — profundidad máx. | 1/32 pulg. (0,8 mm) ó 12,5% espesor de pared (el menor) |
| Socavado externo — longitud máx. | No más de 2 pulg. (50 mm) en cualquier longitud continua de 12 pulg. (300 mm) |
| Burn-through (BT) en soldaduras a tope | Ed. 22ª: **cero permitidas** |
| BT en conexiones de derivación — individual | Máx. 1/4 pulg. (6 mm) |
| BT en branch — suma en 12 pulg. | Máx. 1/2 pulg. (13 mm) |

**Ensayo de mella (Nick Break) — Criterios (5.6.3.3):**

| Parámetro | Límite |
|-----------|--------|
| Penetración y fusión | Completas en superficies expuestas |
| Bolsas de gas — dimensión máxima | ≤ 1/16 pulg. (1,6 mm) |
| Bolsas de gas — área combinada | ≤ 2% de la superficie expuesta |
| Inclusiones de escoria — profundidad | ≤ 1/32 pulg. (0,8 mm) |
| Inclusiones de escoria — longitud | ≤ 1/8 pulg. (3 mm) ó mitad del espesor, el menor |
| Separación entre inclusiones adyacentes | Al menos 1/2 pulg. (13 mm) |
| Ojos de pescado (fisheyes) | No son motivo de rechazo |
| Ancho mínimo área expuesta de fractura | 3/4 pulg. (19 mm) |

**Ensayo de doblado — Criterios (5.6.4.3):**

| Parámetro | Límite |
|-----------|--------|
| Grietas/imperfecciones individuales | ≤ 1/8 pulg. (3 mm) ó mitad del espesor, el menor |
| Grietas en radio exterior del borde | < 1/4 pulg. (6 mm) no se consideran (salvo discontinuidades obvias) |
| Muestra de reemplazo (OD > 12,750 pulg.) | Si falla UNA muestra: 2 muestras adicionales de posiciones adyacentes; si ambas fallan = inaceptable |

**Dimensiones de plantilla de doblado guiado (Figura 8):**
- Radio del émbolo (A): 1¾ pulg. (45 mm)
- Radio del troquel (B): 2⅛ pulg. (60 mm)
- Anchura del troquel (C): 2 pulg. (50 mm)

**Ensayo de tracción — Criterios (5.6.2.3):**

| Condición | Resultado |
|-----------|---------|
| Rompe fuera de soldadura/fusión, resist. ≥ SMTS | Cumple |
| Rompe fuera de soldadura y HAZ, resist. ≥ 95% SMTS | Cumple |
| Rompe en soldadura, resist. ≥ SMTS Y cumple 5.6.3.3 | Cumple |
| Rompe en soldadura, resist. < SMTS | Inaceptable |

**Criterios de aceptación radiográfica (Sección 9.3) — para calificación por NDT:**

| Tipo de discontinuidad | Criterio de defecto |
|------------------------|---------------------|
| Penetración inadecuada (IP) | Individual > 1 pulg. (25 mm) O suma en 12 pulg. > 1 pulg. O suma > 8% longitud soldadura |
| Fusión incompleta (IF) | Individual > 1 pulg. O suma en 12 pulg. > 1 pulg. O suma > 8% longitud |
| Escoria alargada (ESI) | Longitud > 2 pulg. (50 mm) O suma en 12 pulg. > 2 pulg. O ancho > 1/16 pulg. (1,6 mm) |
| Escoria aislada (ISI) | Suma en 12 pulg. > 1/2 pulg. (13 mm) O ancho > 1/8 pulg. (3 mm) O más de 4 ISI en 12 pulg. |
| Porosidad — poro individual | Diámetro > 1/8 pulg. (3 mm) ó 25% espesor, el menor |
| Grietas | Cualquier grieta (excepto cráter/estrella ≤ 5/32 pulg. / 4 mm) |
| Concavidad interna (IC) | Densidad radiográfica > material adyacente más delgado |

**Criterios de socavado por tabla (Ed. 22ª — Tabla 8 / 9.7):**

| Profundidad del socavado | Longitud aceptable |
|--------------------------|-------------------|
| > 1/32 pulg. (0,8 mm) ó > 12,5% espesor | No aceptable (ninguna longitud) |
| > 1/64 pulg. pero ≤ 1/32 pulg. ó > 6% pero ≤ 12,5% espesor | Máx. 2 pulg. (50 mm) en 12 pulg. (300 mm) continuas |
| ≤ 1/64 pulg. (0,4 mm) ó ≤ 6% espesor | Aceptable a cualquier longitud |

### 10.3 AWS D1.1

**Inspección visual — soldaduras en ranura (6.10.1 / 6.23.1):**

| Parámetro | Valor límite |
|-----------|------------|
| Grietas | Cualquier grieta = inaceptable (sin importar tamaño) |
| Cráteres | Deben llenarse hasta sección completa |
| Refuerzo máximo | 1/8 pulg. (3 mm) |
| Socavación máxima | 1/32 pulg. (1 mm) |
| Concavidad de raíz (CJP un solo lado) | Máx. 1/16 pulg. (2 mm), siempre que espesor total ≥ metal base |
| Sobrepenetración máxima (CJP) | 1/8 pulg. (3 mm) |

**Inspección visual — soldaduras en filete:**
- Grietas: inaceptables
- Piernas de filete: no inferiores a las requeridas
- Socavación máxima: 1/32 pulg. (1 mm)

**Ensayo de doblez guiado (6.10.3.3):**

| Parámetro | Valor límite |
|-----------|------------|
| Discontinuidad individual (cualquier dirección) | Máx. 1/8 pulg. (3 mm) |
| Suma de discontinuidades (1/32 a 1/8 pulg.) | Máx. 3/8 pulg. (10 mm) total |
| Grieta de esquina máxima | Máx. 1/4 pulg. (6 mm) — si no hay inclusiones; si hay: aplica límite de 1/8 pulg. |

**Ensayo de rotura de filete (6.23.4):**

| Criterio | Valor |
|---------|-------|
| Aceptación por doblado | Si probeta se dobla plana sobre sí misma |
| Fusión hasta raíz | Requerida |
| Inclusión/porosidad individual máxima | 3/32 pulg. (2,5 mm) |
| Suma de inclusiones/porosidad (en 6 pulg.) | Máx. 3/8 pulg. (10 mm) |

**Ensayo de macrografía (6.23.2):**
- Filetes: fusión hasta raíz + pierna mínima conforme + sin grietas
- Tapones: sin grietas + fusión completa al respaldo + escoria acumulada ≤ 1/4 pulg. (6 mm)
- PJP: tamaño real ≥ tamaño especificado S

**Ensayo CVN (Charpy) — Parte D (6.26–6.29):**
- 3 probetas (o 5: descartando máxima y mínima)
- Probeta tamaño completo: 10 mm × 10 mm, cuando cupón ≥ 7/16 pulg. (11 mm)
- Sub-tamaño: cuando cupón < 7/16 pulg. (11 mm) — el mayor tamaño sub-estándar posible
- Si no se cumplen criterios: retest con 3 probetas adicionales del mismo cupón — cada una debe cumplir el criterio mínimo promedio

### 10.4 NAG 100 Apéndice C — Criterios de aceptación

**Prueba básica (Sección I):**
- **Rechazo:** Si 2 o más de las 4 probetas de curvado de raíz presentan fisura en material de aporte o en zona entre aporte y metal base, mayor de 3,2 mm (1/8") en cualquier dirección
- Grietas solo en cantos de las muestras: NO se consideran

**Sección III — Doblado guiado:**
- **Rechazo:** Cualquier grieta después de retirada de la máquina de doblado

**Sección III — Tracción:**
- **Rechazo:** Rotura adyacente a o en el metal de soldadura

---

## 11. VIGENCIA Y RENOVACIÓN DE CALIFICACIÓN

| Parámetro | ASME IX | API 1104 | AWS D1.1 | NAG 105 (Cat. C/D) | NAG 100 (Apend. C) |
|-----------|---------|----------|----------|--------------------|-------------------|
| **Vigencia general** | Sin plazo fijo | Sin plazo fijo | Sin plazo fijo (indefinida) | **2 años** | 15 meses (1 vez por año calendario) |
| **Límite de inactividad** | **6 meses** | Sin especificar (a discreción compañía) | **6 meses** | **90 días corridos** | 7,5 meses (2 veces/año) |
| **Causa de expiración 1** | No usar el proceso ≥ 6 meses | Problema sobre competencia | No ejercer el proceso ≥ 6 meses | Inactividad > 90 días EN trabajos supervisados Gas del Estado | No calificado dentro de los 15 meses |
| **Causa de expiración 2** | Razón para cuestionar habilidad | — | Razón específica para cuestionar capacidad | Transcurridos 2 años trabajando continuamente | 3 uniones rechazadas o 3% del total (lo mayor) |
| **Renovación por inactividad** | 1 cupón cualquiera (material, posición, espesor libre) → restaura TODAS las calificaciones previas para ese proceso | Recalificación completa | Placa de 3/8" (10 mm) califica espesores ≥ 1/8" | Rendir nueva prueba de habilidad | — |
| **Renovación por revocación** | Recalificación completa para el trabajo planificado | Recalificación a solicitud | Retest completo tras entrenamiento | Inmediata re-examinación | — |
| **Retest inmediato** | 2 cupones consecutivos (ambos deben pasar) | Una segunda oportunidad (condiciones ajenas al soldador) | 2 soldaduras de cada tipo/posición que falló | Espera mínima 60 días corridos | — |
| **Prueba suplementaria** | — | — | — | Dentro de 15 días corridos desde primera prueba | — |
| **Plásticos (NAG 100)** | — | — | — | — | 6 meses sin usar el proceso |

**Notas:**
- **ASME IX:** La extensión de calificación se logra si dentro de los 6 meses el soldador usó ese proceso bajo supervisión de la organización → extiende 6 meses adicionales
- **AWS D1.1:** No se permite retest inmediato después de una falla de recalificación (6.25.1.4) — solo retest tras entrenamiento adicional
- **NAG 105:** El período de 90 días es MÁS ESTRICTO que el de ASME IX y API 1104. La credencial física tiene validez de 2 años con renovación de cupones
- **API 1104 Ed. 22ª:** La recomendación es que la compañía establezca su propia política de mantenimiento de calificación

---

## 12. FORMULARIOS Y REGISTROS OBLIGATORIOS

### 12.1 ASME IX

| Formulario | Código | Contenido clave |
|-----------|--------|----------------|
| WPS (Welding Procedure Specification) | — | Variables de procedimiento |
| PQR (Procedure Qualification Record) | — | Resultados de calificación del procedimiento |
| WPQ (Welder Performance Qualification) | QW-484A | Variables esenciales, tipos de ensayo, resultados, rangos calificados |
| WPQ Operador | QW-484B | Ídem para operadores automáticos |

**Campos obligatorios del WPQ (QW-484A):**
- Nombre e identificación del soldador
- Proceso(s) de soldadura
- Variables esenciales calificadas (per QW-350)
- Tipo de cupón (plancha/tubo, con/sin respaldo)
- Posición(es) ensayadas
- Diámetro del tubo (si aplica)
- Espesor del depósito de metal
- Número P del metal base
- Número F del electrodo/aporte
- Resultados de ensayos (visual, doblez, volumétrico)
- Rangos calificados (posiciones, espesores, diámetros per QW-452)
- Fecha y firma del supervisor/inspector

**Identificación obligatoria:** Cada soldador recibe número, letra o símbolo único (QW-301.3)

### 12.2 API 1104

**Campos del Formulario de Registro (Figura 2 Ed. 21ª / Figura 1 Ed. 22ª):**

| Sección | Campos |
|---------|--------|
| Encabezado | Fecha, N° de ensayo, Tipo de ensayo (procedimiento/soldador), Ubicación |
| Identificación | Nombre del soldador |
| Condiciones | Temperatura media, Condiciones meteorológicas, Protección viento |
| Equipo | Tipo y tamaño de máquina de soldadura |
| Parámetros eléctricos | Voltaje, Amperaje |
| Material de aporte | Clasificación, Tamaño del refuerzo |
| Tubería | Tipo y grado, Espesor de pared, Diámetro exterior |
| Procedimiento | Posición (Rotación/Fijo), Procedimiento aplicado, Marcas y cupón estarcido |
| Resultados mecánicos | Dimensiones de muestra, Área, Carga máxima, Resistencia a tracción, Ubicación de fractura |
| Observaciones | Ensayos de resistencia, doblado y mella |
| Conclusión | Calificado / Descalificado |

**Ed. 22ª añade campos:** Grado de material 1 y 2, Tipo de junta, Ángulo de bisel, Tipo de respaldo, Parámetros por pasada (proceso, dirección, clasificación y diámetro del metal de aporte, gas/fundente, temperatura precalentamiento/interpases, voltaje, amperaje, velocidad, heat input), Tiempos entre pasadas, Método de enfriamiento, PWHT, resultados NDT

### 12.3 AWS D1.1

| Formulario | Descripción | Referencia |
|-----------|-------------|-----------|
| WPS | Welding Procedure Specification | Cláusula 6.8 |
| PQR | Procedure Qualification Record | Cláusula 6.10 |
| WPQR | Welding Performance Qualification Record | Cláusula 6.19 / Tabla 6.12 + Anexo J |

**WPQR debe listar:** Todas las variables esenciales aplicables de Tabla 6.12

### 12.4 NAG 105

| Formulario | Denominación | Categorías | N° formulario |
|-----------|-------------|-----------|--------------|
| EPS | Especificación de Procedimiento de Soldadura | Todas | — |
| RCP | Registro de Calificación de Procedimiento | A y B | — |
| Calificación de soldadores | Datos personales + resultados de ensayos | Todas | — |
| Credencial para Soldadores | Con fotografía 4×4 cm, datos personales, categoría, alcance | C y D | Form. N° 513-780-0 |
| Cupón para Credenciales | Renovable (6 cupones por plancha) | C y D | Form. N° 513-781-0 |

**Datos de la credencial (anverso):** Apellido, nombre, fecha de nacimiento, documento (CI/DNI/LE/P), nacionalidad, fotografía 4×4 cm, firma y sello del Jefe del Sector

**Datos de la credencial (reverso):** Fecha de prueba, sector, N° de Registro, Categoría (A/B/C/D), Posición (ascendente/descendente/horizontal/plana/todas), Alcance de diámetro (< 2" / 2"–12" / > 12"), firma del soldador, sello del sector

---

## 13. SISTEMA DE CATEGORÍAS NAG (ARGENTINA)

### Resumen de las 4 Categorías

| Categoría | Proceso | Aplicación principal | Norma de referencia | Límites específicos |
|-----------|---------|---------------------|--------------------|--------------------|
| **A** | SMAW + semiautomáticas | Recipientes a presión, estructuras, cañerías de proceso en plantas | ASME IX | Sin límite de diámetro/presión |
| **B** | Automático (SAW, etc.) | Ídem Cat. A + soldadura transversal de gasoductos | ASME IX | Sin límite de diámetro/presión |
| **C** | SMAW + semiautomáticas | Gasoductos, poliductos, ramales industriales alta presión | API 1104 | Sin límite (lleva al A si OD > 12") |
| **D** | SMAW electrodo revestido | Redes de distribución, plantas reguladoras ≤ 25 kg/cm² | EPS N°1 (interna) | OD máx. 323,8 mm (12 3/4"), espesor máx. 19 mm, tensión < 20% SMYS |

### Subcategorías de D

| Subcategoría | Alcance |
|-------------|---------|
| **Da** | Redes de distribución, extensiones, ramales domiciliarios |
| **Db** | Plantas de regulación |
| **Dab** | Da + Db (cobertura total) |

### Jerarquía de calificaciones NAG

- **Categoría C aprobada** → habilita también para **Categoría D** (dentro de las mismas variables esenciales)
- **Categoría Dab** → habilita para **Da** y **Db**
- **Categoría A** → NO habilita para C o D (categorías distintas, diferentes normas de referencia)

### Vigencia y penalidades NAG 105

**Vigencia:**
- Categorías C y D: **2 años** (credencial física)
- Inactividad > **90 días** en obras bajo supervisión Gas del Estado → re-examen

**Penalidades:**
| Conducta | Sanción |
|----------|---------|
| Soldaduras para las que no fue calificado | Inhabilitación 1 mes a 1 año |
| No ajustarse al procedimiento | Inhabilitación 1 mes a 1 año |
| Falta de respeto a la inspección | Inhabilitación 1 mes a 1 año |
| Alterar procedimiento en su beneficio / dolo o mala fe | Inhabilitación 1 a 5 años |
| Reincidencia | Mínimos y máximos se **duplican** (hasta inhabilitación permanente) |

**Transferencia entre obras:** El soldador Cat. C o D puede transferirse entre obras dentro de su categoría sin re-examen, siguiendo el procedimiento de pase de registro.

### Fórmula de verificación de tensión para Categoría D

```
i = (P × D × 100) / (2 × t × S)

Donde:
P = presión de diseño (kg/cm² M)
D = diámetro nominal exterior (mm)
t = espesor nominal de pared (mm)
S = tensión de fluencia del material (kg/cm²)
i = porcentaje de tensión circunferencial respecto a S (debe ser < 20%)
```

**Ejemplo:** P=25 kg/cm²M, D=219,1 mm, t=5,56 mm, S=2110 kg/cm² → i=23,34% → **Excede 20%: NO puede ser soldado por Cat. D**

---

## 14. TABLA COMPARATIVA CRUZADA

### 14.1 Variables esenciales — comparación directa

| Variable | ASME IX | API 1104 Ed.21ª | API 1104 Ed.22ª | AWS D1.1 | NAG Cat. D |
|----------|---------|-----------------|-----------------|----------|------------|
| Proceso | ✓ (por proceso separado) | ✓ | ✓ | ✓ | Fijo: SMAW |
| Dirección vertical | ✓ SMAW/GMAW/GTAW/PAW | ✓ | ✓ (+ horiz.) | ✓ | Fijo |
| Posición | ✓ (adicionar) | ✓ | ✓ | ✓ | Fija horizontal |
| Metal de aporte (grupo) | ✓ N° F | ✓ Tabla 1 | ✓ Tabla 4 WF-1/6 | ✓ F-number | Fijo: E-6010/6015 |
| Respaldo (backing) | ✓ | ✓ | ✓ | ✓ | — |
| Diámetro | ✓ QW-403.16 | ✓ 3 grupos OD | ✓ 3 grupos OD | ✓ Tabla 6.11 | Hasta 323,8 mm |
| Espesor | ✓ QW-452 | ✓ 3 grupos | ✓ proporcional | ✓ Tabla 6.11 | Hasta 19 mm |
| Material base (N°P o grupo) | ✓ N° P | ✓ grupos A/B/C | ✓ grupos A/B/C | ✓ grupos I-IV | Acero carbono |
| Modo transferencia (GMAW) | ✓ QW-409.2 | — | ✓ (GMAW-S) | ✓ (sep. proceso) | — |
| Insertos consumibles | ✓ GTAW/PAW | — | — | — | — |
| Corriente/polaridad | ✓ GTAW | — | — | — | CC inversa |
| Pasadas por proceso | — | — | ✓ nuevo (var. k) | — | Mín. 3 pasadas |

### 14.2 Límites numéricos de inactividad

| Norma | Límite de inactividad | Consecuencia |
|-------|----------------------|-------------|
| ASME IX | 6 meses | Calificación expira para ese proceso |
| API 1104 | Sin especificar (norma) | A criterio de la compañía |
| AWS D1.1 | 6 meses | Calificación expira para ese proceso |
| NAG 105 (C y D) | **90 días corridos** | Re-examen requerido |
| NAG 100 Ap.C | 7,5 meses (2 veces/año); 15 meses máx. | Re-examen requerido |

### 14.3 Comparación de ensayos requeridos para calificación de soldador

| Ensayo | ASME IX | API 1104 | AWS D1.1 | NAG Cat.D |
|--------|---------|----------|----------|-----------|
| Inspección visual | Siempre | Siempre | Siempre | — |
| Doblez de raíz | Sí (< 3/4") | Sí (OD ≤ 12,75", t ≤ 0,5") | Sí (T = 3/8") | Sí |
| Doblez de cara | Sí (< 3/4") | Sí (OD > 12,75", t ≤ 0,5") | Sí (T = 3/8") | — |
| Doblez lateral | Sí (≥ 3/4") | Sí (t > 0,5") | Sí (T ≥ 3/8") | — |
| Nick break (mella) | No | Siempre | No | Sí (Opción 1 y 2) |
| Tracción | No | Sí (OD > 4,5", Ed.21ª) | No (solo WPS) | Sí (Opción 2) |
| Macro-ataque | Filetes | — | Sí (filetes y tapones) | — |
| Rotura de filete | Filetes | Branch connection | Sí | — |
| RT / END volumétrico | Alternativa | Alternativa (a criterio compañía) | Alternativa (no GMAW-S) | Alternativa (a opción Gas del Estado) |
| CVN / Charpy | No (solo procedimientos) | No (solo procedimientos) | Sí (cuando especificado) | No |

### 14.4 Criterios de aceptación de doblado — comparación

| Parámetro | ASME IX | API 1104 | AWS D1.1 |
|-----------|---------|----------|----------|
| Grieta/discontinuidad individual máxima | 1/8" (3 mm) | 1/8" (3 mm) ó mitad del espesor | 1/8" (3 mm) |
| Grieta de esquina máxima | 1/8" (3 mm) | 1/4" (6 mm) — bordes sin discontin. | 1/4" (6 mm) — sin inclusiones |
| Radio del émbolo (útil de doblez) | 1-1/2t (aceros comunes) | 1¾ pulg. (45 mm) fijo | Según Tabla correspondiente |
| Suma de discontinuidades | — | — | 3/8" (10 mm) máx. |

### 14.5 Posición 6G — el más amplio qualifier

| Norma | Qué califica la posición 6G |
|-------|----------------------------|
| ASME IX | **Todas las posiciones** de ranura y filete (plancha y tubo de cualquier diámetro) |
| AWS D1.1 | **Todas las posiciones** para placa y tubería |
| NAG 105 Cat. A | **Todas las posiciones** (equivalencia con ASME IX) |
| API 1104 | Eje a 45° = **todas las posiciones** en calificación individual; 6G no mencionado explícitamente (usa criterio de eje inclinado) |

### 14.6 Cuándo aplica cada norma en Argentina (contexto NAG)

| Tipo de instalación | Presión relativa | Norma de calificación obligatoria |
|---------------------|-----------------|-----------------------------------|
| Gasoductos / oleoductos (≥ 20% TFME) | Alta | API 1104 Sección 6 O ASME IX |
| Gasoductos / oleoductos (< 20% TFME) | Baja | NAG 100 Apéndice C (calificación propia) |
| Recipientes a presión en plantas | Alta | ASME IX (Cat. A de NAG 105) |
| Cañerías de proceso en plantas | Alta | ASME IX (Cat. A de NAG 105) |
| Gasoductos troncales (automático) | Alta | ASME IX (Cat. B de NAG 105) |
| Redes de distribución ≤ 25 kg/cm² | Media/baja | EPS N°1 (Cat. D de NAG 105) |
| Instalaciones industriales de gas | Según caso | ASME IX o API 1104 (NAG 201 Secc. 4.2.2) |

---

## ANEXO A — DIMENSIONES DE MUESTRAS DE ENSAYO

### Muestras API 1104

**Nick Break — Figura 5:**
- Longitud: ~9 pulg. (230 mm)
- Ancho: ~1 pulg. (25 mm)
- Entalle de sierra en cada lado del centro: ~1/8 pulg. (3 mm) profundidad

**Doblez de raíz/cara — Figura 7:**
- Longitud: ~9 pulg. (230 mm)
- Ancho: ~1 pulg. (25 mm) con bordes longitudinales redondeados
- Radio en esquinas: máx. 1/8 pulg. (3 mm)
- Refuerzo de soldadura eliminado de ambas caras (ras con superficie)

**Doblez lateral — Figura 9 (para t > 0,500 pulg.):**
- Longitud: ~9 pulg. (230 mm)
- Ancho: ~1/2 pulg. (13 mm)
- Lados lisos y paralelos; refuerzo eliminado de ambas caras

**Tracción — Figura 4:**
- Longitud: ~9 pulg. (230 mm); Ancho: ~1 pulg. (25 mm); refuerzo NO eliminado
- Sección reducida alternativa: 3/4 pulg. (19 mm) ancho en sección reducida

### Muestras ASME IX — Doblez de lado (QW-462.2)

| T del cupón | Espesor muestra (y) |
|------------|---------------------|
| 3/8" a < 1-1/2" (10–38 mm) | 1/8" (3 mm) |
| ≥ 1-1/2" (≥38 mm) | Múltiples muestras de 3/4" a 1-1/2" cada una |
| Ancho (W) | 3/8" (10 mm) |

### Muestras ASME IX — Doblez de cara y raíz transversal (QW-462.3a)

| T del cupón | y (metales comunes) |
|------------|---------------------|
| 1/16" a < 1/8" (1,5–3 mm) | T |
| 1/8" a < 3/8" (3–10 mm) | T |
| > 3/8" (> 10 mm) | 3/8" (10 mm) |

### Muestras AWS D1.1

| Ensayo | Probeta clave | Dimensiones |
|--------|--------------|-------------|
| Rotura de filete | Fig. 6.25 | 6 pulg. (150 mm) longitud |
| Punteo (tack welder) | Fig. 6.27 | Máx. 1/4 pulg. (6 mm) tamaño, ~2 pulg. (50 mm) largo |
| Tapón | Fig. 6.26 | Agujero 3/4 pulg. (20 mm) en placa 3/8 pulg. (10 mm) |

---

## ANEXO B — DIMENSIONES DE MATRICES DE DOBLEZ

### API 1104 — Plantilla de doblez guiado (Figura 8)

| Componente | Dimensión |
|-----------|----------|
| A — Radio del émbolo (plunger) | 1¾ pulg. (45 mm) |
| B — Radio del troquel (die) | 2⅛ pulg. (60 mm) |
| C — Anchura del troquel | 2 pulg. (50 mm) |

### ASME IX — Matriz de doblez para aceros comunes (alargamiento ≥ 20%, QW-466.1)

**Sistema Inglés:**

| Espesor muestra t | A (Radio émbolo) | B | C | D |
|------------------|-----------------|---|---|---|
| 3/8" | 1-1/2" (4t) | 3/4" (2t) | 2-3/16" (6t+1/8") | 1-3/16" (3t+1/16") |

**Sistema SI (t = 10 mm):**

| A | B | C | D |
|---|---|---|---|
| 38,1 mm (4t) | 19,0 mm (2t) | 60,4 mm (6t+3,2) | 30,2 mm (3t+1,6) |

---

## ANEXO C — REACTIVOS DE ATAQUE QUÍMICO (ASME IX QW-470)

### Para metales ferrosos (QW-472)

| Reactivo | Composición | Aplicación |
|---------|-------------|-----------|
| Ácido Clorhídrico | HCl + agua (partes iguales en volumen) | Inmersión a ebullición |
| Persulfato de Amonio | 1 parte + 9 partes agua (por peso) | Frotamiento con algodón (ambiente) |
| Yodo + Yoduro de Potasio | 1:2:10 (I:KI:agua en peso) | Aplicación con brocha (ambiente) |
| Ácido Nítrico | 1 parte HNO3 + 3 partes agua (por volumen) | Varilla de vidrio / inmersión |

### Para titanio (QW-473.4)

| Reactivo | HF (48%) | HNO3 conc. | HCl conc. | Agua |
|---------|----------|------------|---------|------|
| Ataque de Kroll | 1–3 ml | 2–6 ml | — | Hasta 100 ml |
| Ataque de Kellers | 0,5 ml | 2,5 ml | 1,5 ml | Hasta 100 ml |

---

## ANEXO D — COMPOSICIÓN QUÍMICA DEPÓSITO (ASME IX — Números A, QW-442)

| A-No. | Tipo | C máx. | Cr | Mo | Ni | Mn máx. | Si máx. |
|-------|------|--------|----|----|----|---------|---------|
| 1 | Acero suave | 0,20 | — | — | — | 1,60 | 1,00 |
| 2 | C-Mo | 0,15 | ≤0,50 | 0,40–0,65 | — | 1,60 | 1,00 |
| 3 | Cr(0,4–2%)-Mo | 0,15 | 0,40–2,00 | 0,40–0,65 | — | 1,60 | 1,00 |
| 4 | Cr(2–6%)-Mo | 0,15 | 2,00–6,00 | 0,40–1,50 | — | 1,60 | 2,00 |
| 5 | Cr(6–10,5%)-Mo | 0,15 | 6,00–10,50 | 0,40–1,50 | — | 1,20 | 2,00 |
| 6 | Cr-Martensítico | 0,15 | 11,00–15,00 | ≤0,70 | — | 2,00 | 1,00 |
| 7 | Cr-Ferrítico | 0,15 | 11,00–30,00 | ≤1,00 | — | 1,00 | 3,00 |
| 8 | Cr-Ni Austenítico | 0,15 | 14,50–30,00 | ≤4,00 | 7,50–15,00 | 2,50 | 1,00 |
| 9 | Cr-Ni (alta Ni) | 0,30 | 19,00–30,00 | ≤6,00 | 15,00–37,00 | 2,50 | 1,00 |
| 10 | 4% Ni | 0,15 | — | ≤0,55 | 0,80–4,00 | 1,70 | 1,00 |
| 11 | Mn-Mo | 0,17 | — | 0,25–0,75 | ≤0,85 | 1,25–2,25 | 1,00 |
| 12 | Ni-Cr-Mo | 0,15 | ≤1,50 | 0,25–0,80 | 1,25–2,80 | 0,75–2,25 | 1,00 |

---

## ANEXO E — PRECALENTAMIENTO Y TRATAMIENTOS TÉRMICOS (NAG 100)

### Precalentamiento (NAG 100 Sección 237)

| Condición del acero | Requisito |
|--------------------|-----------|
| C > 0,32% (análisis de colada) O Ceq (C + ¼Mn) > 0,65% | Precalentamiento **obligatorio** |
| Otros aceros | Precalentar cuando contribuya a aliviar condiciones adversas |

### Alivio de tensiones (NAG 100 Sección 239)

| Condición | Temperatura mínima |
|-----------|-------------------|
| Aceros al carbono | **600 °C** |
| Aceros aleados ferríticos | **650 °C** |
| Espesor de pared > 32 mm | Alivio obligatorio |
| C > 0,32% o Ceq > 0,65% | Según ASME VIII |

**Excepciones (sin alivio requerido):**
- Soldadura de filete o ranura ≤ 12,7 mm de ancho que une conexión ≤ 50 mm de diámetro
- Soldadura de filete o ranura ≤ 9,5 mm de ancho que une elementos de apoyo sin presión

---

*Documento generado: 2026-06-24*
*Fuentes: ASME IX 2013 (español) · API 1104 Ed. 21ª (español) + Ed. 22ª (inglés) · AWS D1.1:2020 (inglés) + D1.1:2015 (español) · NAG 100 · NAG 105 (GE-N1-105) · NAG 201*
*Ubicación fuentes: `c:\laragon\www\kocert\info\teoria\PADULA\`*
