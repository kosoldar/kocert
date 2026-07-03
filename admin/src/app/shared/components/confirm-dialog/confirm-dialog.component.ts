import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-confirm-dialog',
  standalone: true,
  template: `
    <div class="confirm-overlay d-flex align-items-center justify-content-center"
         (click)="onOverlayClick($event)">
      <div class="confirm-dialog bg-body rounded-4 shadow-lg p-4 border"
           style="width:100%;max-width:380px">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h5 class="fw-semibold mb-0">{{ title }}</h5>
          <button type="button" class="btn-close" (click)="cancelled.emit()"></button>
        </div>
        <p class="text-muted small mb-4">{{ message }}</p>
        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-secondary btn-sm px-3"
                  (click)="cancelled.emit()">Cancelar</button>
          <button type="button" class="btn btn-sm px-3 fw-semibold"
                  [class]="confirmClass"
                  (click)="confirmed.emit()">{{ confirmLabel }}</button>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .confirm-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(4px);
      z-index: 1060;
      animation: cdFadeIn .15s ease;
    }
    @keyframes cdFadeIn { from { opacity: 0 } to { opacity: 1 } }
  `],
})
export class ConfirmDialogComponent {
  @Input() title        = 'Confirmar';
  @Input() message      = '¿Estás seguro?';
  @Input() confirmLabel = 'Confirmar';
  @Input() confirmClass = 'btn-danger';

  @Output() confirmed = new EventEmitter<void>();
  @Output() cancelled = new EventEmitter<void>();

  onOverlayClick(e: MouseEvent): void {
    if ((e.target as HTMLElement).classList.contains('confirm-overlay'))
      this.cancelled.emit();
  }
}
