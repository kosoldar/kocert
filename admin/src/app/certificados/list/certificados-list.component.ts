import { ChangeDetectorRef, Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { Subject } from 'rxjs';
import { debounceTime, distinctUntilChanged, takeUntil } from 'rxjs/operators';
import { CertificadoService } from '../services/certificado.service';
import { Certificado, CertEstado } from '../models/certificado.model';
import { PageMeta } from '../../shared/components/pagination/pagination.component';
import { ToastService } from '../../core/services/toast.service';

@Component({
  selector: 'app-certificados-list',
  standalone: false,
  templateUrl: './certificados-list.component.html',
})
export class CertificadosListComponent implements OnInit, OnDestroy {
  certificados: Certificado[] = [];
  meta: PageMeta | null       = null;
  loading     = false;
  searchTerm  = '';
  currentPage = 1;
  perPage     = 20;
  perPageOpts = [10, 20, 50, 100];
  estadoFiltro = '';

  deleteTarget: Certificado | null = null;
  sortBy  = 'anio';
  sortDir: 'asc' | 'desc' = 'desc';
  pdfLoading:   number | null      = null;
  pdfViewing:   number | null      = null;

  private search$ = new Subject<string>();
  private destroy$ = new Subject<void>();

  constructor(
    private service: CertificadoService,
    private router:  Router,
    private cdr:     ChangeDetectorRef,
    private toast:   ToastService,
  ) {}

  ngOnInit(): void {
    this.search$.pipe(
      debounceTime(300), distinctUntilChanged(), takeUntil(this.destroy$),
    ).subscribe(() => { this.currentPage = 1; this.load(); });
    this.load();
  }

  ngOnDestroy(): void { this.destroy$.next(); this.destroy$.complete(); }

  sort(field: string): void {
    if (this.sortBy === field) {
      this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
    } else {
      this.sortBy  = field;
      this.sortDir = 'desc';
    }
    this.currentPage = 1;
    this.load();
  }

  sortIcon(field: string): string {
    if (this.sortBy !== field) return 'bi-arrow-down-up text-muted opacity-50';
    return this.sortDir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
  }

  load(): void {
    this.loading = true;
    this.service.getAll({
      search:   this.searchTerm,
      page:     this.currentPage,
      per_page: this.perPage,
      estado:   this.estadoFiltro || undefined,
      sort_by:  this.sortBy,
      sort_dir: this.sortDir,
    }).pipe(takeUntil(this.destroy$)).subscribe({
      next: res => {
        this.certificados = res.data;
        this.meta         = res.meta;
        this.loading      = false;
        this.cdr.markForCheck();
      },
      error: () => { this.loading = false; this.cdr.markForCheck(); },
    });
  }

  onSearch(v: string): void   { this.searchTerm = v; this.search$.next(v); }
  onPerPage(): void           { this.currentPage = 1; this.load(); }
  onPage(p: number): void     { this.currentPage = p; this.load(); }
  onEstado(): void            { this.currentPage = 1; this.load(); }

  goNew():                    void { this.router.navigate(['/certificados/new']); }
  goEdit(c: Certificado):     void { this.router.navigate(['/certificados', c.id, 'edit']); }

  viewPdf(c: Certificado): void {
    this.pdfViewing = c.id;
    this.service.getPdf(c.id).subscribe({
      next: blob => {
        const url = URL.createObjectURL(blob);
        window.open(url, '_blank', 'noopener');
        setTimeout(() => URL.revokeObjectURL(url), 30000);
        this.pdfViewing = null;
        this.cdr.markForCheck();
      },
      error: () => {
        this.toast.error('Error al generar el PDF.');
        this.pdfViewing = null;
        this.cdr.markForCheck();
      },
    });
  }

  downloadPdf(c: Certificado): void {
    this.pdfLoading = c.id;
    this.service.getPdf(c.id).subscribe({
      next: blob => {
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = `RCS-${c.numero}-${c.anio}-REV${c.revision}.pdf`;
        a.click();
        setTimeout(() => URL.revokeObjectURL(url), 10000);
        this.pdfLoading = null;
        this.cdr.markForCheck();
      },
      error: () => {
        this.toast.error('Error al generar el PDF.');
        this.pdfLoading = null;
        this.cdr.markForCheck();
      },
    });
  }

  confirmDelete(c: Certificado): void { this.deleteTarget = c; this.cdr.detectChanges(); }
  cancelDelete():                void { this.deleteTarget = null; this.cdr.detectChanges(); }

  doDelete(): void {
    if (!this.deleteTarget) return;
    const codigo = this.deleteTarget.codigo ?? `#${this.deleteTarget.id}`;
    this.service.delete(this.deleteTarget.id).subscribe({
      next: () => {
        this.deleteTarget = null;
        this.toast.success(`Certificado ${codigo} eliminado.`);
        this.load();
      },
      error: () => {
        this.deleteTarget = null;
        this.cdr.detectChanges();
        this.toast.error('Error al eliminar.');
      },
    });
  }

  estadoBadge(estado: CertEstado): string {
    return {
      borrador:  'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
      vigente:   'bg-success',
      vencido:   'bg-warning text-dark',
      suspendido: 'bg-danger',
    }[estado] ?? 'bg-secondary';
  }
}
