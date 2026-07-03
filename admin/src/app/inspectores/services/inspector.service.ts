import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Inspector } from '../models/inspector.model';

@Injectable({ providedIn: 'root' })
export class InspectorService {
  private base = `${environment.apiUrl}/inspectores`;

  constructor(private http: HttpClient) {}

  getAll(): Observable<Inspector[]> {
    return this.http.get<Inspector[]>(this.base);
  }

  getOne(id: number): Observable<Inspector> {
    return this.http.get<Inspector>(`${this.base}/${id}`);
  }

  create(data: Partial<Inspector>): Observable<Inspector> {
    return this.http.post<Inspector>(this.base, data);
  }

  update(id: number, data: Partial<Inspector>): Observable<Inspector> {
    return this.http.put<Inspector>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }

  uploadFirma(id: number, file: File): Observable<{ firma_path: string }> {
    const fd = new FormData();
    fd.append('firma', file);
    return this.http.post<{ firma_path: string }>(`${this.base}/${id}/firma`, fd);
  }
}
