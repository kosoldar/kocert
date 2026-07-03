import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { EmpresaService } from '../services/empresa.service';
import { ToastService } from '../../core/services/toast.service';
import { ReviewField } from '../../shared/components/data-review-modal/data-review-modal.component';

@Component({
  selector: 'app-empresa-form',
  standalone: false,
  templateUrl: './empresa-form.component.html',
})
export class EmpresaFormComponent implements OnInit {
  form!:      FormGroup;
  loading   = false;
  saving    = false;
  showReview = false;
  editId:   number | null = null;

  get isEdit(): boolean { return !!this.editId; }

  get reviewFields(): ReviewField[] {
    const v = this.form.value;
    return [
      { label: 'Nombre',   value: v.nombre   },
      { label: 'CUIT',     value: v.cuit     },
      { label: 'Contacto', value: v.contacto },
      { label: 'Estado',   value: v.activo ? 'Activa' : 'Inactiva' },
    ];
  }

  constructor(
    private fb:      FormBuilder,
    private service: EmpresaService,
    private route:   ActivatedRoute,
    private router:  Router,
    private cdr:     ChangeDetectorRef,
    private toast:   ToastService,
  ) {}

  ngOnInit(): void {
    this.form = this.fb.group({
      nombre:   ['', [Validators.required, Validators.maxLength(200)]],
      cuit:     ['', Validators.maxLength(20)],
      contacto: ['', Validators.maxLength(100)],
      activo:   [true],
    });

    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.editId  = +id;
      this.loading = true;
      this.service.getOne(this.editId).subscribe({
        next:  e  => { this.form.patchValue(e); this.loading = false; this.cdr.markForCheck(); },
        error: () => { this.loading = false; this.cdr.markForCheck(); this.back(); },
      });
    }
  }

  f(name: string) { return this.form.get(name)!; }

  openReview(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.showReview = true;
  }

  onConfirmed(): void {
    this.showReview = false;
    this.saving = true;

    const obs$ = this.isEdit
      ? this.service.update(this.editId!, this.form.value)
      : this.service.create(this.form.value);

    obs$.subscribe({
      next: () => {
        this.toast.success(this.isEdit ? 'Empresa actualizada.' : 'Empresa creada.');
        this.router.navigate(['/empresas']);
      },
      error: (err) => {
        this.saving = false;
        this.cdr.markForCheck();
        const errors = err.error?.errors ?? {};
        if (errors['nombre']) {
          this.f('nombre').setErrors({ taken: true });
        } else {
          this.toast.error('Error al guardar. Revisá los datos.');
        }
      },
    });
  }

  back(): void { this.router.navigate(['/empresas']); }
}
