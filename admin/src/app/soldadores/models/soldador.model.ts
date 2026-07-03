export interface Soldador {
  id: number;
  nombre: string;
  apellido: string;
  nombre_completo: string;
  dni: string;
  fecha_nacimiento?: string | null;
  nacionalidad?: string | null;
  cuño?: string | null;
  ciudad?: string | null;
  telefono?: string | null;
  email?: string | null;
  foto_path?:  string | null;
  activo: boolean;
  total_certificados?: number;
  created_at?: string;
}

import { PageMeta } from '../../shared/components/pagination/pagination.component';

export interface SoldadoresPage {
  data: Soldador[];
  meta: PageMeta;
}
