import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Empresa, EmpresasPage } from '../models/empresa.model';

export interface EmpresasQuery {
  search?:   string;
  page?:     number;
  per_page?: number;
  activo?:   boolean;
  sort_by?:  string;
  sort_dir?: string;
}

@Injectable({ providedIn: 'root' })
export class EmpresaService {
  private base = `${environment.apiUrl}/empresas`;

  constructor(private http: HttpClient) {}

  getAll(opts: EmpresasQuery = {}): Observable<EmpresasPage> {
    let params = new HttpParams();
    if (opts.search)               params = params.set('search',   opts.search);
    if (opts.page)                 params = params.set('page',     opts.page.toString());
    if (opts.per_page)             params = params.set('per_page', opts.per_page.toString());
    if (opts.activo !== undefined)  params = params.set('activo',  opts.activo ? '1' : '0');
    if (opts.sort_by)              params = params.set('sort_by',  opts.sort_by);
    if (opts.sort_dir)             params = params.set('sort_dir', opts.sort_dir);
    return this.http.get<EmpresasPage>(this.base, { params });
  }

  getOne(id: number): Observable<Empresa> {
    return this.http.get<Empresa>(`${this.base}/${id}`);
  }

  create(data: Partial<Empresa>): Observable<Empresa> {
    return this.http.post<Empresa>(this.base, data);
  }

  update(id: number, data: Partial<Empresa>): Observable<Empresa> {
    return this.http.put<Empresa>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
