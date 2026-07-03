import { ChangeDetectorRef, Component, OnInit, OnDestroy, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { CertLayout, LayoutBlock } from '../models/cert-layout.model';
import { CertLayoutService, Norma } from '../services/cert-layout.service';
import { ToastService } from '../../core/services/toast.service';
import { DesignerCanvasComponent } from './canvas/designer-canvas.component';

@Component({
  selector: 'app-cert-layout-designer',
  standalone: false,
  templateUrl: './cert-layout-designer.component.html',
})
export class CertLayoutDesignerComponent implements OnInit, OnDestroy {
  @ViewChild(DesignerCanvasComponent) canvasComp!: DesignerCanvasComponent;

  layoutId:     number | null = null;
  existing:     CertLayout | null = null;

  nombre       = 'Nueva plantilla';
  descripcion  = '';
  orientacion: 'portrait' | 'landscape' = 'portrait';
  esDefault    = false;
  selectedNormaIds: Set<number> = new Set();

  allNormas:       Norma[] = [];
  normaPickerOpen  = false;
  normaPickerPos   = { top: 0, left: 0 };

  get selectedNormaLabel(): string {
    if (this.selectedNormaIds.size === 0) return 'Sin norma asignada';
    const names = this.allNormas
      .filter(n => this.selectedNormaIds.has(n.id))
      .map(n => n.nombre);
    if (names.length <= 2) return names.join(', ');
    return `${names[0]}, ${names[1]} +${names.length - 2}`;
  }

  openNormaPicker(btn: HTMLElement): void {
    const r = btn.getBoundingClientRect();
    this.normaPickerPos  = { top: r.bottom + 4, left: r.left };
    this.normaPickerOpen = !this.normaPickerOpen;
    this.cdr.markForCheck();
  }

  selectedBlock:      LayoutBlock | null = null;
  saving              = false;
  previewing          = false;
  previewPdf:         SafeResourceUrl | null = null;

  /** Blocks de cada página — fuente de verdad para multi-página. El canvas sólo muestra `pages[activePageIndex]`. */
  pages:           LayoutBlock[][] = [[]];
  activePageIndex  = 0;
  zoom             = 1.0;

  private destroy$ = new Subject<void>();

  constructor(
    private route:     ActivatedRoute,
    private router:    Router,
    private svc:       CertLayoutService,
    private toast:     ToastService,
    public  cdr:       ChangeDetectorRef,
    private sanitizer: DomSanitizer,
  ) {}

  ngOnInit(): void {
    // Carga lista de normas para el selector visual
    this.svc.getNormas().pipe(takeUntil(this.destroy$)).subscribe({
      next: normas => { this.allNormas = normas; this.cdr.markForCheck(); },
    });

    const idParam = this.route.snapshot.paramMap.get('id');
    if (idParam) {
      this.layoutId = +idParam;
      this.svc.getOne(this.layoutId).pipe(takeUntil(this.destroy$)).subscribe({
        next: layout => {
          this.existing         = layout;
          this.nombre           = layout.nombre;
          this.descripcion      = layout.descripcion ?? '';
          this.orientacion      = layout.orientacion;
          this.esDefault        = layout.es_default;
          this.selectedNormaIds = new Set(layout.norma_ids ?? []);
          this.pages = (layout.blocks?.pages ?? [{ pageNumber: 1, blocks: [] }])
            .map(p => p.blocks ?? []);
          if (!this.pages.length) this.pages = [[]];
          this.activePageIndex = 0;
          this.cdr.markForCheck();
        },
        error: () => { this.toast.error('Error al cargar plantilla.'); this.router.navigate(['/plantillas']); },
      });
    }
  }

  ngOnDestroy(): void { this.destroy$.next(); this.destroy$.complete(); }

  get initialLayout() { return this.existing?.blocks ?? null; }

  onBlockSelected(block: LayoutBlock | null): void {
    this.selectedBlock = block;
    this.cdr.markForCheck();
  }

  onBlockChanged(block: LayoutBlock): void {
    this.selectedBlock = block;
    this.canvasComp.updateSelectedBlock(block);
    this.cdr.markForCheck();
  }

  onDeleteSelected(): void {
    this.canvasComp.deleteSelected();
    this.selectedBlock = null;
    this.cdr.markForCheck();
  }

  onOrientacionChange(val: 'portrait' | 'landscape'): void {
    this.orientacion = val;
    this.cdr.markForCheck();
  }

  // ── Páginas ───────────────────────────────────────────────────────────────

  get sharedBlocks(): LayoutBlock[] {
    return (this.pages[0] ?? []).filter(b => b.shared === true);
  }

  switchPage(index: number): void {
    if (index === this.activePageIndex || index < 0 || index >= this.pages.length) return;
    this.pages[this.activePageIndex] = this.canvasComp.getBlocks();
    this.activePageIndex = index;
    this.canvasComp.loadBlocks(this.pages[index]);
    if (index > 0 && this.sharedBlocks.length) {
      this.canvasComp.showSharedOverlays(this.sharedBlocks);
    } else {
      this.canvasComp.hideSharedOverlays();
    }
    this.selectedBlock = null;
    this.cdr.markForCheck();
  }

  changeZoom(factor: number): void {
    this.zoom = Math.round(Math.max(0.25, Math.min(3.0, factor)) * 100) / 100;
    this.cdr.markForCheck();
  }

  addPage(): void {
    this.pages[this.activePageIndex] = this.canvasComp.getBlocks();
    this.pages.push([]);
    this.switchPage(this.pages.length - 1);
  }

  removePage(index: number, evt: Event): void {
    evt.stopPropagation();
    if (this.pages.length <= 1) return;

    this.pages.splice(index, 1);
    const newIndex = Math.min(index, this.pages.length - 1);

    if (index === this.activePageIndex) {
      this.canvasComp.loadBlocks(this.pages[newIndex]);
      this.activePageIndex = newIndex;
    } else if (index < this.activePageIndex) {
      this.activePageIndex -= 1;
    }
    this.selectedBlock = null;
    this.cdr.markForCheck();
  }

  save(): void {
    this.saving = true;
    this.pages[this.activePageIndex] = this.canvasComp.getBlocks();

    const blocks = {
      pageSize:    'A4',
      orientation: this.orientacion,
      marginMm:    { top: 0, right: 0, bottom: 0, left: 0 },
      pages:       this.pages.map((pageBlocks, i) => ({ pageNumber: i + 1, blocks: pageBlocks })),
    };

    const payload = {
      nombre:      this.nombre,
      descripcion: this.descripcion || null,
      norma_ids:   this.parseNormaIds(),
      es_default:  this.esDefault,
      orientacion: this.orientacion,
      blocks,
      thumbnail:   this.canvasComp.getThumbnail(),
    };

    const req$ = this.layoutId
      ? this.svc.update(this.layoutId, payload)
      : this.svc.create(payload);

    req$.pipe(takeUntil(this.destroy$)).subscribe({
      next: saved => {
        this.saving   = false;
        this.layoutId = saved.id;
        this.existing = saved;
        this.toast.success('Plantilla guardada.');
        this.cdr.markForCheck();
        if (!this.route.snapshot.paramMap.get('id')) {
          this.router.navigate(['/plantillas', saved.id, 'editar'], { replaceUrl: true });
        }
      },
      error: () => { this.saving = false; this.toast.error('Error al guardar.'); this.cdr.markForCheck(); },
    });
  }

  openPreview(): void {
    if (!this.layoutId) { this.toast.error('Guardá la plantilla antes de previsualizar.'); return; }
    this.previewing = true;
    this.svc.preview(this.layoutId).pipe(takeUntil(this.destroy$)).subscribe({
      next: res => {
        this.previewing = false;
        this.previewPdf = this.sanitizer.bypassSecurityTrustResourceUrl(res.pdf);
        this.cdr.markForCheck();
      },
      error: () => {
        this.previewing = false;
        this.toast.error('Error generando preview. ¿Hay certificados cargados?');
        this.cdr.markForCheck();
      },
    });
  }

  closePreview(): void { this.previewPdf = null; }

  openBlockEditor(block: LayoutBlock): void {
    if (!this.layoutId) {
      this.toast.error('Guardá la plantilla antes de editar el bloque.');
      return;
    }
    const routeSegment: Record<string, string> = {
      header_block:       'cabecera',
      soldador_block:     'soldador',
      variables_block:    'variables',
      passes_block:       'pasadas',
      results_block:      'resultados',
      joint_design_block: 'junta',
    };
    const segment = routeSegment[block.type];
    if (!segment) { this.toast.error(`Sin editor para tipo: ${block.type}`); return; }
    this.pages[this.activePageIndex] = this.canvasComp.getBlocks();
    this.router.navigate(['/plantillas', this.layoutId, 'editar', segment, block.id]);
  }


  onCanvasChanged(): void { this.cdr.markForCheck(); }

  goBack(): void { this.router.navigate(['/plantillas']); }

  toggleNorma(id: number): void {
    this.selectedNormaIds.has(id) ? this.selectedNormaIds.delete(id) : this.selectedNormaIds.add(id);
    this.cdr.markForCheck();
  }

  private parseNormaIds(): number[] | null {
    const ids = Array.from(this.selectedNormaIds);
    return ids.length ? ids : null;
  }
}
