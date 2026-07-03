import { ChangeDetectorRef, Component, OnInit, OnDestroy } from '@angular/core';
import { Router, NavigationEnd } from '@angular/router';
import { Subject } from 'rxjs';
import { filter, takeUntil } from 'rxjs/operators';
import { AuthService } from '../core/services/auth.service';
import { ThemeService } from '../core/services/theme.service';

@Component({
  selector: 'app-layout',
  standalone: false,
  templateUrl: './layout.component.html',
  styleUrl: './layout.component.scss',
})
export class LayoutComponent implements OnInit, OnDestroy {
  sidebarOpen = false;
  sidebarMini = localStorage.getItem('kocert_sidebar_mini') === 'true';
  isDark       = false;

  navItems = [
    { label: 'Dashboard',    icon: 'bi-speedometer2',       route: '/dashboard' },
    { label: 'Soldadores',   icon: 'bi-person-badge',       route: '/soldadores' },
    { label: 'Certificados', icon: 'bi-file-earmark-check', route: '/certificados' },
    { label: 'Empresas',     icon: 'bi-building',           route: '/empresas' },
    { label: 'Inspectores',  icon: 'bi-person-lines-fill',  route: '/inspectores' },
    { label: 'Referencia',   icon: 'bi-journal-bookmark',   route: '/referencia' },
    { label: 'Plantillas',   icon: 'bi-layout-text-window', route: '/plantillas' },
    { label: 'Usuarios',     icon: 'bi-people',             route: '/usuarios' },
  ];

  private destroy$ = new Subject<void>();

  constructor(
    public  auth:  AuthService,
    public  theme: ThemeService,
    private router: Router,
    private cdr:    ChangeDetectorRef,
  ) {}

  ngOnInit(): void {
    this.theme.isDark$.pipe(takeUntil(this.destroy$)).subscribe(d => {
      this.isDark = d;
      this.cdr.markForCheck();
    });

    this.router.events.pipe(
      filter(e => e instanceof NavigationEnd),
      takeUntil(this.destroy$),
    ).subscribe(() => {
      this.sidebarOpen = false;
      window.scrollTo({ top: 0 });
    });
  }

  ngOnDestroy(): void { this.destroy$.next(); this.destroy$.complete(); }

  toggleMini(): void {
    this.sidebarMini = !this.sidebarMini;
    localStorage.setItem('kocert_sidebar_mini', String(this.sidebarMini));
  }

  toggleSidebar(): void { this.sidebarOpen = !this.sidebarOpen; }
  closeSidebar(): void  { this.sidebarOpen = false; }
  logout(): void        { this.auth.logout(); }

  get user() { return this.auth.currentUser(); }
}
