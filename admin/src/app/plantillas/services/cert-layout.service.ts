import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { CertLayout, LayoutBlock, LayoutJson } from '../models/cert-layout.model';

export interface Norma {
  id:     number;
  nombre: string;
  codigo?: string;
  parent_norma_id?: number | null;
}

export interface CertLayoutPayload {
  nombre:      string;
  descripcion?: string | null;
  norma_ids?:  number[] | null;
  es_default?: boolean;
  orientacion?: 'portrait' | 'landscape';
  blocks:      LayoutJson;
  thumbnail?:  string | null;
}

export interface PreviewResponse {
  pdf: string; // data:application/pdf;base64,...
}

@Injectable({ providedIn: 'root' })
export class CertLayoutService {
  private base = `${environment.apiUrl}/cert-layouts`;

  constructor(private http: HttpClient) {}

  getAll(): Observable<CertLayout[]> {
    return this.http.get<CertLayout[]>(this.base);
  }

  getNormas(): Observable<Norma[]> {
    return this.http.get<Norma[]>(`${environment.apiUrl}/normas`);
  }

  getOne(id: number): Observable<CertLayout> {
    return this.http.get<CertLayout>(`${this.base}/${id}`);
  }

  create(data: CertLayoutPayload): Observable<CertLayout> {
    return this.http.post<CertLayout>(this.base, data);
  }

  update(id: number, data: Partial<CertLayoutPayload>): Observable<CertLayout> {
    return this.http.put<CertLayout>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }

  preview(layoutId: number, certificadoId?: number): Observable<PreviewResponse> {
    const body = certificadoId ? { certificado_id: certificadoId } : {};
    return this.http.post<PreviewResponse>(`${this.base}/${layoutId}/preview`, body);
  }

  previewBlock(block: Partial<LayoutBlock>, certificadoId?: number): Observable<{ html: string }> {
    const body: Record<string, unknown> = { block };
    if (certificadoId) body['certificado_id'] = certificadoId;
    return this.http.post<{ html: string }>(`${this.base}/preview-block`, body);
  }
}
