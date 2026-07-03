import { CampoNorma, CatalogoNorma } from '../../core/services/catalogo.service';

export type CertFormStep = 'solicitante' | 'variables' | 'pasadas' | 'fechas';

export const TOP_LEVEL = new Set(['proceso', 'posicion', 'progresion', 'tipo_cupon']);

export const CAMPO_LABELS: Record<string, string> = {
  proceso: 'Proceso', posicion: 'Posición', progresion: 'Progresión',
  tipo: 'Tipo (manual/semi-auto)',
  transfer_type: 'Modo de transferencia (GMAW)',
  tipo_cupon: 'Tipo cupón', tipo_junta: 'Tipo de junta',
  p_number: 'P-Number metal base', f_number: 'F-Number aporte',
  a_number: 'A-Number (análisis depósito)',
  metal_base: 'Especificación metal base',
  electrodo: 'Electrodo', respaldo: 'Respaldo', backing_material: 'Material backing strip (Ed. 22ª)',
  corriente: 'Corriente/Polaridad', corriente_raiz: 'Corriente raíz',
  corriente_relleno: 'Corriente relleno',
  espesor_cupon: 'Espesor ensayado (mm)', diametro_cupon: 'Diámetro ensayado (mm)',
  espesor_deposito: 'Espesor depositado (mm)',
  junta_calificada: 'Junta calificada',
  gas_proteccion: 'Gas protección', gas_respaldo: 'Gas respaldo',
  tungsteno: 'Tungsteno (GTAW)',
  insertos_consumibles: 'Insertos consumibles (QW-404.22)',
  grupo_base_metal:   'Grupo metal base',
  grupo_consumible:   'Grupo consumible',
  tipo_producto:      'Tipo de producto (P/T)',
  tipo_respaldo:      'Tipo de respaldo',
  electrodo_raiz: 'Electrodo raíz', electrodo_relleno: 'Electrodo relleno',
  electrodo_terminacion: 'Electrodo terminación',
  temperatura_preheat: 'Precalentamiento (°C)',
  temperatura_interpass: 'Temp. entre pasadas (°C)',
  linea_tipo: 'Tipo de línea', cliente: 'Cliente', obra: 'Obra',
  empresa_contratista: 'Empresa contratista',
  num_pasadas: 'N° de pasadas',
  tiempo_p1_p2:   'Tiempo máx. 1ª→2ª pasada (min)',
  tiempo_p2_rest: 'Tiempo máx. 2ª→restantes (min)',
  velocidad_avance: 'Velocidad de avance',
  limpieza_entre_pasadas: 'Limpieza entre pasadas',
  presentador: 'Presentador',
  nag_categoria:  'Categoría NAG',
  nag_credencial: 'N° Credencial NAG (Form 513-780-0)',
  presion_diseno:   'Presión de diseño P (kg/cm²)',
  tension_fluencia: 'Tensión de fluencia SMYS S (kg/cm²)',
  diseno_junta: 'Diseño de junta',
  resultado_vt: 'Examen visual', resultado_bend: 'Prueba de plegado',
  resultado_rt: 'Radiografía (alt.)', resultado_nick_break: 'Nick break',
  resultado_fractura_filete: 'Fractura filete', resultado_macro: 'Macro examen',
};

export function getCampoLabel(campo: string): string {
  return CAMPO_LABELS[campo] ?? campo;
}

export function shouldShow(campo: CampoNorma, vars: Record<string, unknown>): boolean {
  const c = campo as CampoNorma & {
    si_proceso?: string[]; si_cupon?: string; si_junta?: string; si_producto?: string;
  };
  if (c.si_proceso?.length && !c.si_proceso.includes(vars['proceso'] as string)) return false;
  if (c.si_cupon    && vars['tipo_cupon']    !== c.si_cupon)    return false;
  if (c.si_junta    && vars['tipo_junta']    !== c.si_junta)    return false;
  if (c.si_producto && vars['tipo_producto'] !== c.si_producto) return false;
  return true;
}

export function getCatalogOptions(
  catalogName: string,
  catalogData: CatalogoNorma,
  vars: Record<string, unknown>,
): unknown[] {
  switch (catalogName) {
    case 'procesos':
      return catalogData.procesos ?? [];

    case 'grupos_base_metal':
      return catalogData.grupos_base_metal ?? [];

    case 'grupos_consumible': {
      const all = catalogData.grupos_consumible ?? [];
      const metalCode = (vars['p_number'] ?? vars['grupo_base_metal']) as string | undefined;
      const allowed = metalCode ? catalogData.metal_consumible?.[metalCode] : undefined;
      return allowed ? all.filter((g: any) => allowed.includes(g.codigo)) : all;
    }

    case 'consumibles': {
      const all = catalogData.consumibles ?? [];
      const code = (vars['f_number'] ?? vars['grupo_consumible']) as string | undefined;
      return code ? all.filter((c: any) => c.grupo?.codigo === code) : all;
    }

    case 'posiciones': {
      const all = catalogData.posiciones ?? [];
      const code = (vars['f_number'] ?? vars['grupo_consumible']) as string | undefined;
      const restricted = code ? catalogData.consumible_posiciones?.[code] : undefined;
      return restricted?.length ? all.filter((p: any) => restricted.includes(p.codigo)) : all;
    }

    default:
      return [];
  }
}

