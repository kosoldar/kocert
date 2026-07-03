import { ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { Router } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { CertLayout } from '../models/cert-layout.model';
import { CertLayoutService } from '../services/cert-layout.service';
import { ToastService } from '../../core/services/toast.service';

@Component({
  selector: 'app-plantillas-list',
  standalone: false,
  templateUrl: './plantillas-list.component.html',
})
export class PlantillasListComponent implements OnInit, OnDestroy {
  layouts:      CertLayout[] = [];
  loading       = false;
  deleteTarget: CertLayout | null       = null;
  previewLoading: number | null         = null;
  previewPdf:     SafeResourceUrl | null = null;

  private destroy$ = new Subject<void>();

  constructor(
    private svc:       CertLayoutService,
    private router:    Router,
    private cdr:       ChangeDetectorRef,
    private toast:     ToastService,
    private sanitizer: DomSanitizer,
  ) {}

  ngOnInit(): void { this.load(); }

  ngOnDestroy(): void { this.destroy$.next(); this.destroy$.complete(); }

  load(): void {
    this.loading = true;
    this.svc.getAll().pipe(takeUntil(this.destroy$)).subscribe({
      next: data => {
        this.layouts = data;
        this.loading = false;
        this.cdr.markForCheck();
      },
      error: () => {
        this.loading = false;
        this.toast.error('Error al cargar plantillas.');
        this.cdr.markForCheck();
      },
    });
  }

  goNew(): void  { this.router.navigate(['/plantillas/nueva']); }
  goEdit(l: CertLayout): void { this.router.navigate(['/plantillas', l.id, 'editar']); }

  confirmDelete(l: CertLayout): void { this.deleteTarget = l; this.cdr.detectChanges(); }
  cancelDelete(): void                { this.deleteTarget = null; }

  doDelete(): void {
    if (!this.deleteTarget) return;
    const target = this.deleteTarget;
    this.deleteTarget = null;
    this.svc.delete(target.id).pipe(takeUntil(this.destroy$)).subscribe({
      next: () => { this.toast.success(`Plantilla "${target.nombre}" eliminada.`); this.load(); },
      error: () => this.toast.error('Error al eliminar plantilla.'),
    });
  }

  openPreview(l: CertLayout): void {
    this.previewLoading = l.id;
    this.svc.preview(l.id).pipe(takeUntil(this.destroy$)).subscribe({
      next: res => {
        this.previewLoading = null;
        this.previewPdf     = this.sanitizer.bypassSecurityTrustResourceUrl(res.pdf);
        this.cdr.detectChanges();
      },
      error: () => {
        this.previewLoading = null;
        this.toast.error('Error generando preview. ¿Hay certificados cargados?');
        this.cdr.detectChanges();
      },
    });
  }

  closePreview(): void { this.previewPdf = null; }

  normasLabel(layout: CertLayout): string {
    if (!layout.norma_ids?.length) return 'Todas las normas';
    return `${layout.norma_ids.length} norma${layout.norma_ids.length > 1 ? 's' : ''}`;
  }
}
