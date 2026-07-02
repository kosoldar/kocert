---
name: Modelo de base de datos kocert
description: Diseño del modelo de datos acordado para el sistema SaaS de certificación de soldadores. Arquitectura multi-tenant con base de conocimiento compartida. Todo en inglés.
type: project
---

## Convención de nombres
Todo en inglés — tablas, columnas, enums, clases, métodos.

## Capa 1 — Tenancy

```
tenants
├── id, name, slug (unique), logo, plan, active

tenant_users
├── id, tenant_id, name, email, password, active
└── HasRoles (Spatie Laravel Permission, teams=true)
```

## Capa 2 — Knowledge (compartida, todas las normas)

```
standards
├── id, code ("AWS D1.1"), full_name, organization, edition, active

welding_processes
├── id, code (SMAW/GTAW/GMAW/SAW...), name, name_es

positions
├── id, code (1G/2G/3G/4G/5G/6G/1F...), name, name_es, type (groove/fillet)

base_metal_groups
├── id, standard_id, code (P-No.1 Gr.1/Group I/API 5L A/B...), description, description_es

base_materials
├── id, group_id, specification (A36/A106B/API5LX52...), description, description_es

consumable_groups
├── id, standard_id, code (F-No.3/F-No.4/WF-1...), description, description_es

consumables
├── id, group_id, classification (E7018/E6010/ER70S-6...), sfa (SFA-5.1...), description_es

joint_designs                          ← 12 tipos de junta con SVG
├── id, code (single-v/double-v/fillet/flare-v...)
├── name, name_es
├── svg (SVG markup del corte transversal — se renderiza en el PDF del certificado)
└── parameters JSON (['angle','root_opening','root_face'...])
```

**12 tipos cargados:** square-groove, single-v, double-v, single-bevel, double-bevel, single-u, double-u, single-j, double-j, flare-v, flare-bevel, fillet.

## Capa 3 — Rules (corazón inteligente del sistema)

```
thickness_rules
├── id, standard_id
├── coupon_type ENUM('plate','pipe','both')   ← preparado para ISO 9606-1 y normas futuras
├── thickness_from_mm, thickness_to_mm
├── min_layers (e.g. 3 for unlimited range)
├── qualifies_min_mm, qualifies_max_formula ("2t" / "unlimited" / "max(19.0,1.5t)")
└── notes

diameter_rules
├── id, standard_id
├── coupon_type ENUM('plate','pipe','both')
├── diameter_from_mm, diameter_to_mm
├── qualifies_min_mm, qualifies_max_formula
└── notes

qualified_positions
├── id, standard_id
├── tested_position_id, qualified_position_id
└── joint_type (groove/fillet/both)

test_types
├── id, standard_id
├── code (VT/BT-F/BT-R/BT-S/NB/RT/UT...), name, name_es
└── required (bool)
```

## Capa 4 — Operation (datos de cada tenant, aislados por tenant_id)

```
clients                          ← empresas cuyos soldadores son evaluados
├── id, tenant_id
├── name, city, contact, active

inspectors
├── id, tenant_id
├── name, certification ("IRAM-IAS NIVEL II - CERT. 4542")
├── signature (path imagen), email, phone, active

welders
├── id, tenant_id
├── client_id (nullable → null si es particular)
├── last_name, first_name, dni, stamp, id_photo, active

wps
├── id, tenant_id, standard_id
├── code ("208-EP-06/20"), description, active

welder_events                    ← tracking de actividad (ASME IX QW-322.1 / ISO 9606-1 §9.3)
├── id, welder_id, tenant_id
├── event_type ENUM(qualification|renewal|activity_record|suspension|revocation)
├── event_date, notes
└── created_by → tenant_users
```

**Por qué welder_events:** la regla de los 6 meses (ASME IX e ISO 9606-1) invalida la calificación por inactividad antes del vencimiento formal. Sin esta tabla no se puede auditar la continuidad.

## Capa 5 — Certificates (RCS/WPQ)