export function getCatalogLabel(item: any, catalogName: string): string {
  switch (catalogName) {
    case 'procesos':          return item.nombre;
    case 'posiciones':        return `${item.codigo} — ${item.descripcion}`;
    case 'grupos_base_metal': return `${item.codigo}${item.descripcion ? ' — ' + item.descripcion : ''}`;
    case 'grupos_consumible': return `${item.codigo}${item.descripcion ? ' — ' + item.descripcion : ''}`;
    case 'consumibles':       return `${item.clasificacion}${item.sfa ? '  ' + item.sfa : ''}`;
    default: return item.nombre ?? item.codigo ?? item.clasificacion ?? '';
  }
}

export function getCatalogValue(item: any, catalogName: string): string {
  switch (catalogName) {
    case 'procesos':          return item.nombre;
    case 'posiciones':        return item.codigo;
    case 'grupos_base_metal': return item.codigo;
    case 'grupos_consumible': return item.codigo;
    case 'consumibles':       return item.clasificacion;
    default: return String(item.id ?? '');
  }
}

export function deriveResultado(vars: Record<string, unknown>): string {
  const tests = ['resultado_vt', 'resultado_bend', 'resultado_rt',
                 'resultado_nick_break', 'resultado_fractura_filete', 'resultado_macro'];
  return tests.some(t => vars[t] === 'rechazado') ? 'rechazado' : 'aprobado';
}

function normalizePasses(raw: any[]): Record<string, unknown>[] {
  return raw.map((p, i) => ({
    orden:                i + 1,
    etiqueta:             p.etiqueta             || `Pasada ${i + 1}`,
    proceso_id:           p.proceso_id           || null,
    clasificacion_aporte: p.clasificacion_aporte || null,
    diametro_aporte_mm:   p.diametro_aporte_mm   || null,
    polaridad:            p.polaridad            || null,
    amperaje_min:         p.amperaje_min         || null,
    amperaje_max:         p.amperaje_max         || null,
    voltaje_min:          p.voltaje_min          || null,
    voltaje_max:          p.voltaje_max          || null,
    velocidad_avance_min: p.velocidad_avance_min || null,
    velocidad_avance_max: p.velocidad_avance_max || null,
    progresion:           p.progresion           || null,
  }));
}

export function buildCertPayload(v: Record<string, any>): Record<string, unknown> {
  const vars = { ...(v['variables'] ?? {}) };
  const topLevel: Record<string, unknown> = {};
  for (const key of TOP_LEVEL) {
    if (key in vars) { topLevel[key] = vars[key]; delete vars[key]; }
  }
  return {
    soldador_id:        v['soldador_id'],
    empresa_id:         v['empresa_id'],
    norma_id:           v['norma_id'],
    inspector_id:       v['inspector_id'] || null,
    tipo:               v['tipo'],
    eps_numero:         v['eps_numero'],
    pqr_numero:         v['pqr_numero'] || null,
    fecha_calificacion: v['fecha_calificacion'],
    fecha_vencimiento:  v['fecha_vencimiento'] || null,
    numero:             v['numero'],
    anio:               v['anio'],
    revision:           v['revision'] ?? 0,
    joint_design_id:    v['joint_design_id'] || null,
    joint_detail:       v['joint_detail']    || null,
    observaciones:      v['observaciones'] || null,
    resultado:          deriveResultado(v['variables'] ?? {}),
    passes:             normalizePasses(v['pasadas'] ?? []),
    ...topLevel,
    variables:          vars,
  };
}

export function buildDraftPayload(v: Record<string, any>): Record<string, unknown> {
  const vars = { ...(v['variables'] ?? {}) };
  const topLevel: Record<string, unknown> = {};
  for (const key of TOP_LEVEL) {
    if (key in vars) { topLevel[key] = vars[key]; delete vars[key]; }
  }
  return {
    borrador:           true,
    numero:             v['numero'],
    anio:               v['anio'],
    revision:           v['revision'] ?? 0,
    soldador_id:        v['soldador_id']        || null,
    empresa_id:         v['empresa_id']         || null,
    norma_id:           v['norma_id']           || null,
    tipo:               v['tipo']               || null,
    eps_numero:         v['eps_numero']         || null,
    pqr_numero:         v['pqr_numero']         || null,
    fecha_calificacion: v['fecha_calificacion'] || null,
    fecha_vencimiento:  v['fecha_vencimiento']  || null,
    joint_design_id:    v['joint_design_id']    || null,
    joint_detail:       v['joint_detail']       || null,
    observaciones:      v['observaciones']      || null,
    passes:             normalizePasses(v['pasadas'] ?? []),
    ...topLevel,
    variables:          vars,
  };
}
