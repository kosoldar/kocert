import { Component, EventEmitter, Input, Output } from '@angular/core';
import { LayoutBlock } from '../../models/cert-layout.model';
import { FIELD_KEYS } from '../utils/field-keys';

const uuid = () => crypto.randomUUID();

/** Sub-bloques pre-armados para la sección de datos del soldador/empresa.
 *  Coords relativas al contenedor (0,0 = esquina sup-izq del bloque). */
const SOLDADOR_PRESET_BLOCKS: LayoutBlock[] = [
  { id: uuid(), type: 'text',  content: 'Proceso / Process:',  x: 0,   y: 0,   width: 88,  height: 9,  style: { fontSize: 7 } },
  { id: uuid(), type: 'field', fieldKey: 'cert.proceso',        x: 90,  y: 0,   width: 55,  height: 9,  style: { fontSize: 14, fontWeight: 'bold', textAlign: 'center' } },
  { id: uuid(), type: 'text',  content: 'MANUAL',               x: 147, y: 0,   width: 53,  height: 9,  style: { fontSize: 14, fontWeight: 'bold', textAlign: 'center' } },
  { id: uuid(), type: 'line',  direction: 'horizontal',         x: 0,   y: 9,   width: 200, height: 1,  style: { color: '#000000' } },
  { id: uuid(), type: 'text',  content: "Welder / Nombre Soldador:", x: 0, y: 10, width: 108, height: 8, style: { fontSize: 7 } },
  { id: uuid(), type: 'field', fieldKey: 'soldador.apellido',   x: 0,   y: 18,  width: 108, height: 10, style: { fontSize: 20, fontWeight: 'bold' } },
  { id: uuid(), type: 'field', fieldKey: 'soldador.nombre',     x: 0,   y: 28,  width: 108, height: 8,  style: { fontSize: 16, fontWeight: 'bold' } },
  { id: uuid(), type: 'image', imageType: 'photo',              x: 112, y: 10,  width: 44,  height: 55, style: {} },
  { id: uuid(), type: 'text',  content: 'Company / Empresa:',  x: 0,   y: 37,  width: 35,  height: 7,  style: { fontSize: 7 } },
  { id: uuid(), type: 'field', fieldKey: 'empresa.nombre',      x: 36,  y: 37,  width: 74,  height: 7,  style: { fontSize: 11, fontWeight: 'bold' } },
  { id: uuid(), type: 'text',  content: 'ID / DNI:',           x: 0,   y: 45,  width: 30,  height: 7,  style: { fontSize: 7 } },
  { id: uuid(), type: 'field', fieldKey: 'soldador.dni',        x: 31,  y: 45,  width: 79,  height: 7,  style: { fontSize: 11, fontWeight: 'bold' } },
  { id: uuid(), type: 'text',  content: 'WPS/EPS:',            x: 0,   y: 53,  width: 30,  height: 9,  style: { fontSize: 7 } },
  { id: uuid(), type: 'field', fieldKey: 'cert.eps_numero',     x: 31,  y: 53,  width: 79,  height: 9,  style: { fontSize: 10, fontWeight: 'bold' } },
  { id: uuid(), type: 'text',  content: 'Qualification / Fecha calificación:', x: 0, y: 63, width: 57, height: 8, style: { fontSize: 7 } },
  { id: uuid(), type: 'field', fieldKey: 'cert.fecha_calificacion', x: 58, y: 63, width: 52, height: 8, style: { fontSize: 10, fontWeight: 'bold' } },
  { id: uuid(), type: 'text',  content: 'Expiration / FECHA DE VENCIMIENTO:', x: 0, y: 72, width: 57, height: 9, style: { fontSize: 7, fontWeight: 'bold' } },
  { id: uuid(), type: 'field', fieldKey: 'cert.fecha_vencimiento', x: 58, y: 72, width: 52, height: 11, style: { fontSize: 16, fontWeight: 'bold', color: '#CC0000' } },
  { id: uuid(), type: 'line',  direction: 'horizontal',         x: 0,   y: 84,  width: 200, height: 1,  style: { color: '#000000' } },
];

