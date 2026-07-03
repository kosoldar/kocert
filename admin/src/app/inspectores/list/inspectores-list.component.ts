import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { InspectorService } from '../services/inspector.service';
import { Inspector } from '../models/inspector.model';
import { ToastService } from '../../core/services/toast.service';

@Component({
  selector: 'app-inspectores-list',
  standalone: false,
  templateUrl: './inspectores-list.component.html',
})
export class InspectoresListComponent implements OnInit {
  inspectores: Inspector[] = [];
  loading       = false;
  deleteTarget: Inspector | null = null;

  constructor(
    private service: InspectorService,
    private router:  Router,
    private cdr:     ChangeDetectorRef,
    private toast:   ToastService,
  ) {}

  ngOnInit(): void { this.load(); }

  load(): void {
    this.loading = true;
    this.service.getAll().subscribe({
      next: data => { this.inspectores = data; this.loading = false; this.cdr.markForCheck(); },
      error: () => { this.loading = false; this.cdr.markForCheck(); },
    });
  }

  goNew():                    void { this.router.navigate(['/inspectores/new']); }
  goEdit(i: Inspector):       void { this.router.navigate(['/inspectores', i.id, 'edit']); }
  confirmDelete(i: Inspector): void { this.deleteTarget = i; this.cdr.detectChanges(); }
  cancelDelete():              void { this.deleteTarget = null; this.cdr.detectChanges(); }

  doDelete(): void {
    if (!this.deleteTarget) return;
    const nombre = this.deleteTarget.nombre;
    this.service.delete(this.deleteTarget.id).subscribe({
      next: () => {
        this.deleteTarget = null;
        this.toast.success(`Inspector ${nombre} dado de baja.`);
        this.load();
      },
      error: () => {
        this.deleteTarget = null;
        this.cdr.detectChanges();
        this.toast.error('Error al dar de baja.');
      },
    });
  }
}
