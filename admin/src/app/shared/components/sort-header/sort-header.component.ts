import { Component, EventEmitter, Input, Output } from '@angular/core';
import { NgClass } from '@angular/common';

export type SortDir = 'asc' | 'desc' | 'none';

export interface SortEvent {
  field: string;
  direction: SortDir;
}

@Component({
  selector: '[appSortHeader]',
  standalone: true,
  imports: [NgClass],
  template: `
    <span class="d-flex align-items-center gap-1 user-select-none sort-trigger" (click)="toggle()">
      {{ label }}
      <i class="bi small" [ngClass]="iconClass"></i>
    </span>
  `,
  styles: [`
    :host { cursor: pointer; white-space: nowrap; }
    .sort-trigger:hover i { opacity: 1 !important; }
  `],
})
export class SortHeaderComponent {
  @Input('appSortHeader') field!: string;
  @Input() label!: string;
  @Input() activeField: string | null = null;
  @Input() direction: SortDir = 'none';

  @Output() sortChange = new EventEmitter<SortEvent>();

  get isActive(): boolean { return this.activeField === this.field; }

  get iconClass(): string {
    if (!this.isActive || this.direction === 'none') return 'bi-arrow-down-up opacity-25';
    return this.direction === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
  }

  toggle(): void {
    let next: SortDir = 'asc';
    if (this.isActive) {
      next = this.direction === 'asc' ? 'desc' : this.direction === 'desc' ? 'none' : 'asc';
    }
    this.sortChange.emit({ field: this.field, direction: next });
  }
}
