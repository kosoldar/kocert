import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Usuario, UsuariosPage } from '../models/usuario.model';

export interface UsuariosQuery {
  search?:   string;
  page?:     number;
  per_page?: number;
  activo?:   boolean;
  rol?:      string;
  sort_by?:  string;
  sort_dir?: string;
}

@Injectable({ providedIn: 'root' })
export class UsuarioService {
  private base = `${environment.apiUrl}/usuarios`;

  constructor(private http: HttpClient) {}

  getAll(opts: UsuariosQuery = {}): Observable<UsuariosPage> {
    let params = new HttpParams();
    if (opts.search)               params = params.set('search',   opts.search);
    if (opts.page)                 params = params.set('page',     opts.page.toString());
    if (opts.per_page)             params = params.set('per_page', opts.per_page.toString());
    if (opts.activo !== undefined)  params = params.set('activo',  opts.activo ? '1' : '0');
    if (opts.rol)                  params = params.set('rol',      opts.rol);
    if (opts.sort_by)              params = params.set('sort_by',  opts.sort_by);
    if (opts.sort_dir)             params = params.set('sort_dir', opts.sort_dir);
    return this.http.get<UsuariosPage>(this.base, { params });
  }

  getOne(id: number): Observable<Usuario> {
    return this.http.get<Usuario>(`${this.base}/${id}`);
  }

  create(data: Partial<Usuario> & { password?: string }): Observable<Usuario> {
    return this.http.post<Usuario>(this.base, data);
  }

  update(id: number, data: Partial<Usuario> & { password?: string }): Observable<Usuario> {
    return this.http.put<Usuario>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
