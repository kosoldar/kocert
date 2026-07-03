import { Component, EventEmitter, Input, OnChanges, Output } from '@angular/core';
import { LayoutBlock, BlockStyle, PASSES_COLUMNS, PassesColumnKey } from '../../models/cert-layout.model';
import { FIELD_KEYS, FIELD_KEY_GROUPS, fieldLabel } from '../utils/field-keys';

@Component({
  selector: 'app-properties-panel',
  standalone: false,
  templateUrl: './properties-panel.component.html',
})
export class PropertiesPanelComponent implements OnChanges {
  @Input()  block:          LayoutBlock | null = null;
  @Input()  activePageIndex = 0;
  @Output() changed = new EventEmitter<LayoutBlock>();
  @Output() deleted = new EventEmitter<void>();

  // local editable copy
  draft: LayoutBlock | null = null;

  fieldKeys    = FIELD_KEYS;
  fieldGroups  = FIELD_KEY_GROUPS;
  fieldLabel   = fieldLabel;

  imageTypes = [
    { value: 'logo',       label: 'Logo Kosoldar' },
    { value: 'norma_logo', label: 'Logo de norma' },
    { value: 'photo',      label: 'Foto soldador' },
    { value: 'signature',  label: 'Firma inspector' },
    { value: 'qr',         label: 'Código QR' },
  ];

  passesColumns = PASSES_COLUMNS;

  ngOnChanges(): void {
    this.draft = this.block ? structuredClone(this.block) : null;
  }

  emit(): void {
    if (this.draft) this.changed.emit(structuredClone(this.draft));
  }

  get s(): BlockStyle { return this.draft?.style ?? {}; }
  get cfg(): Record<string, any> { return (this.draft as any)?.config ?? {}; }

  setStyle(key: keyof BlockStyle, value: unknown): void {
    if (!this.draft) return;
    (this.draft.style as any)[key] = value;
    this.emit();
  }

  setField(key: keyof LayoutBlock, value: unknown): void {
    if (!this.draft) return;
    (this.draft as any)[key] = value;
    this.emit();
  }

  setConfig(key: string, value: unknown): void {
    if (!this.draft) return;
    if (!(this.draft as any).config) (this.draft as any).config = {};
    (this.draft as any).config[key] = value;
    this.emit();
  }

  isPassesColumnActive(key: PassesColumnKey): boolean {
    const cols = this.cfg['columns'] as PassesColumnKey[] | undefined;
    return !cols || cols.includes(key);
  }

  togglePassesColumn(key: PassesColumnKey): void {
    const all  = PASSES_COLUMNS.map(c => c.key) as PassesColumnKey[];
    const cur  = (this.cfg['columns'] as PassesColumnKey[] | undefined) ?? [...all];
    const next = cur.includes(key) ? cur.filter(c => c !== key) : [...cur, key];
    this.setConfig('columns', next.length === all.length ? undefined : next);
  }

  typeLabel(type: LayoutBlock['type']): string {
    const m: Record<string, string> = {
      field: 'Campo', text: 'Texto', image: 'Imagen', line: 'Línea',
      variables_block: 'Variables (dinámico)', results_block: 'Resultados (dinámico)',
      passes_block: 'Pasadas (dinámico)', joint_design_block: 'Junta (dinámico)',
    };
    return m[type] ?? type;
  }

  isDynamic(type: LayoutBlock['type'] | undefined): boolean {
    return ['variables_block', 'results_block', 'passes_block', 'joint_design_block'].includes(type ?? '');
  }
}
