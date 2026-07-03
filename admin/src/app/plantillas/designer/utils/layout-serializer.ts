import { LayoutBlock, LayoutJson, BlockStyle } from '../../models/cert-layout.model';
import * as fabric from 'fabric';

export const MM_TO_PX = 3;   // 1mm = 3px  →  A4 portrait = 630×891px

/** Extrae el LayoutBlock adjunto a un objeto Fabric.js de forma tipada. */
export function getBlockData(obj: fabric.FabricObject): LayoutBlock | undefined {
  return (obj as any).data as LayoutBlock | undefined;
}

/** Adjunta un LayoutBlock a un objeto Fabric.js. */
function attachBlockData(obj: fabric.FabricObject, block: LayoutBlock): void {
  (obj as any).data = { ...block };
}

export function mmToPx(mm: number): number  { return mm * MM_TO_PX; }
export function pxToMm(px: number): number  { return px / MM_TO_PX; }

/** Snap value to 0.5mm grid */
export function snapMm(mm: number): number  { return Math.round(mm * 2) / 2; }

export function pageWidthPx(orientation: 'portrait' | 'landscape'):  number {
  return mmToPx(orientation === 'landscape' ? 297 : 210);
}
export function pageHeightPx(orientation: 'portrait' | 'landscape'): number {
  return mmToPx(orientation === 'landscape' ? 210 : 297);
}

// ── Block → Fabric object ─────────────────────────────────────────────────────

export function blockToFabric(block: LayoutBlock): fabric.FabricObject {
  const x  = mmToPx(block.x);
  const y  = mmToPx(block.y);
  const w  = mmToPx(block.width);
  const h  = mmToPx(block.height);
  const s  = block.style ?? {};

  switch (block.type) {
    case 'field':
      return makeFieldObj(block, x, y, w, h, s);
    case 'text':
      return makeTextObj(block, x, y, w, h, s);
    case 'image':
      return makeImagePlaceholder(block, x, y, w, h);
    case 'line':
      return makeLineObj(block, x, y, w, h, s);
    case 'rect':
      return makeRectObj(block, x, y, w, h, s);
    default:
      return makeDynamicPlaceholder(block, x, y, w, h);
  }
}

function makeFieldObj(block: LayoutBlock, x: number, y: number, w: number, h: number, s: BlockStyle): fabric.Group {
  const rect = new fabric.Rect({
    left: 0, top: 0, width: w, height: h,
    fill: s.backgroundColor ?? 'transparent',
    stroke: s.borderWidth ? (s.borderColor ?? '#cccccc') : 'transparent',
    strokeWidth: s.borderWidth ?? 0,
    rx: 1, ry: 1,
  });
  const label = new fabric.Text(block.fieldKey ?? '(campo)', {
    left: 3, top: 2,
    fontSize: (s.fontSize ?? 10) * MM_TO_PX * 0.38,
    fill: s.color ?? '#000000',
    fontWeight: s.fontWeight ?? 'normal',
    fontFamily: 'Arial',
    textAlign: (s.textAlign ?? 'left') as any,
    width: w - 6,
  });
  const grp = new fabric.Group([rect, label], {
    left: x, top: y, width: w, height: h,
    subTargetCheck: false,
  });
  attachBlockData(grp, block);
  return grp;
}

function makeTextObj(block: LayoutBlock, x: number, y: number, w: number, h: number, s: BlockStyle): fabric.Group {
  const rect = new fabric.Rect({
    left: 0, top: 0, width: w, height: h,
    fill: s.backgroundColor ?? 'transparent',
    stroke: s.borderWidth ? (s.borderColor ?? '#cccccc') : '#e0e0e0',
    strokeWidth: s.borderWidth ?? 0.5,
    strokeDashArray: [3, 3],
  });
  const label = new fabric.Text(block.content ?? '(texto)', {
    left: 3, top: 2,
    fontSize: (s.fontSize ?? 10) * MM_TO_PX * 0.38,
    fill: s.color ?? '#333333',
    fontFamily: 'Arial',
    textAlign: (s.textAlign ?? 'left') as any,
    width: w - 6,
  });
  const grp = new fabric.Group([rect, label], {
    left: x, top: y, width: w, height: h,
  });
  attachBlockData(grp, block);
  return grp;
}

