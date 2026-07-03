import {
  Component, Input, Output, EventEmitter, forwardRef,
  HostListener, ElementRef, OnChanges, SimpleChanges
} from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { NgIf, NgFor } from '@angular/common';
import { FormsModule } from '@angular/forms';

export interface SelectOption {
  id: number;
  name: string;
}

@Component({
  selector: 'app-searchable-select',
  standalone: true,
  imports: [NgIf, NgFor, FormsModule],
  templateUrl: './searchable-select.component.html',
  providers: [{
    provide: NG_VALUE_ACCESSOR,
    useExisting: forwardRef(() => SearchableSelectComponent),
    multi: true
  }]
})
export class SearchableSelectComponent implements ControlValueAccessor, OnChanges {
  @Input() items: SelectOption[]   = [];
  @Input() placeholder             = 'Buscar...';
  @Input() allowCreate             = false;
  @Input() creating                = false;
  @Output() createRequest          = new EventEmitter<string>();

  searchTerm  = '';
  open        = false;
  selectedId: number | null = null;
  selectedName= '';

  private onChange: (v: number | null) => void = () => {};
  private onTouched: () => void = () => {};

  constructor(private el: ElementRef) {}

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['items'] && this.selectedId) {
      const found = this.items.find(i => i.id === this.selectedId);
      if (found) { this.selectedName = found.name; this.searchTerm = found.name; }
    }
  }

  writeValue(id: number | null): void {
    this.selectedId = id;
    if (!id) { this.selectedName = ''; this.searchTerm = ''; return; }
    const found = this.items.find(i => i.id === id);
    if (found) { this.selectedName = found.name; this.searchTerm = found.name; }
  }

  registerOnChange(fn: (v: number | null) => void): void { this.onChange = fn; }
  registerOnTouched(fn: () => void): void { this.onTouched = fn; }

  get filtered(): SelectOption[] {
    const term = this.searchTerm.toLowerCase().trim();
    if (!term) return this.items;
    return this.items.filter(i => i.name.toLowerCase().includes(term));
  }

  get showCreate(): boolean {
    if (!this.allowCreate || !this.searchTerm.trim()) return false;
    return !this.items.some(i => i.name.toLowerCase() === this.searchTerm.toLowerCase().trim());
  }

  onInputFocus(): void {
    this.searchTerm = '';
    this.open = true;
    this.onTouched();
  }

  onInputInput(): void {
    this.open = true;
    if (!this.searchTerm.trim()) {
      this.selectedId   = null;
      this.selectedName = '';
      this.onChange(null);
    }
  }

  select(item: SelectOption): void {
    this.selectedId   = item.id;
    this.selectedName = item.name;
    this.searchTerm   = item.name;
    this.open         = false;
    this.onChange(item.id);
  }

  requestCreate(): void {
    const term = this.searchTerm.trim();
    if (!term) return;
    this.createRequest.emit(term);
    this.open = false;
  }

  selectNewItem(item: SelectOption): void {
    this.select(item);
  }

  onBlur(): void {
    setTimeout(() => {
      if (!this.open) this.searchTerm = this.selectedName;
    }, 200);
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(e: MouseEvent): void {
    if (!this.el.nativeElement.contains(e.target)) {
      this.open = false;
      this.searchTerm = this.selectedName;
    }
  }
}
