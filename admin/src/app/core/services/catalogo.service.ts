import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface CampoNorma {
  campo:        string;
  tipo:         'catalog' | 'enum' | 'text' | 'decimal' | 'integer';
  catalogo?:    string;
  opciones?:    string[];
  requerido:    boolean;
  si_proceso?:  string[];
  si_cupon?:    string;
  si_junta?:    string;
  si_producto?: string;
}

export interface CatalogoNorma {
  norma:                   string;
  procesos:                { id: number; nombre: string }[];
  posiciones?:             any[];
  grupos_base_metal?:      any[];
  grupos_consumible?:      any[];
  consumibles?:            any[];
  campos_norma:            CampoNorma[];
  /** metal_codigo → [consumible_codigo, ...] */
  metal_consumible?:       Record<string, string[]>;
  /** consumible_codigo → [posicion_codigo, ...] — vacío = todas permitidas */
  consumible_posiciones?:  Record<string, string[]>;
  /** metal_probado → [metal_calificado, ...] */
  metal_calificados?:      Record<string, string[]>;
  /** consumible_probado → [consumible_calificado, ...] */
  consumible_calificados?: Record<string, string[]>;
}

export interface Norma {
  id:          number;
  nombre:      string;
  descripcion?: string | null;
}

export interface JointDesign {
  id:     number;
  code:   string;
  nombre: string;
}

@Injectable({ providedIn: 'root' })
export class CatalogoService {
  private base = `${environment.apiUrl}/catalogos`;

  constructor(private http: HttpClient) {}

  getNormas(): Observable<Norma[]> {
    return this.http.get<Norma[]>(`${this.base}/normas`);
  }

  porNorma(normaId: number): Observable<CatalogoNorma> {
    return this.http.get<CatalogoNorma>(`${this.base}/norma/${normaId}`);
  }

  nextNumero(): Observable<{ numero: number; anio: number }> {
    return this.http.get<{ numero: number; anio: number }>(`${this.base}/next-numero`);
  }

  checkNumero(numero: number, anio: number): Observable<{ disponible: boolean }> {
    const params = new HttpParams().set('numero', numero).set('anio', anio);
    return this.http.get<{ disponible: boolean }>(`${this.base}/check-numero`, { params });
  }

  getJointDesigns(): Observable<JointDesign[]> {
    return this.http.get<JointDesign[]>(`${this.base}/joint-designs`);
  }
}