function makeImagePlaceholder(block: LayoutBlock, x: number, y: number, w: number, h: number): fabric.Group {
  const colors: Record<string, string> = {
    logo: '#e8f4fd', norma_logo: '#e8f4fd', photo: '#fdf0e8', signature: '#f0fdf4', qr: '#fafafe',
  };
  const icons: Record<string, string> = {
    logo: 'LOGO', norma_logo: 'LOGO\nNORMA', photo: 'FOTO', signature: 'FIRMA', qr: 'QR',
  };
  const bg = colors[block.imageType ?? ''] ?? '#f5f5f5';
  const lbl = icons[block.imageType ?? ''] ?? (block.imageType ?? 'IMG');

  const rect = new fabric.Rect({ left: 0, top: 0, width: w, height: h, fill: bg, stroke: '#cccccc', strokeWidth: 0.5 });
  const label = new fabric.Text(lbl, {
    left: w / 2, top: h / 2,
    originX: 'center', originY: 'center',
    fontSize: Math.min(w, h) * 0.18,
    fill: '#666666',
    fontFamily: 'Arial',
    fontWeight: 'bold',
    textAlign: 'center',
  });
  const grp = new fabric.Group([rect, label], { left: x, top: y, width: w, height: h });
  attachBlockData(grp, block);
  return grp;
}

function makeRectObj(block: LayoutBlock, x: number, y: number, w: number, h: number, s: BlockStyle): fabric.Rect {
  const fill   = s.backgroundColor ?? 'transparent';
  const stroke = s.borderWidth && s.borderWidth > 0 ? (s.borderColor ?? '#000000') : 'transparent';
  const sw     = s.borderWidth ?? 0;
  const rect   = new fabric.Rect({ left: x, top: y, width: w, height: h, fill, stroke, strokeWidth: sw });
  attachBlockData(rect, block);
  return rect;
}

function makeLineObj(block: LayoutBlock, x: number, y: number, w: number, h: number, s: BlockStyle): fabric.Line {
  const color = s.color ?? '#000000';
  const isV   = block.direction === 'vertical';
  const line  = isV
    ? new fabric.Line([0, 0, 0, h], { left: x + w / 2, top: y, stroke: color, strokeWidth: 1 })
    : new fabric.Line([0, 0, w, 0], { left: x, top: y + h / 2, stroke: color, strokeWidth: 1 });
  attachBlockData(line, block);
  return line;
}

function makeDynamicPlaceholder(block: LayoutBlock, x: number, y: number, w: number, h: number): fabric.Group {
  const colors: Record<string, string> = {
    variables_block:    '#fffbeb',
    results_block:      '#f0fdf4',
    passes_block:       '#eff6ff',
    joint_design_block: '#faf5ff',
    header_block:       '#e0f0ff',
    soldador_block:     '#fdecea',
  };
  const labels: Record<string, string> = {
    variables_block:    '⊞ Variables de soldadura',
    results_block:      '✓ Resultados / Ensayos',
    passes_block:       '≡ Tabla de pasadas',
    joint_design_block: '◈ Diseño de junta',
    header_block:       '☰ Cabecera — doble-click para editar',
    soldador_block:     '👤 Sección soldador — doble-click para editar',
  };
  const bg  = colors[block.type] ?? '#f5f5f5';
  const lbl = labels[block.type] ?? block.type;

  const rect = new fabric.Rect({
    left: 0, top: 0, width: w, height: h,
    fill: bg,
    stroke: '#aaaaaa',
    strokeWidth: 0.5,
    strokeDashArray: [5, 3],
  });
  const label = new fabric.Text(lbl, {
    left: w / 2, top: h / 2,
    originX: 'center', originY: 'center',
    fontSize: Math.min(12, h * 0.3),
    fill: '#555555',
    fontFamily: 'Arial',
    fontStyle: 'italic',
    textAlign: 'center',
    width: w - 8,
  });
  const grp = new fabric.Group([rect, label], { left: x, top: y, width: w, height: h });
  attachBlockData(grp, block);
  return grp;
}

