import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { debounceTime, Subject, switchMap, takeUntil } from 'rxjs';
import { CertLayout, LayoutBlock, ASME_VARIABLE_ROWS, API1104_VARIABLE_ROWS, VariableRowDef } from '../../models/cert-layout.model';
import { CertLayoutService } from '../../services/cert-layout.service';
import { ToastService } from '../../../core/services/toast.service';

@Component({
  selector: 'app-variables-editor',
  standalone: false,
  changeDetection: ChangeDetectionStrategy.OnPush,
  templateUrl: './variables-editor.component.html',
})
export class VariablesEditorComponent implements OnInit, OnDestroy {
  layout:  CertLayout | null = null;
  block:   LayoutBlock | null = null;
  saving   = false;
  previewHtml: SafeHtml = '';
  previewLoading = false;
  previewError   = false;

  private destroy$ = new Subject<void>();
  private preview$ = new Subject<LayoutBlock>();

  constructor(
    private route:     ActivatedRoute,
    private router:    Router,
    private svc:       CertLayoutService,
    private toast:     ToastService,
    private sanitizer: DomSanitizer,
    public  cdr:       ChangeDetectorRef,
  ) {
    this.preview$.pipe(
      debounceTime(600),
      switchMap(b => { this.previewLoading = true; this.cdr.markForCheck(); return this.svc.previewBlock(b); }),
      takeUntil(this.destroy$),
    ).subscribe({
      next: r => { this.previewHtml = this.sanitizer.bypassSecurityTrustHtml(r.html); this.previewLoading = false; this.cdr.markForCheck(); },
      error: () => { this.previewLoading = false; this.previewError = true; this.cdr.markForCheck(); },
    });
  }

  ngOnInit(): void {
    const layoutId = +this.route.snapshot.paramMap.get('layoutId')!;
    const blockId  =  this.route.snapshot.paramMap.get('blockId')!;
    this.svc.getOne(layoutId).pipe(takeUntil(this.destroy$)).subscribe({
      next: layout => {
        this.layout = layout;
        this.block  = this.findBlock(layout, blockId);
        if (!this.block) { this.toast.error('Bloque no encontrado.'); this.goBack(); return; }
        this.preview$.next(this.block);
        this.cdr.markForCheck();
      },
      error: () => { this.toast.error('Error al cargar.'); this.goBack(); },
    });
  }

  ngOnDestroy(): void { this.destroy$.next(); this.destroy$.complete(); }

  // ── Filas ─────────────────────────────────────────────────────────────────

  get isApi1104(): boolean { return this.layout?.norma_ids?.includes(4) ?? false; }

  get rowDefs(): VariableRowDef[] {
    const base  = this.isApi1104 ? API1104_VARIABLE_ROWS : ASME_VARIABLE_ROWS;
    const order = this.cfg['rowOrder'] as string[] | undefined;
    if (!order?.length) return base;
    const idx = Object.fromEntries(base.map(r => [r.key, r]));
    return [...order.map(k => idx[k]).filter(Boolean) as VariableRowDef[], ...base.filter(r => !order.includes(r.key))];
  }

  isHidden(key: string): boolean { return ((this.cfg['hiddenRows'] as string[] | undefined) ?? []).includes(key); }

  toggleRow(key: string): void {
    const hidden = this.isHidden(key)
      ? ((this.cfg['hiddenRows'] as string[]) ?? []).filter(k => k !== key)
      : [...((this.cfg['hiddenRows'] as string[]) ?? []), key];
    this.setConfig('hiddenRows', hidden.length ? hidden : undefined);
  }

  moveRow(key: string, dir: -1 | 1): void {
    const base  = (this.isApi1104 ? API1104_VARIABLE_ROWS : ASME_VARIABLE_ROWS).map(r => r.key);
    const order = [...((this.cfg['rowOrder'] as string[] | undefined) ?? base)];
    const i = order.indexOf(key);
    if (i < 0) return;
    const j = i + dir;
    if (j < 0 || j >= order.length) return;
    [order[i], order[j]] = [order[j], order[i]];
    this.setConfig('rowOrder', order);
  }

  // ── Config & save ─────────────────────────────────────────────────────────

  get cfg(): Record<string, unknown> { return (this.block as any)?.config ?? {}; }

  setConfig(key: string, value: unknown): void {
    if (!this.block) return;
    if (!(this.block as any).config) (this.block as any).config = {};
    value === undefined ? delete (this.block as any).config[key] : ((this.block as any).config[key] = value);
    this.preview$.next(this.block);
    this.cdr.markForCheck();
  }

  save(): void {
    if (!this.layout || !this.block) return;
    this.saving = true;
    const pages = (this.layout.blocks?.pages ?? []).map(p => ({
      ...p, blocks: (p.blocks ?? []).map(b => b.id === this.block!.id ? this.block! : b),
    }));
    this.svc.update(this.layout.id, { blocks: { ...this.layout.blocks, pages } }).pipe(takeUntil(this.destroy$)).subscribe({
      next: () => { this.saving = false; this.toast.success('Guardado.'); this.goBack(); },
      error: () => { this.saving = false; this.toast.error('Error al guardar.'); this.cdr.markForCheck(); },
    });
  }

  goBack(): void { this.router.navigate(['/plantillas', this.layout?.id ?? 0, 'editar']); }

  private findBlock(layout: CertLayout, id: string): LayoutBlock | null {
    for (const p of layout.blocks?.pages ?? []) for (const b of p.blocks ?? []) if (b.id === id) return b;
    return null;
  }
}
