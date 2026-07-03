import { Component, EventEmitter, Input, Output } from '@angular/core';
import { NgIf, NgFor } from '@angular/common';

export interface ReviewField {
  label: string;
  value: string | number | boolean | null | undefined;
}

type ModalStep = 'review' | 'confirm';

@Component({
  selector: 'app-data-review-modal',
  standalone: true,
  imports: [NgIf, NgFor],
  templateUrl: './data-review-modal.component.html',
  styles: [`
    .review-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.5);
      backdrop-filter: blur(4px);
      z-index: 1060;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      animation: rmFade .15s ease;
    }
    @keyframes rmFade { from { opacity: 0; transform: scale(.97) } to { opacity: 1; transform: scale(1) } }

    .review-dialog {
      background: var(--bs-body-bg);
      border: 1px solid var(--bs-border-color);
      border-radius: 1rem;
      width: 100%;
      max-width: 480px;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
      box-shadow: 0 8px 32px rgba(0,0,0,.2);
    }

    .review-body {
      overflow-y: auto;
      flex: 1 1 auto;
      min-height: 0;
    }

    /* Only the numbered circles */
    .step-num {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: .7rem;
      font-weight: 700;
      flex-shrink: 0;
      background: var(--bs-secondary-bg);
      color: var(--bs-secondary-color);
      transition: background .2s, color .2s;
    }
    .step-num.active  { background: var(--bs-emphasis-color); color: var(--bs-body-bg); }
    .step-num.done    { background: var(--bs-success); color: #fff; }

    .step-connector {
      flex: 1;
      height: 2px;
      background: var(--bs-border-color);
      margin: 0 .5rem;
      transition: background .2s;
    }
    .step-connector.active { background: var(--bs-success); }

    .field-row { border-bottom: 1px solid var(--bs-border-color-translucent); }
    .field-row:last-child { border-bottom: none; }
  `],
})
export class DataReviewModalComponent {
  @Input() title  = 'Revisar datos';
  @Input() fields: ReviewField[] = [];
  @Input() isEdit = false;

  @Output() confirmed = new EventEmitter<void>();
  @Output() cancelled = new EventEmitter<void>();

  step: ModalStep = 'review';

  get actionLabel(): string { return this.isEdit ? 'actualizar' : 'crear'; }

  next():    void { this.step = 'confirm'; }
  back():    void { this.step = 'review'; }
  confirm(): void { this.confirmed.emit(); }
  cancel():  void { this.step = 'review'; this.cancelled.emit(); }

  onOverlayClick(e: MouseEvent): void {
    if ((e.target as HTMLElement).classList.contains('review-overlay')) this.cancel();
  }
}
