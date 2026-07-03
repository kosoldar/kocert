import { Component, Input, Output, EventEmitter } from '@angular/core';

export interface PageMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from?: number | null;
  to?: number | null;
}

@Component({
  selector: 'app-pagination',
  standalone: true,
  template: `
    @if (meta && meta.last_page > 1) {
      <div class="d-flex align-items-center justify-content-between">
        <span class="text-muted small">
          @if (meta.from && meta.to) {
            Mostrando {{ meta.from }}–{{ meta.to }} de {{ meta.total }}
          } @else {
            {{ meta.total }} registros
          }
        </span>
        <nav aria-label="Paginación">
          <ul class="pagination pagination-sm mb-0 gap-1">
            <li class="page-item" [class.disabled]="meta.current_page === 1">
              <button class="page-link rounded"
                      (click)="pageChange.emit(meta.current_page - 1)"
                      [disabled]="meta.current_page === 1">
                <i class="bi bi-chevron-left"></i>
              </button>
            </li>
            @for (p of pages; track p) {
              <li class="page-item" [class.active]="meta.current_page === p">
                <button class="page-link rounded" (click)="pageChange.emit(p)">{{ p }}</button>
              </li>
            }
            <li class="page-item" [class.disabled]="meta.current_page === meta.last_page">
              <button class="page-link rounded"
                      (click)="pageChange.emit(meta.current_page + 1)"
                      [disabled]="meta.current_page === meta.last_page">
                <i class="bi bi-chevron-right"></i>
              </button>
            </li>
          </ul>
        </nav>
      </div>
    }
  `,
})
export class PaginationComponent {
  @Input() set meta(value: PageMeta | null) {
    this._meta = value;
    if (value) {
      const total = value.last_page;
      const cur   = value.current_page;
      const delta = 2;
      const range = Array.from({ length: total }, (_, i) => i + 1)
        .filter(p => p === 1 || p === total || Math.abs(p - cur) <= delta);
      this.pages = range;
    } else {
      this.pages = [];
    }
  }
  get meta(): PageMeta | null { return this._meta; }

  @Output() pageChange = new EventEmitter<number>();

  _meta: PageMeta | null = null;
  pages: number[] = [];
}
