import {
  AfterViewInit,
  ChangeDetectionStrategy,
  Component,
  ElementRef,
  EventEmitter,
  Input,
  NgZone,
  OnChanges,
  OnDestroy,
  Output,
  SimpleChanges,
  ViewChild,
} from '@angular/core';

import * as fabric from 'fabric';
import { LayoutBlock, LayoutJson } from '../../models/cert-layout.model';
import { environment } from '../../../../environments/environment';

export const ZOOM_MIN  = 0.25;
export const ZOOM_MAX  = 3.0;
export const ZOOM_STEP = 0.25;

/** Tipos de bloque que abren un editor dedicado con doble-click. */
export const DYNAMIC_BLOCK_TYPES = [
  'variables_block', 'results_block', 'passes_block', 'joint_design_block',
  'header_block', 'soldador_block',
] as const;

// ── SVG placeholders para imágenes no disponibles en el designer ──────────────

const SVG_BASE = environment.apiUrl.replace('/api', '');

const IMAGE_URLS: Record<string, string> = {
  logo:       `${SVG_BASE}/images/logos/kosoldar.png`,
  norma_logo: svgUri(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30">
    <rect width="60" height="30" fill="#f0f4ff"/>
    <text x="30" y="16" font-family="Arial" font-size="7" font-weight="bold" fill="#3a5bd9" text-anchor="middle">NORMA</text>
    <text x="30" y="24" font-family="Arial" font-size="5" fill="#888" text-anchor="middle">logo norma</text>
  </svg>`),
  qr: svgUri(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21">
    <rect width="21" height="21" fill="white"/>
    <rect x="0" y="0" width="8" height="8" fill="none" stroke="black" stroke-width="0.7"/>
    <rect x="1.5" y="1.5" width="5" height="5" fill="none" stroke="black" stroke-width="0.7"/>
    <rect x="3" y="3" width="2" height="2" fill="black"/>
    <rect x="13" y="0" width="8" height="8" fill="none" stroke="black" stroke-width="0.7"/>
    <rect x="14.5" y="1.5" width="5" height="5" fill="none" stroke="black" stroke-width="0.7"/>
    <rect x="16" y="3" width="2" height="2" fill="black"/>
    <rect x="0" y="13" width="8" height="8" fill="none" stroke="black" stroke-width="0.7"/>
    <rect x="1.5" y="14.5" width="5" height="5" fill="none" stroke="black" stroke-width="0.7"/>
    <rect x="3" y="16" width="2" height="2" fill="black"/>
    <rect x="9" y="0" width="1" height="1" fill="black"/><rect x="11" y="0" width="1" height="1" fill="black"/>
    <rect x="9" y="2" width="2" height="1" fill="black"/><rect x="9" y="4" width="1" height="2" fill="black"/>
    <rect x="9" y="9" width="3" height="1" fill="black"/><rect x="13" y="9" width="2" height="2" fill="black"/>
    <rect x="16" y="9" width="3" height="1" fill="black"/><rect x="9" y="11" width="1" height="2" fill="black"/>
    <rect x="11" y="12" width="2" height="1" fill="black"/><rect x="14" y="11" width="1" height="3" fill="black"/>
    <rect x="9" y="14" width="2" height="2" fill="black"/><rect x="15" y="15" width="2" height="1" fill="black"/>
    <rect x="18" y="14" width="2" height="3" fill="black"/>
  </svg>`),
  photo: svgUri(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 80">
    <rect width="60" height="80" fill="#f5f0eb"/>
    <circle cx="30" cy="25" r="12" fill="#c9b99a"/>
    <ellipse cx="30" cy="65" rx="20" ry="18" fill="#c9b99a"/>
  </svg>`),
  signature: svgUri(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 40">
    <rect width="100" height="40" fill="#f0fdf4"/>
    <path d="M10,30 C20,10 25,28 35,20 C45,12 50,30 60,22 C70,14 80,28 90,20"
          stroke="#2d6a4f" stroke-width="2" fill="none" stroke-linecap="round"/>
  </svg>`),
};

function svgUri(svg: string): string {
  return `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`;
}
import { GuidelineManager } from './guideline-manager';
import {
  blockToFabric,
  blocksFromCanvas,
  canvasThumbnail,
  clearSharedOverlays,
  exportLayout,
  getBlockData,
  loadBlocksIntoCanvas,
  loadLayout,
  loadSharedOverlays,
  mmToPx,
  pageHeightPx,
  pageWidthPx,
  pxToMm,
  snapMm,
} from '../utils/layout-serializer';

