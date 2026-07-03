import { ChangeDetectorRef, Directive, OnDestroy, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, Subject } from 'rxjs';
import { debounceTime, distinctUntilChanged, takeUntil } from 'rxjs/operators';
import { PageMeta } from '../components/pagination/pagination.component';
import { SortDir, SortEvent } from '../components/sort-header/sort-header.component';
import { ToastService } from '../../core/services/toast.service';

export interface PagedQuery {
  search?:   string;
  page?:     number;
  per_page?: number;
  sort_by?:  string;
  sort_dir?: string;
}

export interface PagedResponse<T> {
  data: T[];
  meta: PageMeta;
}

@Directive()
export abstract class BasePagedListComponent<T extends { id: number }>
  implements OnInit, OnDestroy {

  items: T[]            = [];
  meta: PageMeta | null = null;
  loading     = false;
  searchTerm  = '';
  currentPage = 1;
  perPage     = 25;
  perPageOpts = [10, 25, 50, 100];
  sortField: string | null = null;
  sortDir: SortDir = 'none';
  deleteTarget: T | null = null;

  protected search$  = new Subject<string>();
  protected destroy$ = new Subject<void>();

  constructor(
    protected router: Router,
    protected cdr:    ChangeDetectorRef,
    protected toast:  ToastService,
  ) {}

  ngOnInit(): void {
    this.search$.pipe(
      debounceTime(300), distinctUntilChanged(), takeUntil(this.destroy$),
    ).subscribe(() => { this.currentPage = 1; this.load(); });
    this.load();
  }

  ngOnDestroy(): void { this.destroy$.next(); this.destroy$.complete(); }

  load(): void {
    this.loading = true;
    this.fetchData({
      search:   this.searchTerm,
      page:     this.currentPage,
      per_page: this.perPage,
      sort_by:  this.sortField ?? undefined,
      sort_dir: this.sortDir !== 'none' ? this.sortDir : undefined,
    }).pipe(takeUntil(this.destroy$)).subscribe({
      next: res => {
        this.items   = res.data;
        this.meta    = res.meta;
        this.loading = false;
        this.cdr.markForCheck();
      },
      error: () => { this.loading = false; this.cdr.markForCheck(); },
    });
  }

  onSearch(v: string): void { this.searchTerm = v; this.search$.next(v); }
  onPerPage(): void          { this.currentPage = 1; this.load(); }
  onPage(p: number): void    { this.currentPage = p; this.load(); }

  onSort(e: SortEvent): void {
    this.sortField   = e.direction === 'none' ? null : e.field;
    this.sortDir     = e.direction;
    this.currentPage = 1;
    this.load();
  }

  confirmDelete(item: T): void { this.deleteTarget = item; this.cdr.detectChanges(); }
  cancelDelete():          void { this.deleteTarget = null; this.cdr.detectChanges(); }

  doDelete(): void {
    if (!this.deleteTarget) return;
    const target = this.deleteTarget;
    this.deleteItem(target.id).subscribe({
      next: () => {
        this.deleteTarget = null;
        this.toast.success(this.getDeleteMessage(target));
        this.load();
      },
      error: (err: { status?: number; error?: { message?: string } }) => {
        this.deleteTarget = null;
        this.cdr.detectChanges();
        this.toast.error(
          err?.status === 409 ? (err.error?.message ?? 'Error al eliminar.') : 'Error al eliminar.',
        );
      },
    });
  }

  goNew():          void { this.router.navigate([`/${this.basePath}/new`]); }
  goEdit(item: T):  void { this.router.navigate([`/${this.basePath}`, item.id, 'edit']); }

  protected getDeleteMessage(item: T): string {
    return `${this.getDisplayName(item)} eliminado.`;
  }

  protected abstract get basePath(): string;
  protected abstract getDisplayName(item: T): string;
  protected abstract fetchData(params: PagedQuery): Observable<PagedResponse<T>>;
  protected abstract deleteItem(id: number): Observable<void>;
}
