export interface Empresa {
  id: number;
  nombre: string;
  cuit?: string | null;
  contacto?: string | null;
  activo: boolean;
  created_at?: string;
}

import { PageMeta } from '../../shared/components/pagination/pagination.component';

export interface EmpresasPage {
  data: Empresa[];
  meta: PageMeta;
}
