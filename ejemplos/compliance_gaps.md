# Gaps de compliance vs. glosarioNormas.md

Última auditoría: 2026-06-25 (6ª pasada — glosarioNormas.md leído completo). Todos los gaps críticos e importantes cerrados.

---

## ℹ️ DECISIÓN DE DISEÑO (no accionable técnicamente)

- **Firma del soldador y sello del sector** en Form 513-780-0 reverso — requiere flujo de firma digital o campo de upload por cert. No implementado.
- **AWS D1.1 progresión requerida** — solo aplica en posición vertical (3G/5G/6G). Sin guard `si_posicion` en el form, no se puede hacer `requerido: true` sin romper certs en posición plana.
- **ThicknessRule AWS D1.1 fila duplicada** (T=9.5mm exact) — redundante pero inofensiva; el ranker elige la correcta.
- **ASME IX F-Number sin respaldo vs con respaldo** — §8.2 dice: F-1 CON respaldo→F-1..5; F-1 SIN respaldo→solo F-1. Actualmente no se discrimina (sin columna `backing` en tabla). Requiere schema change.
- **API 1104 / NAG Cat.C bucket vs API grupo** — NAG §6.4 usa 50,8/304,8mm para la credencial; API 1104 internamente usa 60,3/323,9mm. El `min_value` almacenado por el DiameterRule API 1104 puede caer en bucket NAG incorrecto para ODs entre 50,8 y 60,3mm. Afecta solo Cat.C (que delega a API). Fix futuro: tabla separada de display-buckets NAG vs reglas internas API.

---

## ✅ CERRADO (historial completo)

| Item | Fix | Sesión |
|---|---|---|
| F-Number ASME IX seeder inverso | Invertido a upward (F-1=más amplio) | 2026-06-25 |
| NAG Cat. D: electrodo enum E-6010/E-6015 | ✅ | 2026-06-25 |
| NAG Cat. D: progresion campo | ✅ | 2026-06-25 |
| NAG Cat. D: resultado_nick_break + resultado_bend | ✅ | 2026-06-25 |
| NAG Cat. D: PDF muestra NB + BT-R | ✅ | 2026-06-25 |
| NAG Cat. D: presion_diseno + tension_fluencia + cálculo SMYS en PDF | ✅ | 2026-06-25 |
| Vigencia auto-calc +2 años para NAG | ✅ | 2026-06-25 |
| Insertos consumibles GTAW/PAW (QW-404.22) | ✅ | 2026-06-25 |
| NAG Cat. B restringido a SAW/ESW/GMAW-Auto | ✅ | 2026-06-25 |
| NAG credencial: fecha_nacimiento + nacionalidad en modelo/form/PDF | ✅ | 2026-06-25 |
| NAG credencial: alcance diámetro (<2"/2"-12"/>12") en PDF pág. 2 | ✅ | 2026-06-25 |
| NAG certs: validación fecha_nacimiento + nacionalidad en store() | ✅ | 2026-06-25 |
| API 1104 Ed. 22ª var. j: backing strip (respaldo + backing_material) | ✅ | 2026-06-25 |
| ASME IX num_pasadas en camposAsme() → unlimited threshold alcanzable | ✅ | 2026-06-25 |
| renovar() + ampliar() + update() sin checkNagSoldadorData() | ✅ | 2026-06-25 |
| rcs-api1104.blade.php "Número de soldadores" tautología | ✅ | 2026-06-25 |
| NAG Cat. D: ThicknessRuleSeeder sin filas (cat_d norma_id) → espesor vacío | ✅ | 2026-06-25 |
| NAG Cat. D: DiameterRuleSeeder sin filas → alcance diámetro '—' en PDF | ✅ | 2026-06-25 |
| NAG Cat. D: QualifiedPositionSeeder sin filas → posiciones vacías | ✅ | 2026-06-25 |
| **NAG bucket diámetro EQUIVOCADO** — usaba límites API 1104 (60,3/323,9mm) en vez de NAG §6.4 (50,8mm=2" / 304,8mm=12") | ✅ | 2026-06-25 |
