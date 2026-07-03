import { Component } from '@angular/core';
import { AsyncPipe } from '@angular/common';
import { ToastService, Toast } from '../../../core/services/toast.service';

@Component({
  selector: 'app-toast',
  standalone: true,
  imports: [AsyncPipe],
  template: `
    <div class="toast-container position-fixed top-0 end-0 p-3 toast-responsive" style="z-index: 1090;">
      @for (t of (toastService.toasts$ | async) ?? []; track t.id) {
        <div [class]="'toast show align-items-center border-0 mb-2 ' + bgClass(t)" role="alert">
          <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2 fw-semibold">
              <i [class]="'bi ' + iconClass(t)"></i>
              {{ t.message }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    (click)="toastService.dismiss(t.id)"></button>
          </div>
        </div>
      }
    </div>
  `,
})
export class ToastComponent {
  constructor(public toastService: ToastService) {}

  bgClass(t: Toast): string {
    return ({
      success: 'text-bg-success',
      error:   'text-bg-danger',
      warning: 'text-bg-warning text-dark',
      info:    'text-bg-dark',
    } as Record<string, string>)[t.type] ?? 'text-bg-dark';
  }

  iconClass(t: Toast): string {
    return ({
      success: 'bi-check-circle-fill',
      error:   'bi-x-circle-fill',
      warning: 'bi-exclamation-triangle-fill',
      info:    'bi-info-circle-fill',
    } as Record<string, string>)[t.type] ?? 'bi-info-circle-fill';
  }
}
