import { Component } from '@angular/core';
import { LayoutBlock } from '../../models/cert-layout.model';
import { FIELD_KEYS } from '../../designer/utils/field-keys';

const uuid = () => crypto.randomUUID();

interface PaletteItem {
  label:    string;
  icon:     string;
  color:    string;
  block:    () => Partial<LayoutBlock>;
}

@Component({
  selector: 'app-cabecera-palette',
  standalone: false,
  template: `
    <div class="p-2">
      @for (section of sections; track section.title) {
        <div class="text-uppercase text-muted small fw-semibold px-1 mt-3 mb-1"
             style="font-size:.65rem;">{{ section.title }}</div>
        @for (item of section.items; track item.label) {
          <div class="palette-item d-flex align-items-center gap-2 px-2 py-2 mb-1 rounded"
               [style.border-left]="'3px solid ' + item.color"
               draggable="true"
               (dragstart)="onDrag($event, item)"
               title="{{ item.label }}">
            <i class="bi {{ item.icon }} flex-shrink-0" style="font-size:.85rem;"></i>
            <span class="small">{{ item.label }}</span>
          </div>
        }
      }
    </div>
  `,
  styles: [`
    .palette-item {
      cursor: grab; background: var(--bs-body-bg);
      border: 1px solid var(--bs-border-color);
      transition: background .15s; user-select: none;
    }
    .palette-item:hover { background: var(--bs-secondary-bg); }
    .palette-item:active { cursor: grabbing; }
  `],
})
export class CabeceraBlockPaletteComponent {

  sections = [
    {
      title: 'Texto y campos',
      items: [
        {
          label: 'Campo dinámico', icon: 'bi-input-cursor-text', color: '#4a90d9',
          block: (): Partial<LayoutBlock> => ({
            type: 'field', fieldKey: FIELD_KEYS[0].key,
            width: 60, height: 8,
            style: { fontSize: 10, fontWeight: 'normal', color: '#000000', textAlign: 'left' },
          }),
        },
        {
          label: 'Texto fijo', icon: 'bi-fonts', color: '#9b59b6',
          block: (): Partial<LayoutBlock> => ({
            type: 'text', content: 'Texto aquí',
            width: 80, height: 8,
            style: { fontSize: 9, fontWeight: 'normal', color: '#000000', textAlign: 'left' },
          }),
        },
      ],
    },
    {
      title: 'Imágenes',
      items: [
        { label: 'Logo Kosoldar',  icon: 'bi-image', color: '#27ae60', block: () => ({ type: 'image' as const, imageType: 'logo'       as const, width: 35, height: 14, style: {} }) },
        { label: 'Logo norma',     icon: 'bi-image', color: '#27ae60', block: () => ({ type: 'image' as const, imageType: 'norma_logo' as const, width: 25, height: 10, style: {} }) },
        { label: 'QR (pequeño)',   icon: 'bi-qr-code', color: '#2c3e50', block: () => ({ type: 'image' as const, imageType: 'qr'        as const, width: 18, height: 18, style: {} }) },
        { label: 'QR (grande)',    icon: 'bi-qr-code', color: '#2c3e50', block: () => ({ type: 'image' as const, imageType: 'qr'        as const, width: 40, height: 40, style: {} }) },
        { label: 'Foto soldador',  icon: 'bi-person-square', color: '#e67e22', block: () => ({ type: 'image' as const, imageType: 'photo'     as const, width: 30, height: 38, style: {} }) },
        { label: 'Firma inspector',icon: 'bi-pen', color: '#16a085', block: () => ({ type: 'image' as const, imageType: 'signature' as const, width: 35, height: 14, style: {} }) },
      ],
    },
    {
      title: 'Líneas',
      items: [
        {
          label: 'Línea horizontal', icon: 'bi-dash-lg', color: '#7f8c8d',
          block: (): Partial<LayoutBlock> => ({
            type: 'line', direction: 'horizontal', width: 200, height: 1,
            style: { color: '#000000' },
          }),
        },
        {
          label: 'Línea vertical', icon: 'bi-layout-sidebar', color: '#7f8c8d',
          block: (): Partial<LayoutBlock> => ({
            type: 'line', direction: 'vertical', width: 1, height: 20,
            style: { color: '#000000' },
          }),
        },
      ],
    },
    {
      title: 'Formas',
      items: [
        {
          label: 'Rectángulo relleno', icon: 'bi-square-fill', color: '#34495e',
          block: (): Partial<LayoutBlock> => ({
            type: 'rect', width: 40, height: 10,
            style: { backgroundColor: '#1a1a1a', borderWidth: 0 },
          }),
        },
        {
          label: 'Caja (borde)', icon: 'bi-square', color: '#34495e',
          block: (): Partial<LayoutBlock> => ({
            type: 'rect', width: 40, height: 10,
            style: { backgroundColor: 'transparent', borderWidth: 1, borderColor: '#000000' },
          }),
        },
      ],
    },
  ];

  onDrag(evt: DragEvent, item: PaletteItem): void {
    const partial = item.block();
    const block: LayoutBlock = {
      id:     uuid(),
      type:   partial.type as LayoutBlock['type'],
      x:      5,
      y:      5,
      width:  partial.width  ?? 40,
      height: partial.height ?? 8,
      style:  partial.style  ?? {},
      ...(partial.fieldKey   ? { fieldKey:   partial.fieldKey   } : {}),
      ...(partial.imageType  ? { imageType:  partial.imageType  } : {}),
      ...(partial.content    ? { content:    partial.content    } : {}),
      ...(partial.direction  ? { direction:  partial.direction  } : {}),
    };
    evt.dataTransfer?.setData('application/json', JSON.stringify(block));
  }
}