export interface PaletteBlockDef {
  type:       LayoutBlock['type'];
  label:      string;
  icon:       string;
  color:      string;
  defaults:   Partial<LayoutBlock>;
}

@Component({
  selector: 'app-block-palette',
  standalone: false,
  template: `
    <div class="p-2">
      <div class="text-uppercase text-muted small fw-semibold px-1 mb-2">Bloques</div>

      @for (def of staticBlocks; track def.type + def.label) {
        <div class="palette-item d-flex align-items-center gap-2 px-2 py-2 mb-1 rounded"
             [style.border-left]="'3px solid ' + def.color"
             draggable="true"
             (dragstart)="onDragStart($event, def)"
             title="{{ def.label }}">
          <i class="bi {{ def.icon }} flex-shrink-0"></i>
          <span class="small">{{ def.label }}</span>
        </div>
      }

      @if (mode === 'header') {
        <div class="text-uppercase text-muted small fw-semibold px-1 mt-3 mb-2">Imágenes</div>
        @for (def of headerImageBlocks; track def.label) {
          <div class="palette-item d-flex align-items-center gap-2 px-2 py-2 mb-1 rounded"
               [style.border-left]="'3px solid ' + def.color"
               draggable="true"
               (dragstart)="onDragStart($event, def)"
               title="{{ def.label }}">
            <i class="bi {{ def.icon }} flex-shrink-0"></i>
            <span class="small">{{ def.label }}</span>
          </div>
        }
      }

      @if (mode === 'full') {
        <div class="text-uppercase text-muted small fw-semibold px-1 mt-3 mb-2">Dinámicos</div>
        @for (def of dynamicBlocks; track def.type) {
          <div class="palette-item d-flex align-items-center gap-2 px-2 py-2 mb-1 rounded"
               [style.border-left]="'3px solid ' + def.color"
               draggable="true"
               (dragstart)="onDragStart($event, def)"
               title="{{ def.label }}">
            <i class="bi {{ def.icon }} flex-shrink-0"></i>
            <span class="small">{{ def.label }}</span>
          </div>
        }
      }
    </div>
  `,
  styles: [`
    .palette-item {
      cursor: grab;
      background: var(--bs-body-bg);
      border: 1px solid var(--bs-border-color);
      transition: background .15s;
      user-select: none;
    }
    .palette-item:hover { background: var(--bs-secondary-bg); }
    .palette-item:active { cursor: grabbing; }
  `],
})
export class BlockPaletteComponent {
  @Input()  mode: 'full' | 'header' = 'full';
  @Output() blockDropped = new EventEmitter<LayoutBlock>();

  staticBlocks: PaletteBlockDef[] = [
    {
      type: 'field', label: 'Campo', icon: 'bi-input-cursor-text', color: '#4a90d9',
      defaults: { fieldKey: FIELD_KEYS[0].key, width: 60, height: 8, style: { fontSize: 11, fontWeight: 'normal', color: '#000000', textAlign: 'left' } },
    },
    {
      type: 'text', label: 'Texto fijo', icon: 'bi-fonts', color: '#9b59b6',
      defaults: { content: 'Texto aquí', width: 80, height: 8, style: { fontSize: 10, fontWeight: 'normal', color: '#000000', textAlign: 'left' } },
    },
    {
      type: 'image', label: 'Imagen', icon: 'bi-image', color: '#27ae60',
      defaults: { imageType: 'logo', width: 30, height: 20, style: {} },
    },
    {
      type: 'line', label: 'Línea H', icon: 'bi-dash-lg', color: '#7f8c8d',
      defaults: { direction: 'horizontal', width: 190, height: 2, style: { color: '#000000' } },
    },
    {
      type: 'line', label: 'Línea V', icon: 'bi-layout-sidebar', color: '#7f8c8d',
      defaults: { direction: 'vertical', width: 2, height: 20, style: { color: '#000000' } },
    },
    {
      type: 'rect', label: 'Rectángulo', icon: 'bi-square-fill', color: '#34495e',
      defaults: { width: 40, height: 10, style: { backgroundColor: '#000000', borderWidth: 0 } },
    },
    {
      type: 'rect', label: 'Caja (borde)', icon: 'bi-square', color: '#34495e',
      defaults: { width: 40, height: 10, style: { backgroundColor: 'transparent', borderWidth: 1, borderColor: '#000000' } },
    },
  ];

