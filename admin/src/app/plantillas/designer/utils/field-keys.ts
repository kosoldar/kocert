export interface FieldKeyDef {
  key:      string;
  label:    string;
  group:    string;
}

export const FIELD_KEYS: FieldKeyDef[] = [
  // Certificado
  { key: 'cert.codigo',             label: 'Código RCS',              group: 'Certificado' },
  { key: 'cert.numero',             label: 'Número',                  group: 'Certificado' },
  { key: 'cert.anio',               label: 'Año (2 dígitos)',         group: 'Certificado' },
  { key: 'cert.revision',           label: 'Revisión',                group: 'Certificado' },
  { key: 'cert.tipo',               label: 'Tipo (inicial/renov.)',   group: 'Certificado' },
  { key: 'cert.resultado',          label: 'Resultado',               group: 'Certificado' },
  { key: 'cert.fecha_calificacion', label: 'Fecha calificación',      group: 'Certificado' },
  { key: 'cert.fecha_vencimiento',  label: 'Fecha vencimiento',       group: 'Certificado' },
  { key: 'cert.eps_numero',         label: 'EPS/WPS número',          group: 'Certificado' },
  { key: 'cert.pqr_numero',         label: 'PQR/RCP número',          group: 'Certificado' },
  { key: 'cert.proceso',            label: 'Proceso',                 group: 'Certificado' },
  { key: 'cert.posicion',           label: 'Posición',                group: 'Certificado' },
  { key: 'cert.progresion',         label: 'Progresión',              group: 'Certificado' },
  { key: 'cert.tipo_cupon',         label: 'Tipo cupón',              group: 'Certificado' },
  { key: 'cert.observaciones',      label: 'Observaciones',           group: 'Certificado' },
  { key: 'cert.joint_detail',       label: 'Detalle de junta',        group: 'Certificado' },
  { key: 'cert.metal_base',         label: 'Metal base (especif.)',   group: 'Certificado' },
  { key: 'cert.diametro_espesor',   label: 'Diámetro / Espesor',     group: 'Certificado' },
  { key: 'cert.califica_rangos',    label: 'Rangos calificados',      group: 'Certificado' },
  { key: 'cert.respaldo',           label: 'Tipo de respaldo',        group: 'Certificado' },
  { key: 'cert.metal_base_pnumber', label: 'P-Number metal base',     group: 'Certificado' },
  { key: 'cert.tipo_gas',           label: 'Tipo de gas / Caudal',   group: 'Certificado' },
  { key: 'cert.tipo_corriente',     label: 'Corriente / Polaridad',   group: 'Certificado' },
  { key: 'cert.num_pasadas',        label: 'Número de pasadas',       group: 'Certificado' },
  { key: 'cert.tiempo_p1_p2',       label: 'Tiempo máx. P1→P2',      group: 'Certificado' },
  { key: 'cert.tiempo_p2_rest',     label: 'Tiempo máx. P2→resto',   group: 'Certificado' },
  { key: 'cert.precalentamiento',   label: 'Precalentamiento (°C)',   group: 'Certificado' },
  { key: 'cert.cliente',            label: 'Cliente (API 1104)',      group: 'Certificado' },
  { key: 'cert.obra',               label: 'Obra (API 1104)',         group: 'Certificado' },
  // Soldador
  { key: 'soldador.apellido',       label: 'Apellido',                group: 'Soldador' },
  { key: 'soldador.nombre',         label: 'Nombre',                  group: 'Soldador' },
  { key: 'soldador.dni',            label: 'DNI',                     group: 'Soldador' },
  { key: 'soldador.cuño',           label: 'Cuño',                    group: 'Soldador' },
  { key: 'soldador.ciudad',         label: 'Ciudad',                  group: 'Soldador' },
  { key: 'soldador.nacionalidad',   label: 'Nacionalidad',            group: 'Soldador' },
  // Empresa
  { key: 'empresa.nombre',          label: 'Empresa',                 group: 'Empresa' },
  // Inspector
  { key: 'inspector.nombre',        label: 'Nombre',                  group: 'Inspector' },
  { key: 'inspector.telefono',      label: 'Teléfono',                group: 'Inspector' },
  { key: 'inspector.email',         label: 'Email',                   group: 'Inspector' },
  { key: 'inspector.certificacion', label: 'Certificación',           group: 'Inspector' },
  // Empresa
  { key: 'empresa.ciudad',          label: 'Ciudad empresa',          group: 'Empresa' },
  // Norma
  { key: 'norma.nombre',            label: 'Nombre norma',            group: 'Norma' },
  { key: 'norma.edicion_texto',     label: 'Edición (texto largo)',   group: 'Norma' },
  { key: 'norma.edicion',           label: 'Edición (corto)',         group: 'Norma' },
  { key: 'norma.subtitulo',         label: 'Subtítulo (LINEA REGULAR…)', group: 'Norma' },
];

export const FIELD_KEY_GROUPS = [...new Set(FIELD_KEYS.map(f => f.group))];

export function fieldLabel(key: string): string {
  return FIELD_KEYS.find(f => f.key === key)?.label ?? key;
}
