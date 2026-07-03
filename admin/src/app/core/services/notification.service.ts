import { Injectable, OnDestroy } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, interval, Subject, Subscription } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { ToastService } from './toast.service';

export type NotificationColor =
  | 'primary' | 'secondary' | 'success' | 'danger'
  | 'warning' | 'info' | 'light' | 'dark';

export interface AdminNotification {
  id: string;
  title: string;
  message: string;
  link: string | null;
  icon: string;
  color: NotificationColor;
  read: boolean;
  created_at: string;
  created_at_human: string;
}

interface RawAdminNotification {
  id: string;
  data?: Record<string, unknown>;
  read: boolean;
  created_at: string;
  created_at_human?: string;
}

@Injectable({ providedIn: 'root' })
export class NotificationService implements OnDestroy {
  private base = `${environment.apiUrl}/notifications`;
  private destroy$ = new Subject<void>();
  private pollSub: Subscription | null = null;
  private seenIds = new Set<string>();
  private bootstrapped = false;

  unreadCount$    = new BehaviorSubject<number>(0);
  notifications$  = new BehaviorSubject<AdminNotification[]>([]);
  open$           = new BehaviorSubject<boolean>(false);

  constructor(private http: HttpClient, private toast: ToastService) {}

  startPolling(): void {
    if (this.pollSub) return;
    this.refresh();
    this.pollSub = interval(30_000)
      .pipe(takeUntil(this.destroy$))
      .subscribe(() => this.refresh());
  }

  stopPolling(): void {
    this.pollSub?.unsubscribe();
    this.pollSub = null;
    this.seenIds.clear();
    this.bootstrapped = false;
    this.unreadCount$.next(0);
    this.notifications$.next([]);
    this.open$.next(false);
  }

  toggleOpen(): void {
    const next = !this.open$.value;
    this.open$.next(next);
    if (next) this.fetchList();
  }

  close(): void { this.open$.next(false); }

  markRead(id: string): void {
    this.http.post(`${this.base}/${id}/read`, {}).subscribe({
      next: () => {
        const updated = this.notifications$.value.map(n => n.id === id ? { ...n, read: true } : n);
        this.notifications$.next(updated);
        this.syncCount(updated);
      },
      error: () => {},
    });
  }

  markAllRead(): void {
    this.http.post(`${this.base}/read-all`, {}).subscribe({
      next: () => {
        const updated = this.notifications$.value.map(n => ({ ...n, read: true }));
        this.notifications$.next(updated);
        this.syncCount(updated);
      },
      error: () => {},
    });
  }

  ngOnDestroy(): void { this.destroy$.next(); this.destroy$.complete(); }

  private refresh(): void { this.fetchCount(); this.fetchList(); }

  private fetchCount(): void {
    this.http.get<{ count: number }>(`${this.base}/unread-count`).subscribe({
      next: res => this.unreadCount$.next(res.count),
      error: () => {},
    });
  }

  private fetchList(): void {
    this.http.get<{ data: RawAdminNotification[] }>(this.base).subscribe({
      next: res => {
        const list = res.data.map(n => this.normalize(n));
        this.emitNew(list);
        this.notifications$.next(list);
        this.syncCount(list);
      },
      error: () => {},
    });
  }

  private syncCount(list: AdminNotification[]): void {
    this.unreadCount$.next(list.filter(n => !n.read).length);
  }

  private emitNew(list: AdminNotification[]): void {
    const unread = list.filter(n => !n.read);
    if (!this.bootstrapped) {
      unread.forEach(n => this.seenIds.add(n.id));
      this.bootstrapped = true;
      return;
    }
    unread
      .filter(n => !this.seenIds.has(n.id))
      .slice().reverse()
      .forEach(n => this.toast.info(`${n.title}: ${n.message}`));
    unread.forEach(n => this.seenIds.add(n.id));
  }

  private normalize(n: RawAdminNotification): AdminNotification {
    const d = n.data ?? {};
    return {
      id:               n.id,
      title:            this.str(d['title'], 'Notificación'),
      message:          this.str(d['message'], ''),
      link:             this.str(d['link'], '') || null,
      icon:             this.str(d['icon'], 'bi-bell'),
      color:            this.color(d['color']),
      read:             n.read,
      created_at:       n.created_at,
      created_at_human: n.created_at_human ?? '',
    };
  }

  private str(v: unknown, fallback: string): string {
    return typeof v === 'string' && v.trim() ? v : fallback;
  }

  private color(v: unknown): NotificationColor {
    const valid: NotificationColor[] = ['primary','secondary','success','danger','warning','info','light','dark'];
    return typeof v === 'string' && valid.includes(v as NotificationColor) ? v as NotificationColor : 'primary';
  }
}
