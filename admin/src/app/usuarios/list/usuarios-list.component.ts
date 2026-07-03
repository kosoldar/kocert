import { ChangeDetectorRef, Component } from '@angular/core';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';
import { UsuarioService } from '../services/usuario.service';
import { Usuario } from '../models/usuario.model';
import { ToastService } from '../../core/services/toast.service';
import { BasePagedListComponent, PagedQuery, PagedResponse } from '../../shared/base/base-paged-list.component';

@Component({
  selector: 'app-usuarios-list',
  standalone: false,
  templateUrl: './usuarios-list.component.html',
})
export class UsuariosListComponent extends BasePagedListComponent<Usuario> {
  get usuarios() { return this.items; }

  override perPageOpts = [10, 25, 50];

  constructor(
    private service: UsuarioService,
    router: Router,
    cdr: ChangeDetectorRef,
    toast: ToastService,
  ) {
    super(router, cdr, toast);
  }

  protected get basePath() { return 'usuarios'; }
  protected getDisplayName(u: Usuario) { return `${u.apellido} ${u.nombre}`; }
  protected fetchData(p: PagedQuery): Observable<PagedResponse<Usuario>> { return this.service.getAll(p); }
  protected deleteItem(id: number): Observable<void> { return this.service.delete(id); }

  rolBadge(rol: string): string {
    return rol === 'admin' ? 'bg-primary' : 'bg-secondary';
  }
}
