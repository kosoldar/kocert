import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { Soldador, SoldadoresPage } from '../models/soldador.model';

export interface SoldadoresQuery {
  search?:   string;
  page?:     number;
  per_page?: number;
  activo?:   boolean;
  sort_by?:  string;
  sort_dir?: string;
}

@Injectable({ providedIn: 'root' })
export class SoldadorService {
  private base = `${environment.apiUrl}/soldadores`;

  constructor(private http: HttpClient) {}

  getAll(opts: SoldadoresQuery = {}): Observable<SoldadoresPage> {
    let params = new HttpParams();
    if (opts.search)              params = params.set('search',   opts.search);
    if (opts.page)                params = params.set('page',     opts.page.toString());
    if (opts.per_page)            params = params.set('per_page', opts.per_page.toString());
    if (opts.activo !== undefined) params = params.set('activo',  opts.activo ? '1' : '0');
    if (opts.sort_by)             params = params.set('sort_by',  opts.sort_by);
    if (opts.sort_dir)            params = params.set('sort_dir', opts.sort_dir);
    return this.http.get<SoldadoresPage>(this.base, { params });
  }

  getOne(id: number): Observable<Soldador> {
    return this.http.get<{ data: Soldador }>(`${this.base}/${id}`)
      .pipe(map(r => r.data));
  }

  create(data: Partial<Soldador>): Observable<Soldador> {
    return this.http.post<{ data: Soldador }>(this.base, data)
      .pipe(map(r => r.data));
  }

  update(id: number, data: Partial<Soldador>): Observable<Soldador> {
    return this.http.put<{ data: Soldador }>(`${this.base}/${id}`, data)
      .pipe(map(r => r.data));
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }

  uploadFoto(id: number, file: File): Observable<{ data: Soldador }> {
    const fd = new FormData();
    fd.append('foto', file);
    return this.http.post<{ data: Soldador }>(`${this.base}/${id}/foto`, fd);
  }
}
