import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { switchMap, Subject, takeUntil } from 'rxjs';
import { CertLayout, LayoutBlock } from '../../models/cert-layout.model';
import { CertLayoutService } from '../../services/cert-layout.service';
import { ToastService } from '../../../core/services/toast.service';

@Component({
  selector: 'app-junta-editor',
  standalone: false,
  changeDetection: ChangeDetectionStrategy.OnPush,
  templateUrl: './junta-editor.component.html',
})
export class JuntaEditorComponent implements OnInit, OnDestroy {
  layout:  CertLayout | null = null;
  block:   LayoutBlock | null = null;
  previewHtml: SafeHtml = '';
  previewLoading = false;
  previewError   = false;

  private destroy$ = new Subject<void>();

  constructor(
    private route:     ActivatedRoute,
    private router:    Router,
    private svc:       CertLayoutService,
    private toast:     ToastService,
    private sanitizer: DomSanitizer,
    public  cdr:       ChangeDetectorRef,
  ) {}

  ngOnInit(): void {
    const layoutId = +this.route.snapshot.paramMap.get('layoutId')!;
    const blockId  =  this.route.snapshot.paramMap.get('blockId')!;
    this.svc.getOne(layoutId).pipe(takeUntil(this.destroy$)).subscribe({
      next: layout => {
        this.layout = layout;
        this.block  = this.findBlock(layout, blockId);
        if (!this.block) { this.toast.error('Bloque no encontrado.'); this.goBack(); return; }
        // Carga preview directamente (sin configuración que cambie)
        this.previewLoading = true;
        this.svc.previewBlock(this.block).pipe(takeUntil(this.destroy$)).subscribe({
          next: r => { this.previewHtml = this.sanitizer.bypassSecurityTrustHtml(r.html); this.previewLoading = false; this.cdr.markForCheck(); },
          error: () => { this.previewLoading = false; this.previewError = true; this.cdr.markForCheck(); },
        });
        this.cdr.markForCheck();
      },
      error: () => { this.toast.error('Error al cargar.'); this.goBack(); },
    });
  }

  ngOnDestroy(): void { this.destroy$.next(); this.destroy$.complete(); }

  goBack(): void { this.router.navigate(['/plantillas', this.layout?.id ?? 0, 'editar']); }

  private findBlock(layout: CertLayout, id: string): LayoutBlock | null {
    for (const p of layout.blocks?.pages ?? []) for (const b of p.blocks ?? []) if (b.id === id) return b;
    return null;
  }
}
