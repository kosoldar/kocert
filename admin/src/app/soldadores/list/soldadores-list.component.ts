import { ChangeDetectorRef, Component } from '@angular/core';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';
import { SoldadorService } from '../services/soldador.service';
import { Soldador } from '../models/soldador.model';
import { ToastService } from '../../core/services/toast.service';
import { BasePagedListComponent, PagedQuery, PagedResponse } from '../../shared/base/base-paged-list.component';

@Component({
  selector: 'app-soldadores-list',
  standalone: false,
  templateUrl: './soldadores-list.component.html',
  styleUrl: './soldadores-list.component.scss',
})
export class SoldadoresListComponent extends BasePagedListComponent<Soldador> {
  get soldadores() { return this.items; }

  constructor(
    private service: SoldadorService,
    router: Router,
    cdr: ChangeDetectorRef,
    toast: ToastService,
  ) {
    super(router, cdr, toast);
  }

  protected get basePath() { return 'soldadores'; }
  protected getDisplayName(s: Soldador) { return s.nombre_completo; }
  protected fetchData(p: PagedQuery): Observable<PagedResponse<Soldador>> { return this.service.getAll(p); }
  protected deleteItem(id: number): Observable<void> { return this.service.delete(id); }
}
