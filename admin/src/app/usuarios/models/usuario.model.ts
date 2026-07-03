export type UserRol = 'admin' | 'tecnico';

export interface Usuario {
  id:              number;
  nombre:          string;
  apellido:        string;
  nombre_completo?: string;
  email:           string;
  rol:             UserRol;
  activo:          boolean;
  created_at?:     string;
}

import { PageMeta } from '../../shared/components/pagination/pagination.component';

export interface UsuariosPage {
  data: Usuario[];
  meta: PageMeta;
}
