export interface BlockStyle {
  fontSize?:        number;
  fontWeight?:      'normal' | 'bold';
  color?:           string;
  textAlign?:       'left' | 'center' | 'right';
  borderWidth?:     number;
  borderColor?:     string;
  backgroundColor?: string;
}

export interface VariableRowDef {
  key:   string;
  label: string;
}

export const ASME_VARIABLE_ROWS: VariableRowDef[] = [
  { key: 'type_used',            label: 'Type used / Tipo Usado' },
  { key: 'backing',              label: 'Backing / Respaldo' },
  { key: 'base_metal_pnumber',   label: 'Base metal P/S-Number' },
  { key: 'filler_class',         label: 'Filler metal classification' },
  { key: 'filler_fnumber',       label: 'Filler metal F-Number(s)' },
  { key: 'tungsten',             label: 'Tungsten / Tungsteno (GTAW)' },
  { key: 'joint_design',         label: 'DISEÑO DE JUNTA' },
  { key: 'deposit_thickness',    label: 'Deposit thickness / Espesor depositado' },
  { key: 'position_progression', label: 'Position/Progression / Posición/Progresión' },
  { key: 'gas_type',             label: 'Type of gas (GTAW/GMAW)' },
  { key: 'inert_gas_backing',    label: 'Inert gas backing (GTAW)' },
  { key: 'current_type',         label: 'Current type/polarity / Tipo corriente' },
];

export const API1104_VARIABLE_ROWS: VariableRowDef[] = [
  { key: 'process',         label: 'Process / Proceso' },
  { key: 'backing',         label: 'Backing / Respaldo' },
  { key: 'base_metal',      label: 'Base metal / Metal base' },
  { key: 'filler_root',     label: 'Filler metal root / Electrodo raíz' },
  { key: 'filler_fill',     label: 'Filler metal fill / Electrodo relleno' },
  { key: 'current_root',    label: 'Current root / Corriente raíz' },
  { key: 'current_fill',    label: 'Current fill / Corriente relleno' },
  { key: 'position_prog',   label: 'Position/Progression / Posición/Progresión' },
  { key: 'num_passes',      label: 'Number of passes / Número de pasadas' },
  { key: 'time_p1_p2',      label: 'Time between passes P1→P2' },
  { key: 'time_p2_rest',    label: 'Time between passes P2→rest' },
  { key: 'preheat',         label: 'Preheat temperature / Precalentamiento' },
];

export interface TableStyleConfig {
  fontSize?:         number;
  headerBg?:         string;
  headerColor?:      string;
  headerFontSize?:   number;
  labelFontWeight?:  'bold' | 'normal';
  labelColor?:       string;
  valueColor?:       string;
  rowBg?:            string;
  altRowBg?:         string;
  borderColor?:      string;   // 'transparent' = sin bordes
  cellPaddingH?:     number;   // mm
  cellPaddingV?:     number;   // mm
}

export interface VariablesBlockConfig extends TableStyleConfig {
  showRange?:    boolean;
  showComments?: boolean;
  rowOrder?:     string[];
  hiddenRows?:   string[];
}

export const PASSES_COLUMNS = [
  { key: 'pasada',       label: 'Pasada' },
  { key: 'proceso',      label: 'Proceso' },
  { key: 'clasificacion',label: 'Clasif.' },
  { key: 'diametro',     label: 'Ø mm' },
  { key: 'amperaje',     label: 'Amp.' },
  { key: 'voltaje',      label: 'Volt.' },
  { key: 'avance',       label: 'Avance' },
  { key: 'polaridad',    label: 'Polar.' },
  { key: 'progresion',   label: 'Prog.' },
  { key: 'transferencia',label: 'Transf.' },
] as const;

export type PassesColumnKey = typeof PASSES_COLUMNS[number]['key'];

export interface PassesBlockConfig extends TableStyleConfig {
  columns?:  PassesColumnKey[];
}

export interface ResultsBlockConfig {
  layout?:         'horizontal' | 'vertical';
  showResultText?: boolean;
}

export interface LayoutBlock {
  id:          string;
  type:        'field' | 'text' | 'image' | 'line' | 'rect' | 'variables_block' | 'results_block' | 'passes_block' | 'joint_design_block' | 'header_block' | 'soldador_block';
  fieldKey?:   string;
  imageType?:  'logo' | 'norma_logo' | 'photo' | 'signature' | 'qr';
  content?:    string;
  direction?:  'horizontal' | 'vertical';
  config?:     VariablesBlockConfig | PassesBlockConfig | ResultsBlockConfig;
  shared?:     boolean;
  /** Sub-bloques internos — usado por header_block (y futuros contenedores). */
  blocks?:     LayoutBlock[];
  x:           number;
  y:           number;
  width:       number;
  height:      number;
  style:       BlockStyle;
}

export interface LayoutPage {
  pageNumber: number;
  blocks:     LayoutBlock[];
}

export interface LayoutJson {
  pageSize:    string;
  orientation: 'portrait' | 'landscape';
  marginMm:    { top: number; right: number; bottom: number; left: number };
  pages:       LayoutPage[];
}

export interface CertLayout {
  id:          number;
  nombre:      string;
  descripcion: string | null;
  norma_ids:   number[] | null;
  es_default:  boolean;
  orientacion: 'portrait' | 'landscape';
  blocks:      LayoutJson;
  thumbnail:   string | null;
  created_at:  string;
  updated_at:  string;
}
