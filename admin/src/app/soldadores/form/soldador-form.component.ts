import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { SoldadorService } from '../services/soldador.service';
import { ToastService } from '../../core/services/toast.service';
import { ReviewField } from '../../shared/components/data-review-modal/data-review-modal.component';

@Component({
  selector: 'app-soldador-form',
  standalone: false,
  templateUrl: './soldador-form.component.html',
})
export class SoldadorFormComponent implements OnInit {
  form!:      FormGroup;
  loading   = false;
  saving    = false;
  showReview = false;
  editId:   number | null = null;

  fotoFile:        File | null   = null;
  fotoPreview:     string | null = null;
  fotoExistente:   boolean       = false;
  uploadingFoto    = false;

  get isEdit(): boolean { return !!this.editId; }

  get reviewFields(): ReviewField[] {
    const v = this.form.value;
    return [
      { label: 'Apellido',          value: v.apellido         },
      { label: 'Nombre',            value: v.nombre           },
      { label: 'DNI',               value: v.dni              },
      { label: 'Fecha nacimiento',  value: v.fecha_nacimiento },
      { label: 'Nacionalidad',      value: v.nacionalidad     },
      { label: 'Cuño',              value: v.cuño             },
      { label: 'Ciudad',    value: v.ciudad   },
      { label: 'Teléfono',  value: v.telefono },
      { label: 'Email',     value: v.email    },
      { label: 'Estado',    value: v.activo ? 'Activo' : 'Inactivo' },
    ];
  }

  constructor(
    private fb:      FormBuilder,
    private service: SoldadorService,
    private route:   ActivatedRoute,
    private router:  Router,
    private cdr:     ChangeDetectorRef,
    private toast:   ToastService,
  ) {}

  ngOnInit(): void {
    this.form = this.fb.group({
      apellido:         ['', [Validators.required, Validators.maxLength(100)]],
      nombre:           ['', [Validators.required, Validators.maxLength(100)]],
      dni:              ['', [Validators.required, Validators.maxLength(20)]],
      fecha_nacimiento: [null],
      nacionalidad:     ['', Validators.maxLength(100)],
      cuño:             ['', Validators.maxLength(20)],
      ciudad:   ['', Validators.maxLength(100)],
      telefono: ['', Validators.maxLength(30)],
      email:    ['', [Validators.email, Validators.maxLength(100)]],
      activo:   [true],
    });

    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.editId  = +id;
      this.loading = true;
      this.service.getOne(this.editId).subscribe({
        next:  s  => {
        this.form.patchValue(s);
        this.fotoExistente = !!s.foto_path;
        this.loading = false;
        this.cdr.markForCheck();
      },
        error: () => { this.loading = false; this.cdr.markForCheck(); this.back(); },
      });
    }
  }

  f(name: string) { return this.form.get(name)!; }

  onFotoSelected(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    this.fotoFile = file;
    const reader = new FileReader();
    reader.onload = e => { this.fotoPreview = e.target?.result as string; this.cdr.markForCheck(); };
    reader.readAsDataURL(file);
  }

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
      next: saved => {
        if (this.fotoFile) {
          this.uploadingFoto = true;
          this.service.uploadFoto(saved.id, this.fotoFile).subscribe({
            next: () => {
              this.uploadingFoto = false;
              this.toast.success(this.isEdit ? 'Soldador actualizado.' : 'Soldador creado.');
              this.router.navigate(['/soldadores']);
            },
            error: () => {
              this.uploadingFoto = false;
              this.saving = false;
              this.cdr.markForCheck();
              this.toast.error('Datos guardados, pero falló la subida de la foto. Reintentá desde edición.');
              this.router.navigate(['/soldadores']);
            },
          });
        } else {
          this.toast.success(this.isEdit ? 'Soldador actualizado.' : 'Soldador creado.');
          this.router.navigate(['/soldadores']);
        }
      },
      error: (err) => {
        this.saving = false;
        this.cdr.markForCheck();
        const errors = err.error?.errors ?? {};
        if (errors['dni']) {
          this.f('dni').setErrors({ taken: true });
        } else {
          this.toast.error('Error al guardar. Revisá los datos.');
        }
      },
    });
  }

  back(): void { this.router.navigate(['/soldadores']); }
}
