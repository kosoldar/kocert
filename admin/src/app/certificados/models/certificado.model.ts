export type CertTipo      = 'inicial' | 'renovacion' | 'ampliacion';
export type CertEstado    = 'borrador' | 'vigente' | 'vencido' | 'suspendido';
export type CertResultado = 'aprobado' | 'rechazado';
export type CertProgresion = 'ascendente' | 'descendente';
export type CertTipoCupon  = 'caño' | 'chapa';

export interface Certificado {
  id:                  number;
  numero:              number;
  anio:                number;
  revision:            number;
  codigo?:             string;
  soldador_id:         number;
  empresa_id:          number;
  norma_id:            number;
  usuario_id?:         number;
  tipo:                CertTipo;
  estado:              CertEstado;
  resultado:           CertResultado;
  fecha_calificacion:  string;
  fecha_vencimiento?:  string | null;
  eps_numero:          string;
  pqr_numero?:         string | null;
  proceso:             string;
  posicion:            string;
  progresion?:         CertProgresion | null;
  tipo_cupon:          CertTipoCupon;
  variables?:          Record<string, any> | null;
  joint_design_id?:    number | null;
  joint_detail?:       string | null;
  observaciones?:      string | null;
  qr_token?:           string;
  inspector_id?: number | null;
  soldador?:   { id: number; nombre_completo: string; dni: string };
  empresa?:    { id: number; nombre: string };
  norma?:      { id: number; nombre: string };
  usuario?:    { id: number; nombre?: string };
  inspector?:  { id: number; nombre: string } | null;
  passes?:     {
    id: number; orden: number; etiqueta: string;
    proceso_id: number | null; clasificacion_aporte: string | null;
    diametro_aporte_mm: number | null; polaridad: string | null;
    amperaje_min: number | null; amperaje_max: number | null;
    voltaje_min: number | null; voltaje_max: number | null;
    velocidad_avance_min: number | null; velocidad_avance_max: number | null;
    progresion: string | null;
  }[];
  pdf_path?:   string | null;
  created_at?: string;
}

import { PageMeta } from '../../shared/components/pagination/pagination.component';

export interface CertificadosPage {
  data: Certificado[];
  meta: PageMeta;
}