@Component({
  selector: 'app-designer-canvas',
  standalone: false,
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    <div class="canvas-scroll-area"
         (dragover)="onDragOver($event)"
         (drop)="onDrop($event)">
      <div class="canvas-page-shadow">
        <canvas #fabricCanvas></canvas>
      </div>
    </div>
  `,
  styles: [`
    :host { display: block; flex: 1; overflow: auto; background: #888; }
    .canvas-scroll-area { display: flex; justify-content: flex-start; align-items: flex-start; min-height: 100%; padding: 24px; }
    .canvas-page-shadow { box-shadow: 0 4px 24px rgba(0,0,0,.4); background: white; display: inline-block; }
  `],
})
export class DesignerCanvasComponent implements AfterViewInit, OnChanges, OnDestroy {
  @ViewChild('fabricCanvas', { static: true }) canvasEl!: ElementRef<HTMLCanvasElement>;

  @Input()  orientation:    'portrait' | 'landscape' = 'portrait';
  @Input()  initialLayout:  LayoutJson | null         = null;
  @Input()  zoom            = 1.0;

  @Output() blockSelected    = new EventEmitter<LayoutBlock | null>();
  @Output() canvasChanged    = new EventEmitter<void>();
  @Output() zoomChanged      = new EventEmitter<number>();
  @Output() blockDblClicked  = new EventEmitter<LayoutBlock>();

  private canvas!:     fabric.Canvas;
  private guidelines!: GuidelineManager;
  private _zoom        = 1.0;
  private _ready       = false;
  private _spaceDown   = false;

  // Limpieza de listeners globales
  private _removeKeyListeners!:  () => void;
  private _removePanListeners!:  () => void;

  // elRef.nativeElement = <app-designer-canvas> host = el elemento con overflow:auto
  constructor(private zone: NgZone, private elRef: ElementRef) {}

  ngAfterViewInit(): void {
    this.zone.runOutsideAngular(() => this.initCanvas());
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (!this._ready) return;
    if (changes['orientation']) this.resizeCanvas();
    if (changes['zoom']) this.applyZoom(this.zoom);
    if (changes['initialLayout'] && this.initialLayout) {
      this.zone.runOutsideAngular(() => loadLayout(this.canvas, this.initialLayout!));
    }
  }

  ngOnDestroy(): void {
    if (this.canvas) this.canvas.dispose();
    this._removeKeyListeners?.();
    this._removePanListeners?.();
  }

  // ── Public API called by shell ─────────────────────────────────────────────

  addBlock(block: LayoutBlock): void {
    this.zone.runOutsideAngular(() => {
      const obj = blockToFabric(block);
      this.canvas.add(obj);
      this.canvas.setActiveObject(obj);
      this.canvas.renderAll();
      if (block.type === 'image') {
        this.resolveImage(obj);   // async fire-and-forget
      }
    });
  }

  deleteSelected(): void {
    const active = this.canvas.getActiveObject();
    if (active) {
      this.canvas.remove(active);
      this.canvas.discardActiveObject();
      this.canvas.renderAll();
      this.zone.run(() => this.blockSelected.emit(null));
    }
  }

  updateSelectedBlock(block: LayoutBlock): void {
    const active = this.canvas.getActiveObject();
    if (!active) return;

    (active as any).data = { ...block };

    // If position/size changed via panel, apply to canvas object
    const newX = mmToPx(block.x);
    const newY = mmToPx(block.y);
    const newW = mmToPx(block.width);
    const newH = mmToPx(block.height);

    if (Math.abs((active.left ?? 0) - newX) > 0.5 || Math.abs((active.top ?? 0) - newY) > 0.5) {
      active.set({ left: newX, top: newY });
    }

    // For size, replace object entirely (Fabric groups don't resize cleanly)
    if (Math.abs(active.getScaledWidth() - newW) > 1 || Math.abs(active.getScaledHeight() - newH) > 1) {
      this.canvas.remove(active);
      const updated = blockToFabric({ ...block });
      this.canvas.add(updated);
      this.canvas.setActiveObject(updated);
    }

    this.canvas.renderAll();
    this.zone.run(() => this.canvasChanged.emit());
  }

  export(meta: { nombre: string; orientacion: 'portrait' | 'landscape'; normaIds: number[] | null }) {
    return exportLayout(this.canvas, meta);
  }

  /** Blocks de la página actualmente mostrada en el canvas (coordenadas actuales). */
  getBlocks(): LayoutBlock[] {
    return blocksFromCanvas(this.canvas);
  }

  /** Reemplaza el contenido del canvas por los blocks de otra página (preserva la grilla). */
  loadBlocks(blocks: LayoutBlock[]): void {
    this.zone.runOutsideAngular(() => {
      loadBlocksIntoCanvas(this.canvas, blocks);
      this.loadRealImages();   // async fire-and-forget
    });
    this.zone.run(() => this.blockSelected.emit(null));
  }

  getThumbnail(): string {
    return canvasThumbnail(this.canvas);
  }

  showSharedOverlays(sharedBlocks: LayoutBlock[]): void {
    this.zone.runOutsideAngular(() => loadSharedOverlays(this.canvas, sharedBlocks));
  }

  hideSharedOverlays(): void {
    this.zone.runOutsideAngular(() => clearSharedOverlays(this.canvas));
  }

  // ── Image resolution ─────────────────────────────────────────────────────

  /** Carga imágenes reales de forma async para todos los bloques de tipo image del canvas. */
  async loadRealImages(): Promise<void> {
    const imageObjs = this.canvas.getObjects().filter(o => {
      return getBlockData(o)?.type === 'image';
    });
    await Promise.all(imageObjs.map(o => this.resolveImage(o)));
  }

  private async resolveImage(obj: fabric.FabricObject): Promise<void> {
    const data = getBlockData(obj) as LayoutBlock;
    const url  = IMAGE_URLS[data.imageType ?? ''] ?? null;
    if (!url) return;

    try {
      const img = await fabric.Image.fromURL(url, { crossOrigin: 'anonymous' });

      // Dimensiones visuales del placeholder original
      const w = obj.getScaledWidth();
      const h = obj.getScaledHeight();
      img.set({
        left:   obj.left ?? 0,
        top:    obj.top  ?? 0,
        scaleX: w / (img.width  ?? w),
        scaleY: h / (img.height ?? h),
      });
      (img as any).data = data;

      const wasActive = this.canvas.getActiveObject() === obj;
      this.canvas.remove(obj);
      this.canvas.add(img);
      if (wasActive) this.canvas.setActiveObject(img);
      this.canvas.renderAll();
    } catch {
      // Si falla la carga, el placeholder original permanece
    }
  }

  applyZoom(factor: number): void {
    this._zoom = Math.max(ZOOM_MIN, Math.min(ZOOM_MAX, factor));
    const baseW = pageWidthPx(this.orientation);
    const baseH = pageHeightPx(this.orientation);
    this.canvas.setZoom(this._zoom);
    this.canvas.setDimensions({ width: baseW * this._zoom, height: baseH * this._zoom });
    this.canvas.renderAll();
  }

  // ── Init ───────────────────────────────────────────────────────────────────

  private initCanvas(): void {
    const w = pageWidthPx(this.orientation);
    const h = pageHeightPx(this.orientation);

    this.canvas = new fabric.Canvas(this.canvasEl.nativeElement, {
      width:               w,
      height:              h,
      backgroundColor:     '#ffffff',
      selection:           true,
      preserveObjectStacking: true,
    });

    this.drawGrid();

    this.guidelines = new GuidelineManager(this.canvas);

    this.canvas.on('selection:created',  (e: any) => this.onSelect(e));
    this.canvas.on('selection:updated',  (e: any) => this.onSelect(e));
    this.canvas.on('selection:cleared',  ()        => this.zone.run(() => this.blockSelected.emit(null)));
    this.canvas.on('mouse:dblclick',      (e: any)  => {
      const obj  = e.target;
      if (!obj) return;
      const data = getBlockData(obj);
      if (data && (DYNAMIC_BLOCK_TYPES as readonly string[]).includes(data.type)) {
        this.zone.run(() => this.blockDblClicked.emit(data));
      }
    });
    this.canvas.on('object:moving',      (e: any)  => {
      // Usa las dimensiones lógicas del canvas (sin zoom) para que las guías
      // funcionen igual en sub-designer (cabecera) que en designer principal (A4).
      const w = (this.canvas.width!  || pageWidthPx(this.orientation))  / this._zoom;
      const h = (this.canvas.height! || pageHeightPx(this.orientation)) / this._zoom;
      this.guidelines.update(e.target, w, h);
    });
    this.canvas.on('object:modified',    ()        => {
      this.guidelines.clear();
      this.zone.run(() => this.canvasChanged.emit());
    });

    if (this.initialLayout) {
      loadLayout(this.canvas, this.initialLayout);
      this.loadRealImages();
    }

    // Rueda del mouse sobre el canvas → zoom (sin modificador).
    // El handler está en canvas-page-shadow, solo actúa cuando el cursor está sobre la hoja.
    const el = this.canvasEl.nativeElement.parentElement ?? this.canvasEl.nativeElement;
    el.addEventListener('wheel', (e: WheelEvent) => {
      e.preventDefault();
      const step  = e.deltaY < 0 ? ZOOM_STEP : -ZOOM_STEP;
      const next  = Math.round(Math.max(ZOOM_MIN, Math.min(ZOOM_MAX, this._zoom + step)) * 100) / 100;
      this.zone.run(() => this.zoomChanged.emit(next));
    }, { passive: false });

    this._ready = true;
    this.initPanning();

    // Aplica el zoom inicial si fue pasado como Input antes de que el canvas existiera.
    // ngOnChanges se ejecuta antes de ngAfterViewInit y no puede tocar el canvas aún.
    if (this.zoom !== 1.0) {
      this.applyZoom(this.zoom);
    }
  }

  private initPanning(): void {
    const canvasEl = this.canvasEl.nativeElement as HTMLElement;
    // El scrollable es el HOST <app-designer-canvas> con :host{overflow:auto},
    // NO .canvas-scroll-area (que es un div interior sin overflow configurado).
    const host = this.elRef.nativeElement as HTMLElement;

    let panActive  = false;
    let panDragged = false;
    let panStart   = { x: 0, y: 0, sl: 0, st: 0 };

    const startPan = (clientX: number, clientY: number) => {
      panActive  = true;
      panDragged = false;
      panStart   = { x: clientX, y: clientY, sl: host.scrollLeft, st: host.scrollTop };
    };
    const doPan = (clientX: number, clientY: number) => {
      if (!panActive) return;
      const dx = clientX - panStart.x;
      const dy = clientY - panStart.y;
      if (!panDragged && Math.hypot(dx, dy) < 5) return;
      panDragged = true;
      canvasEl.style.cursor = 'grabbing';
      host.scrollLeft = panStart.sl - dx;
      host.scrollTop  = panStart.st - dy;
    };
    const stopPan = () => {
      if (!panActive) return;
      panActive  = false;
      panDragged = false;
      this.canvas.selection = true;
      canvasEl.style.cursor = this._spaceDown ? 'grab' : '';
    };

    // ── Space + arrastrar ──────────────────────────────────────────────────
    const onKeyDown = (e: KeyboardEvent) => {
      if (e.code === 'Space' && !e.repeat && document.activeElement?.tagName !== 'INPUT'
                                          && document.activeElement?.tagName !== 'TEXTAREA'
                                          && document.activeElement?.tagName !== 'SELECT') {
        e.preventDefault();
        this._spaceDown = true;
        canvasEl.style.cursor = 'grab';
      }
    };
    const onKeyUp = (e: KeyboardEvent) => {
      if (e.code === 'Space') {
        this._spaceDown = false;
        stopPan();
      }
    };

    // Space + drag: captura antes de Fabric con capture:true
    const onCanvasMouseDown = (e: MouseEvent) => {
      if (e.button !== 0 || !this._spaceDown) return;
      e.preventDefault();
      e.stopPropagation();
      startPan(e.clientX, e.clientY);
    };

    // Click izquierdo en canvas vacío: Fabric detecta ev.target===null
    this.canvas.on('mouse:down', (ev: any) => {
      if (this._spaceDown || ev.e.button !== 0 || ev.target) return;
      this.canvas.selection = false;
      startPan(ev.e.clientX, ev.e.clientY);
    });

    // Botón del medio
    const onMiddleDown = (e: MouseEvent) => {
      if (e.button === 1) { e.preventDefault(); startPan(e.clientX, e.clientY); }
    };

    const onMouseMove = (e: MouseEvent) => doPan(e.clientX, e.clientY);
    const onMouseUp   = (e: MouseEvent) => { if (e.button === 0 || e.button === 1) stopPan(); };

    canvasEl.addEventListener('mousedown', onCanvasMouseDown, { capture: true, passive: false });
    host.addEventListener('mousedown',     onMiddleDown,      { passive: false });
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup',   onMouseUp);
    document.addEventListener('keydown',   onKeyDown);
    document.addEventListener('keyup',     onKeyUp);

    this._removeKeyListeners = () => {
      document.removeEventListener('keydown', onKeyDown);
      document.removeEventListener('keyup',   onKeyUp);
    };
    this._removePanListeners = () => {
      canvasEl.removeEventListener('mousedown', onCanvasMouseDown, true);
      host.removeEventListener('mousedown',     onMiddleDown);
      document.removeEventListener('mousemove', onMouseMove);
      document.removeEventListener('mouseup',   onMouseUp);
    };
  }

  private drawGrid(): void {
    const w   = this.canvas.width!;
    const h   = this.canvas.height!;
    const gap = mmToPx(5); // 5mm grid

    const lines: fabric.Line[] = [];
    for (let x = 0; x <= w; x += gap) {
      lines.push(new fabric.Line([x, 0, x, h], { stroke: '#e8e8e8', strokeWidth: 0.5, selectable: false, evented: false }));
    }
    for (let y = 0; y <= h; y += gap) {
      lines.push(new fabric.Line([0, y, w, y], { stroke: '#e8e8e8', strokeWidth: 0.5, selectable: false, evented: false }));
    }
    lines.forEach(l => {
      (l as any).__isGrid = true;
      this.canvas.add(l);
    });
    lines.forEach(l => this.canvas.sendObjectToBack(l));
  }

  private resizeCanvas(): void {
    const w = pageWidthPx(this.orientation)  * this._zoom;
    const h = pageHeightPx(this.orientation) * this._zoom;
    this.canvas.setZoom(this._zoom);
    this.canvas.setDimensions({ width: w, height: h });
    const gridLines = this.canvas.getObjects().filter(o => (o as any).__isGrid);
    gridLines.forEach(l => this.canvas.remove(l));
    this.drawGrid();
    this.canvas.renderAll();
  }

  // ── Drag & Drop ────────────────────────────────────────────────────────────

  onDragOver(evt: DragEvent): void {
    evt.preventDefault();
    if (evt.dataTransfer) evt.dataTransfer.dropEffect = 'copy';
  }

  onDrop(evt: DragEvent): void {
    evt.preventDefault();
    const json = evt.dataTransfer?.getData('application/json');
    if (!json) return;

    try {
      const block = JSON.parse(json) as LayoutBlock;

      const canvasRect = this.canvasEl.nativeElement.getBoundingClientRect();
      const rawX = (evt.clientX - canvasRect.left) / this._zoom;
      const rawY = (evt.clientY - canvasRect.top)  / this._zoom;
      const maxXmm = pxToMm(this.canvas.width!  / this._zoom) - block.width;
      const maxYmm = pxToMm(this.canvas.height! / this._zoom) - block.height;
      block.x = snapMm(Math.max(0, Math.min(maxXmm, pxToMm(rawX))));
      block.y = snapMm(Math.max(0, Math.min(maxYmm, pxToMm(rawY))));
      this.zone.run(() => this.addBlock(block));
    } catch { /* ignore malformed drag data */ }
  }

  private onSelect(e: any): void {
    const obj = e.selected?.[0] ?? this.canvas.getActiveObject();
    if (!obj) return;
    const data = getBlockData(obj);
    if (!data) return;

    // Sync canvas position back into block data
    const synced: LayoutBlock = {
      ...data,
      x:      snapMm(pxToMm(obj.left ?? 0)),
      y:      snapMm(pxToMm(obj.top  ?? 0)),
      width:  snapMm(pxToMm(obj.getScaledWidth())),
      height: snapMm(pxToMm(obj.getScaledHeight())),
    };
    (obj as any).data = synced;   // mutación directa — no hay helper de escritura tipado aún
    this.zone.run(() => this.blockSelected.emit(synced));
  }
}
