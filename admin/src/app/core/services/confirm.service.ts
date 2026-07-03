import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

export interface ConfirmOptions {
  title?:        string;
  message:       string;
  confirmLabel?: string;
  confirmClass?: string;
}

@Injectable({ providedIn: 'root' })
export class ConfirmService {
  private _opts$ = new BehaviorSubject<ConfirmOptions | null>(null);
  readonly opts$ = this._opts$.asObservable();

  private resolveFn?: (result: boolean) => void;

  open(opts: ConfirmOptions): Promise<boolean> {
    return new Promise(resolve => {
      this.resolveFn = resolve;
      this._opts$.next(opts);
    });
  }

  confirm(): void { this.resolveFn?.(true);  this._opts$.next(null); }
  cancel():  void { this.resolveFn?.(false); this._opts$.next(null); }
}
