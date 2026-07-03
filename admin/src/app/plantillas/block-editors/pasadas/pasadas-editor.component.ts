import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { debounceTime, Subject, switchMap, takeUntil } from 'rxjs';
import { CertLayout, LayoutBlock, PASSES_COLUMNS, PassesColumnKey } from '../../models/cert-layout.model';
import { CertLayoutService } from '../../services/cert-layout.service';
import { ToastService } from '../../../core/services/toast.service';

@Component({
  selector: 'app-pasadas-editor',
  standalone: false,
  changeDetection: ChangeDetectionStrategy.OnPush,
  templateUrl: './pasadas-editor.component.html',
})
export class PasadasEditorComponent implements OnInit, OnDestroy {
  layout:  CertLayout | null = null;
  block:   LayoutBlock | null = null;
  saving   = false;
  previewHtml: SafeHtml = '';
  previewLoading = false;
  previewError   = false;

  readonly allColumns = PASSES_COLUMNS;

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

  // ── Columnas ──────────────────────────────────────────────────────────────

  get activeColumns(): PassesColumnKey[] {
    const all = this.allColumns.map(c => c.key) as PassesColumnKey[];
    return (this.cfg['columns'] as PassesColumnKey[] | undefined) ?? all;
  }

  isActive(key: PassesColumnKey): boolean { return this.activeColumns.includes(key); }

  toggleCol(key: PassesColumnKey): void {
    const all  = this.allColumns.map(c => c.key) as PassesColumnKey[];
    const next = this.isActive(key)
      ? this.activeColumns.filter(c => c !== key)
      : [...this.activeColumns, key];
    this.setConfig('columns', next.length === all.length ? undefined : next);
  }

  moveCol(key: PassesColumnKey, dir: -1 | 1): void {
    const all  = this.allColumns.map(c => c.key) as PassesColumnKey[];
    const cols = [...(this.activeColumns.length ? this.activeColumns : all)];
    const i    = cols.indexOf(key);
    if (i < 0) return;
    const j = i + dir;
    if (j < 0 || j >= cols.length) return;
    [cols[i], cols[j]] = [cols[j], cols[i]];
    this.setConfig('columns', cols);
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
