import { Injectable } from '@angular/core';
import { Observable, forkJoin } from 'rxjs';
import { map } from 'rxjs/operators';

import { CertificadoService } from '../services/certificado.service';
import { SoldadorService } from '../../soldadores/services/soldador.service';
import { EmpresaService } from '../../empresas/services/empresa.service';
import { CatalogoService, CatalogoNorma, JointDesign, Norma } from '../../core/services/catalogo.service';
import { InspectorService } from '../../inspectores/services/inspector.service';
import { Inspector } from '../../inspectores/models/inspector.model';
import { Certificado } from '../models/certificado.model';
import { SelectOption } from '../../shared/components/searchable-select/searchable-select.component';

export interface CertFormDropdowns {
  soldadores:   SelectOption[];
  empresas:     SelectOption[];
  normas:       Norma[];
  siguiente:    { numero: number; anio: number };
  inspectores:  Inspector[];
  jointDesigns: JointDesign[];
}

@Injectable({ providedIn: 'root' })
export class CertificadoFormDataService {
  constructor(
    private certSvc:      CertificadoService,
    private catalogoSvc:  CatalogoService,
    private soldadorSvc:  SoldadorService,
    private empresaSvc:   EmpresaService,
    private inspectorSvc: InspectorService,
  ) {}

  loadDropdowns(): Observable<CertFormDropdowns> {
    return forkJoin({
      soldadores:   this.soldadorSvc.getAll({ per_page: 500, activo: true }),
      empresas:     this.empresaSvc.getAll({ per_page: 500, activo: true }),
      normas:       this.catalogoSvc.getNormas(),
      siguiente:    this.catalogoSvc.nextNumero(),
      inspectores:  this.inspectorSvc.getAll(),
      jointDesigns: this.catalogoSvc.getJointDesigns(),
    }).pipe(
      map(res => ({
        soldadores:   res.soldadores.data.map(s => ({ id: s.id, name: `${s.nombre_completo} (${s.dni})` })),
        empresas:     res.empresas.data.map(e => ({ id: e.id, name: e.nombre })),
        normas:       res.normas,
        siguiente:    res.siguiente,
        inspectores:  res.inspectores.filter(i => i.activo),
        jointDesigns: res.jointDesigns,
      })),
    );
  }

  loadCertificado(id: number): Observable<Certificado> {
    return this.certSvc.getOne(id);
  }

  loadCatalogo(normaId: number): Observable<CatalogoNorma> {
    return this.catalogoSvc.porNorma(normaId);
  }

  checkNumero(numero: number, anio: number): Observable<{ disponible: boolean }> {
    return this.catalogoSvc.checkNumero(numero, anio);
  }

  create(payload: Record<string, unknown>): Observable<Certificado> {
    return this.certSvc.create(payload);
  }

  update(id: number, payload: Record<string, unknown>): Observable<Certificado> {
    return this.certSvc.update(id, payload);
  }
}
