import {
  ChangeDetectionStrategy,
  ChangeDetectorRef,
  Component,
  OnDestroy,
  OnInit,
  ViewChild,
} from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { CertLayout, LayoutBlock, LayoutJson } from '../../models/cert-layout.model';
import { CertLayoutService } from '../../services/cert-layout.service';
import { ToastService } from '../../../core/services/toast.service';
import { DesignerCanvasComponent, ZOOM_MIN, ZOOM_MAX } from '../../designer/canvas/designer-canvas.component';

@Component({
  selector: 'app-cabecera-editor',
  standalone: false,
  changeDetection: ChangeDetectionStrategy.OnPush,
  templateUrl: './cabecera-editor.component.html',
})
export class CabeceraEditorComponent implements OnInit, OnDestroy {
  @ViewChild(DesignerCanvasComponent) canvasComp!: DesignerCanvasComponent;

  layout:        CertLayout | null = null;
  block:         LayoutBlock | null = null;
  selectedBlock: LayoutBlock | null = null;
  saving         = false;
  zoom           = 1.0;

  /** Cacheado — mismo objeto hasta que se carga el bloque.
   *  Si fuera un getter que retorna objeto nuevo cada CD, Angular lo pasaría como
   *  @Input cambiado en cada ciclo → canvas.clear() en cada interacción del usuario. */
  canvasLayout:  LayoutJson | null = null;

  private destroy$ = new Subject<void>();

  constructor(
    private route:  ActivatedRoute,
    private router: Router,
    private svc:    CertLayoutService,
    private toast:  ToastService,
    public  cdr:    ChangeDetectorRef,
  ) {}

  ngOnInit(): void {
    const layoutId = +this.route.snapshot.paramMap.get('layoutId')!;
    const blockId  =  this.route.snapshot.paramMap.get('blockId')!;
    this.svc.getOne(layoutId).pipe(takeUntil(this.destroy$)).subscribe({
      next: layout => {
        this.layout = layout;
        this.block  = this.findBlock(layout, blockId);
        if (!this.block) { this.toast.error('Bloque no encontrado.'); this.goBack(); return; }
        this.canvasLayout = this.buildCanvasLayout(this.block);
        this.cdr.markForCheck();
      },
      error: () => { this.toast.error('Error al cargar.'); this.goBack(); },
    });
  }

  ngOnDestroy(): void { this.destroy$.next(); this.destroy$.complete(); }

  // ── Canvas ─────────────────────────────────────────────────────────────────

  private buildCanvasLayout(block: LayoutBlock): LayoutJson {
    const guide: LayoutBlock = {
      id: '__cabecera_limit__', type: 'line', direction: 'horizontal',
      x: 0, y: block.height, width: 210, height: 0.5,
      style: { color: '#2196f3' },
    };
    return {
      pageSize: 'A4', orientation: 'portrait',
      marginMm: { top: 0, right: 0, bottom: 0, left: 0 },
      pages: [{ pageNumber: 1, blocks: [...(block.blocks ?? []), guide] }],
    };
  }

  onBlockSelected(b: LayoutBlock | null): void { this.selectedBlock = b; this.cdr.markForCheck(); }

  onBlockChanged(updated: LayoutBlock): void {
    this.selectedBlock = updated;
    this.canvasComp.updateSelectedBlock(updated);
    this.cdr.markForCheck();
  }

  onDeleteSelected(): void {
    this.canvasComp.deleteSelected();
    this.selectedBlock = null;
    this.cdr.markForCheck();
  }

  changeZoom(f: number): void {
    this.zoom = Math.round(Math.max(ZOOM_MIN, Math.min(ZOOM_MAX, f)) * 100) / 100;
    this.cdr.markForCheck();
  }

  // ── Save ───────────────────────────────────────────────────────────────────

  save(): void {
    if (!this.layout || !this.block) return;
    this.saving = true;
    const subBlocks = this.canvasComp.getBlocks().filter(b => b.id !== '__cabecera_limit__');
    const updatedBlock: LayoutBlock = { ...this.block, blocks: subBlocks };
    const pages = (this.layout.blocks?.pages ?? []).map(p => ({
      ...p, blocks: (p.blocks ?? []).map(b => b.id === updatedBlock.id ? updatedBlock : b),
    }));
    this.svc.update(this.layout.id, { blocks: { ...this.layout.blocks, pages } }).pipe(takeUntil(this.destroy$)).subscribe({
      next: () => { this.saving = false; this.toast.success('Cabecera guardada.'); this.goBack(); },
      error: () => { this.saving = false; this.toast.error('Error al guardar.'); this.cdr.markForCheck(); },
    });
  }

  goBack(): void { this.router.navigate(['/plantillas', this.layout?.id ?? 0, 'editar']); }

  private findBlock(layout: CertLayout, id: string): LayoutBlock | null {
    for (const p of layout.blocks?.pages ?? []) for (const b of p.blocks ?? []) if (b.id === id) return b;
    return null;
  }
}