```
certificates
├── id, tenant_id
├── number ("1604"), year ("25"), revision (0)
├── welder_id, inspector_id, standard_id, process_id, wps_id
├── pqr_code (referencia al procedimiento calificante)
├── coupon_type (plate/pipe)
├── base_metal_id
├── tested_thickness_mm, tested_diameter_mm (nullable si es plate)
├── position_id, progression (uphill/downhill/na)
├── consumable_id, current (DCEP/DCEN/AC)
├── backing (with_backing/without_backing/both)
├── joint_design_id → joint_designs (nullable)
├── joint_detail ("65°±5°, root opening 1mm")
├── root_opening_mm, deposit_thickness_mm
├── preheat_temp_c, interpass_temp_c
├── gas_type, gas_flow
├── inert_gas_backing_type, inert_gas_backing_flow
├── qualification_date, expiration_date
├── status (approved/failed)
├── qr_uuid (para verificación pública)
├── pdf_path, obra
├── ranges_calculated_at TIMESTAMP   ← cuándo se calcularon los rangos
└── rules_version SMALLINT           ← RangeCalculatorService::VERSION al momento del cálculo

certificate_passes                   ← pasadas de soldadura del cupón
├── id, certificate_id, sort_order, sequence_label
├── process_id (nullable — puede diferir del proceso principal)
├── filler_classification, filler_diameter_mm
├── polarity, amperage_min/max, voltage_min/max
├── travel_speed_min/max, transfer_type
└── progression

certificate_tests
├── id, certificate_id, test_type_id
├── result (approved/failed/na)
├── notes
└── details JSONB  ← valores reales del ensayo (bend_angle, defect_size_mm, etc.)

certificate_ranges (calculados automáticamente por RangeCalculatorService)
├── id, certificate_id
├── type (thickness/diameter/position)
├── description ("1/8\" to 3/4\"")
├── min_value, max_value (null = unlimited)
```

## Modelos Eloquent — estructura de carpetas

```
app/Models/
├── Tenancy/     → Tenant, TenantUser
├── Knowledge/   → Standard, WeldingProcess, Position, BaseMetalGroup, BaseMaterial,
│                  ConsumableGroup, Consumable, JointDesign
├── Rules/       → ThicknessRule, DiameterRule, QualifiedPosition, TestType
├── Operation/   → Client, Inspector, Welder, Wps, WelderEvent
└── Certificates/→ Certificate, CertificateTest, CertificateRange, CertificatePass
```

## Services — estructura

```
app/Services/Rules/
├── RangeStrategy.php    ← interface: calculate(Certificate): RangeResult
├── RangeResult.php      ← DTO: thickness[], diameter[], positions[]
├── AsmeIxStrategy.php   ← (pendiente)
├── AwsD11Strategy.php   ← (pendiente)
└── ApiStrategy.php      ← (pendiente)
```

**Por qué calculate() unificado:** ISO 9606-1 tiene dependencias cruzadas entre dimensiones (posición + diámetro afectan el rango juntos). Métodos separados asumirían independencia y romperían con esa norma.

## Effective status del certificado
Computado en `Certificate::getEffectiveStatusAttribute()` (NO columna):
- `valid` → no vencido + actividad reciente
- `expired` → expiration_date pasada
- `inactive` → no vencido pero sin activity_record en los últimos 6 meses

## Seeders — datos cargados

| Tabla | Registros |
|---|---|
| standards | 4 (ASME IX, AWS D1.1, API 1104, AWS D1.3) |
| welding_processes | 7 |
| positions | 12 |
| base_metal_groups | 15 |
| base_materials | 45 |
| consumable_groups | 16 |
| consumables | 31 |
| joint_designs | 12 |
| thickness_rules | 8 (con coupon_type) |
| diameter_rules | 6 (con coupon_type) |
| qualified_positions | 115 |
| test_types | 24 |

## Estado
- [x] 32 migraciones ejecutadas
- [x] Modelos Eloquent completos con relaciones y accessors
- [x] Seeders con knowledge base completa (bilingüe ES/EN)
- [x] Controllers / Requests / Resources
- [x] RangeStrategy interface + RangeResult DTO
- [ ] RangeCalculatorService (AsmeIxStrategy, AwsD11Strategy, ApiStrategy)
- [ ] Wizard Angular
- [ ] Generación de PDFs
