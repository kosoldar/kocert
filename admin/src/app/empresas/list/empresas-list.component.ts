import { ChangeDetectorRef, Component } from '@angular/core';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';
import { EmpresaService } from '../services/empresa.service';
import { Empresa } from '../models/empresa.model';
import { ToastService } from '../../core/services/toast.service';
import { BasePagedListComponent, PagedQuery, PagedResponse } from '../../shared/base/base-paged-list.component';

@Component({
  selector: 'app-empresas-list',
  standalone: false,
  templateUrl: './empresas-list.component.html',
})
export class EmpresasListComponent extends BasePagedListComponent<Empresa> {
  get empresas() { return this.items; }

  constructor(
    private service: EmpresaService,
    router: Router,
    cdr: ChangeDetectorRef,
    toast: ToastService,
  ) {
    super(router, cdr, toast);
  }

  protected get basePath() { return 'empresas'; }
  protected getDisplayName(e: Empresa) { return e.nombre; }
  protected override getDeleteMessage(e: Empresa) { return `${e.nombre} eliminada.`; }
  protected fetchData(p: PagedQuery): Observable<PagedResponse<Empresa>> { return this.service.getAll(p); }
  protected deleteItem(id: number): Observable<void> { return this.service.delete(id); }
}
