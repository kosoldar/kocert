import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { UsuarioService } from '../services/usuario.service';
import { ToastService } from '../../core/services/toast.service';
import { ReviewField } from '../../shared/components/data-review-modal/data-review-modal.component';

@Component({
  selector: 'app-usuario-form',
  standalone: false,
  templateUrl: './usuario-form.component.html',
})
export class UsuarioFormComponent implements OnInit {
  form!:      FormGroup;
  loading   = false;
  saving    = false;
  showReview = false;
  editId:   number | null = null;

  get isEdit(): boolean { return !!this.editId; }

  get reviewFields(): ReviewField[] {
    const v = this.form.value;
    return [
      { label: 'Apellido', value: v.apellido },
      { label: 'Nombre',   value: v.nombre   },
      { label: 'Email',    value: v.email    },
      { label: 'Rol',      value: v.rol      },
      { label: 'Password', value: v.password ? '••••••••' : '(sin cambios)' },
      { label: 'Estado',   value: v.activo ? 'Activo' : 'Inactivo' },
    ];
  }

  constructor(
    private fb:      FormBuilder,
    private service: UsuarioService,
    private route:   ActivatedRoute,
    private router:  Router,
    private cdr:     ChangeDetectorRef,
    private toast:   ToastService,
  ) {}

  ngOnInit(): void {
    this.form = this.fb.group({
      apellido: ['', [Validators.required, Validators.maxLength(100)]],
      nombre:   ['', [Validators.required, Validators.maxLength(100)]],
      email:    ['', [Validators.required, Validators.email, Validators.maxLength(200)]],
      password: [''],
      rol:      ['tecnico', Validators.required],
      activo:   [true],
    });

    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.editId  = +id;
      this.loading = true;
      this.service.getOne(this.editId).subscribe({
        next:  u  => { this.form.patchValue(u); this.loading = false; this.cdr.markForCheck(); },
        error: () => { this.loading = false; this.cdr.markForCheck(); this.back(); },
      });
    } else {
      this.f('password').setValidators([Validators.required, Validators.minLength(8)]);
      this.f('password').updateValueAndValidity();
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

    const payload = { ...this.form.value };
    if (this.isEdit && !payload.password) delete payload.password;

    const obs$ = this.isEdit
      ? this.service.update(this.editId!, payload)
      : this.service.create(payload);

    obs$.subscribe({
      next: () => {
        this.toast.success(this.isEdit ? 'Usuario actualizado.' : 'Usuario creado.');
        this.router.navigate(['/usuarios']);
      },
      error: (err) => {
        this.saving = false;
        this.cdr.markForCheck();
        const errors = err.error?.errors ?? {};
        if (errors['email']) {
          this.f('email').setErrors({ taken: true });
        } else {
          this.toast.error('Error al guardar. Revisá los datos.');
        }
      },
    });
  }

  back(): void { this.router.navigate(['/usuarios']); }
}
