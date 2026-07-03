export interface Inspector {
  id:             number;
  nombre:         string;
  certificacion:  string;
  email?:         string | null;
  telefono?:      string | null;
  firma_path?:    string | null;
  activo:         boolean;
  created_at?:    string;
}