  /** Bloques de imagen desglosados — para modo header */
  headerImageBlocks: PaletteBlockDef[] = [
    { type: 'image', label: 'Logo Kosoldar',   icon: 'bi-image',         color: '#27ae60', defaults: { imageType: 'logo',       width: 35, height: 14, style: {} } },
    { type: 'image', label: 'Logo norma',       icon: 'bi-image',         color: '#27ae60', defaults: { imageType: 'norma_logo', width: 25, height: 10, style: {} } },
    { type: 'image', label: 'QR pequeño',       icon: 'bi-qr-code',       color: '#2c3e50', defaults: { imageType: 'qr',         width: 18, height: 18, style: {} } },
    { type: 'image', label: 'QR grande',        icon: 'bi-qr-code',       color: '#2c3e50', defaults: { imageType: 'qr',         width: 42, height: 42, style: {} } },
    { type: 'image', label: 'Foto soldador',    icon: 'bi-person-square', color: '#e67e22', defaults: { imageType: 'photo',      width: 28, height: 36, style: {} } },
    { type: 'image', label: 'Firma inspector',  icon: 'bi-pen',           color: '#16a085', defaults: { imageType: 'signature',  width: 35, height: 14, style: {} } },
  ];

  dynamicBlocks: PaletteBlockDef[] = [
    {
      type: 'variables_block', label: 'Variables', icon: 'bi-table', color: '#f39c12',
      defaults: { width: 190, height: 80, style: { fontSize: 8 } },
    },
    {
      type: 'results_block', label: 'Resultados', icon: 'bi-check2-square', color: '#2ecc71',
      defaults: { width: 190, height: 20, style: { fontSize: 8 } },
    },
    {
      type: 'passes_block', label: 'Pasadas', icon: 'bi-list-ol', color: '#3498db',
      defaults: { width: 190, height: 40, style: { fontSize: 7 } },
    },
    {
      type: 'joint_design_block', label: 'Junta', icon: 'bi-bezier2', color: '#8e44ad',
      defaults: { width: 60, height: 50, style: {} },
    },
    {
      type: 'header_block', label: 'Cabecera', icon: 'bi-layout-text-window-reverse', color: '#1565c0',
      defaults: { width: 200, height: 35, style: {}, blocks: [] },
    },
    {
      type: 'soldador_block', label: 'Sección soldador', icon: 'bi-person-vcard', color: '#c0392b',
      defaults: { width: 200, height: 110, style: {}, blocks: SOLDADOR_PRESET_BLOCKS },
    },
  ];

  onDragStart(evt: DragEvent, def: PaletteBlockDef): void {
    const block: LayoutBlock = {
      id:      uuid(),
      type:    def.type,
      x:       10,
      y:       10,
      width:   def.defaults.width  ?? 60,
      height:  def.defaults.height ?? 10,
      style:   def.defaults.style  ?? {},
      ...(def.defaults.fieldKey  ? { fieldKey:  def.defaults.fieldKey  } : {}),
      ...(def.defaults.imageType ? { imageType: def.defaults.imageType } : {}),
      ...(def.defaults.content   ? { content:   def.defaults.content   } : {}),
      ...(def.defaults.direction ? { direction: def.defaults.direction } : {}),
      ...(def.defaults.blocks    ? { blocks:    def.defaults.blocks    } : {}),
    };
    evt.dataTransfer?.setData('application/json', JSON.stringify(block));
  }
}
