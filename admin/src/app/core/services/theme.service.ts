import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ThemeService {
  private static readonly KEY = 'kocert_theme';

  readonly isDark$ = new BehaviorSubject<boolean>(this.loadPreference());

  constructor() {
    this.isDark$.subscribe(dark => {
      document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
      localStorage.setItem(ThemeService.KEY, dark ? 'dark' : 'light');
    });
  }

  toggle(): void { this.isDark$.next(!this.isDark$.value); }

  private loadPreference(): boolean {
    const stored = localStorage.getItem(ThemeService.KEY);
    if (stored) return stored === 'dark';
    return window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false;
  }
}
