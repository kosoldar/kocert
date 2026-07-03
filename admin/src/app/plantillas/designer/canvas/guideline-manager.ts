import * as fabric from 'fabric';
import { LayoutBlock } from '../../models/cert-layout.model';

const SNAP_PX   = 4;   // threshold de snap en px (~1.3mm a 3px/mm)
const GUIDE_CLR = '#1e90ff';

interface SnapHit { guidePos: number; newOrigin: number; delta: number; }

export class GuidelineManager {
  private guides: fabric.Line[] = [];

  constructor(private canvas: fabric.Canvas) {}

  /** Llamar en object:moving — alinea el objeto y dibuja guías. */
  update(obj: fabric.FabricObject, pageW: number, pageH: number): void {
    this.clear();

    const others = this.canvas.getObjects().filter(
      o => o !== obj
        && !(o as any).__isGrid
        && !(o as any).__guide
        && !(o as any).__sharedOverlay
        && (o as any).data,   // solo bloques reales
    );

    const oL  = obj.left  ?? 0;
    const oT  = obj.top   ?? 0;
    const oW  = obj.getScaledWidth();
    const oH  = obj.getScaledHeight();
    const oCX = oL + oW / 2;
    const oCY = oT + oH / 2;
    const oR  = oL + oW;
    const oB  = oT + oH;

    // ── X candidates (vertical guide lines) ──────────────────────────────
    const xCands: SnapHit[] = [];

    const addX = (source: number, target: number, originOffset: number) => {
      const d = Math.abs(source - target);
      if (d < SNAP_PX) xCands.push({ guidePos: target, newOrigin: target - originOffset, delta: d });
    };

    // Page edges + center
    addX(oL,  0,      0);
    addX(oR,  pageW,  oW);
    addX(oCX, pageW / 2, oW / 2);

    for (const other of others) {
      const tL  = other.left  ?? 0;
      const tW  = other.getScaledWidth();
      const tCX = tL + tW / 2;
      const tR  = tL + tW;

      addX(oL,  tL,  0);       // obj-left aligns other-left
      addX(oL,  tR,  0);       // obj-left aligns other-right
      addX(oR,  tL,  oW);      // obj-right aligns other-left
      addX(oR,  tR,  oW);      // obj-right aligns other-right
      addX(oCX, tCX, oW / 2);  // centers align
    }

    const bestX = this.best(xCands);
    if (bestX) {
      obj.set({ left: bestX.newOrigin });
      this.drawLine(bestX.guidePos, 0, bestX.guidePos, pageH, 'v');
    }

    // ── Y candidates (horizontal guide lines) ─────────────────────────────
    const yCands: SnapHit[] = [];

    const addY = (source: number, target: number, originOffset: number) => {
      const d = Math.abs(source - target);
      if (d < SNAP_PX) yCands.push({ guidePos: target, newOrigin: target - originOffset, delta: d });
    };

    addY(oT,  0,      0);
    addY(oB,  pageH,  oH);
    addY(oCY, pageH / 2, oH / 2);

    for (const other of others) {
      const tT  = other.top  ?? 0;
      const tH  = other.getScaledHeight();
      const tCY = tT + tH / 2;
      const tB  = tT + tH;

      addY(oT,  tT,  0);
      addY(oT,  tB,  0);
      addY(oB,  tT,  oH);
      addY(oB,  tB,  oH);
      addY(oCY, tCY, oH / 2);
    }

    const bestY = this.best(yCands);
    if (bestY) {
      obj.set({ top: bestY.newOrigin });
      this.drawLine(0, bestY.guidePos, pageW, bestY.guidePos, 'h');
    }
  }

  /** Limpiar guías después de soltar el objeto. */
  clear(): void {
    this.guides.forEach(l => this.canvas.remove(l));
    this.guides = [];
    this.canvas.renderAll();
  }

  private best(cands: SnapHit[]): SnapHit | null {
    return cands.reduce<SnapHit | null>(
      (acc, c) => (!acc || c.delta < acc.delta) ? c : acc,
      null,
    );
  }

  private drawLine(x1: number, y1: number, x2: number, y2: number, _axis: 'h' | 'v'): void {
    const line = new fabric.Line([x1, y1, x2, y2], {
      stroke:          GUIDE_CLR,
      strokeWidth:     1,
      strokeDashArray: [5, 5],
      selectable:      false,
      evented:         false,
      opacity:         0.85,
    });
    (line as any).__guide = true;
    this.canvas.add(line);
    this.canvas.bringObjectToFront(line);
    this.guides.push(line);
  }
}