// ── Canvas → JSON ─────────────────────────────────────────────────────────────

/** Extrae los blocks (con coordenadas actuales) de un canvas — una sola página.
 *  Excluye grilla, guías de snap y overlays de bloques compartidos. */
export function blocksFromCanvas(canvas: fabric.Canvas): LayoutBlock[] {
  const blocks: LayoutBlock[] = [];

  canvas.getObjects().forEach(obj => {
    if ((obj as any).__isGrid || (obj as any).__guide || (obj as any).__sharedOverlay) return;
    const d = (obj as any).data as LayoutBlock | undefined;
    if (!d) return;

    const x = snapMm(pxToMm(obj.left ?? 0));
    const y = snapMm(pxToMm(obj.top  ?? 0));
    const w = snapMm(pxToMm(obj.getScaledWidth()));
    const h = snapMm(pxToMm(obj.getScaledHeight()));

    blocks.push({ ...d, x, y, width: w, height: h });
  });

  return blocks;
}

/** Renderiza bloques compartidos (de página 1) como capa dimmed/bloqueada en páginas 2+. */
export function loadSharedOverlays(canvas: fabric.Canvas, sharedBlocks: LayoutBlock[]): void {
  // Eliminar overlays anteriores
  canvas.getObjects()
    .filter(o => (o as any).__sharedOverlay)
    .forEach(o => canvas.remove(o));

  for (const block of sharedBlocks) {
    const obj = blockToFabric(block);
    obj.set({ selectable: false, evented: false, opacity: 0.3 });
    (obj as any).__sharedOverlay = true;
    canvas.add(obj);
    canvas.sendObjectToBack(obj);
  }
  canvas.renderAll();
}

/** Elimina todos los overlays de bloques compartidos del canvas. */
export function clearSharedOverlays(canvas: fabric.Canvas): void {
  canvas.getObjects()
    .filter(o => (o as any).__sharedOverlay)
    .forEach(o => canvas.remove(o));
  canvas.renderAll();
}

export function canvasThumbnail(canvas: fabric.Canvas): string {
  return canvas.toDataURL({ format: 'png', multiplier: 0.4 });
}

/** @deprecated single-page export — usar blocksFromCanvas + armar pages[] en el componente shell. */
export function exportLayout(
  canvas: fabric.Canvas,
  meta: { nombre: string; orientacion: 'portrait' | 'landscape'; normaIds: number[] | null },
): { blocks: LayoutJson; thumbnail: string } {
  return {
    blocks: {
      pageSize: 'A4',
      orientation: meta.orientacion,
      marginMm: { top: 0, right: 0, bottom: 0, left: 0 },
      pages: [{ pageNumber: 1, blocks: blocksFromCanvas(canvas) }],
    },
    thumbnail: canvasThumbnail(canvas),
  };
}

// ── JSON → Canvas ─────────────────────────────────────────────────────────────

/** Reemplaza bloques normales (preserva grilla y shared overlays). */
export function loadBlocksIntoCanvas(canvas: fabric.Canvas, blocks: LayoutBlock[]): void {
  const toRemove = canvas.getObjects().filter(o => !(o as any).__isGrid && !(o as any).__sharedOverlay);
  toRemove.forEach(o => canvas.remove(o));

  for (const block of blocks) {
    canvas.add(blockToFabric(block));
  }
  canvas.renderAll();
}

/** Carga la página 1 de un layout completo — usado sólo en la carga inicial del canvas. */
export function loadLayout(canvas: fabric.Canvas, layoutJson: LayoutJson): void {
  canvas.clear();
  const page = layoutJson.pages?.[0];
  if (!page) return;

  for (const block of page.blocks) {
    const obj = blockToFabric(block);
    canvas.add(obj);
  }
  canvas.renderAll();
}
