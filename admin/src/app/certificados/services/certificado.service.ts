import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Certificado, CertificadosPage } from '../models/certificado.model';

export interface CertificadosQuery {
  search?:      string;
  page?:        number;
  per_page?:    number;
  soldador_id?: number;
  empresa_id?:  number;
  estado?:      string;
  anio?:        number;
  sort_by?:     string;
  sort_dir?:    'asc' | 'desc';
}

@Injectable({ providedIn: 'root' })
export class CertificadoService {
  private base = `${environment.apiUrl}/certificados`;

  constructor(private http: HttpClient) {}

  getAll(opts: CertificadosQuery = {}): Observable<CertificadosPage> {
    let params = new HttpParams();
    if (opts.search)      params = params.set('search',      opts.search);
    if (opts.page)        params = params.set('page',        opts.page.toString());
    if (opts.per_page)    params = params.set('per_page',    opts.per_page.toString());
    if (opts.soldador_id) params = params.set('soldador_id', opts.soldador_id.toString());
    if (opts.empresa_id)  params = params.set('empresa_id',  opts.empresa_id.toString());
    if (opts.estado)      params = params.set('estado',      opts.estado);
    if (opts.anio)        params = params.set('anio',        opts.anio.toString());
    if (opts.sort_by)     params = params.set('sort_by',     opts.sort_by);
    if (opts.sort_dir)    params = params.set('sort_dir',    opts.sort_dir);
    return this.http.get<CertificadosPage>(this.base, { params });
  }

  getOne(id: number): Observable<Certificado> {
    return this.http.get<Certificado>(`${this.base}/${id}`);
  }

  create(data: Partial<Certificado>): Observable<Certificado> {
    return this.http.post<Certificado>(this.base, data);
  }

  update(id: number, data: Partial<Certificado>): Observable<Certificado> {
    return this.http.put<Certificado>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }

  getPdf(id: number): Observable<Blob> {
    return this.http.get(`${this.base}/${id}/pdf`, { responseType: 'blob' });
  }
}
