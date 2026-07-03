import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { InspectorService } from '../services/inspector.service';
import { ToastService } from '../../core/services/toast.service';

@Component({
  selector: 'app-inspector-form',
  standalone: false,
  templateUrl: './inspector-form.component.html',
})
export class InspectorFormComponent implements OnInit {
  form!:      FormGroup;
  loading   = false;
  saving    = false;
  editId:   number | null = null;

  firmaFile:       File | null = null;
  firmaPreview:    string | null = null;
  firmaExistente:  string | null = null;
  uploadingFirma   = false;

  get isEdit(): boolean { return !!this.editId; }

  constructor(
    private fb:      FormBuilder,
    private service: InspectorService,
    private route:   ActivatedRoute,
    private router:  Router,
    private cdr:     ChangeDetectorRef,
    private toast:   ToastService,
  ) {}

  ngOnInit(): void {
    this.form = this.fb.group({
      nombre:        ['', [Validators.required, Validators.maxLength(100)]],
      certificacion: ['', [Validators.required, Validators.maxLength(200)]],
      email:         ['', [Validators.email, Validators.maxLength(100)]],
      telefono:      ['', Validators.maxLength(30)],
      activo:        [true],
    });

    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.editId  = +id;
      this.loading = true;
      this.service.getOne(this.editId).subscribe({
        next: i => {
          this.form.patchValue(i);
          this.firmaExistente = i.firma_path ?? null;
          this.loading = false;
          this.cdr.markForCheck();
        },
        error: () => { this.loading = false; this.back(); },
      });
    }
  }

  f(name: string) { return this.form.get(name)!; }

  onFirmaSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0];
    if (!file) return;
    this.firmaFile = file;
    const reader = new FileReader();
    reader.onload = e => {
      this.firmaPreview = e.target?.result as string;
      this.cdr.markForCheck();
    };
    reader.readAsDataURL(file);
  }

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;

    const obs$ = this.isEdit
      ? this.service.update(this.editId!, this.form.value)
      : this.service.create(this.form.value);

    obs$.subscribe({
      next: saved => {
        if (this.firmaFile) {
          this.uploadingFirma = true;
          this.service.uploadFirma(saved.id, this.firmaFile).subscribe({
            next: () => {
              this.uploadingFirma = false;
              this.toast.success(this.isEdit ? 'Inspector actualizado.' : 'Inspector creado.');
              this.router.navigate(['/inspectores']);
            },
            error: () => {
              this.uploadingFirma = false;
              this.saving = false;
              this.cdr.markForCheck();
              this.toast.error('Datos guardados, pero falló la subida de la firma.');
              this.router.navigate(['/inspectores']);
            },
          });
        } else {
          this.toast.success(this.isEdit ? 'Inspector actualizado.' : 'Inspector creado.');
          this.router.navigate(['/inspectores']);
        }
      },
      error: (err) => {
        this.saving = false;
        this.cdr.markForCheck();
        if (err.status === 422 && err.error?.errors?.email) {
          this.f('email').setErrors({ taken: true });
        } else {
          this.toast.error('Error al guardar.');
        }
      },
    });
  }

  back(): void { this.router.navigate(['/inspectores']); }
}
