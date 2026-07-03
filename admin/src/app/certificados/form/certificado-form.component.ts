import {
  ChangeDetectorRef, Component, OnDestroy, OnInit,
} from '@angular/core';
import {
  AbstractControl, AsyncValidatorFn, FormArray, FormBuilder, FormControl,
  FormGroup, ValidationErrors, Validators,
} from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, Subject, of, timer } from 'rxjs';
import { catchError, distinctUntilChanged, first, map, switchMap, takeUntil } from 'rxjs/operators';

import { CampoNorma, CatalogoNorma, JointDesign, Norma } from '../../core/services/catalogo.service';
import { Inspector } from '../../inspectores/models/inspector.model';
import { ToastService } from '../../core/services/toast.service';
import { ReviewField } from '../../shared/components/data-review-modal/data-review-modal.component';
import { SelectOption } from '../../shared/components/searchable-select/searchable-select.component';
import { Certificado } from '../models/certificado.model';
import { CertificadoFormDataService } from './certificado-form-data.service';
import {
  CertFormStep, CAMPO_LABELS,
  getCampoLabel, shouldShow, getCatalogOptions, getCatalogLabel, getCatalogValue,
  buildCertPayload, buildDraftPayload,
} from './certificado-form.helpers';

@Component({
  selector: 'app-certificado-form',
  standalone: false,
  templateUrl: './certificado-form.component.html',
})
export class CertificadoFormComponent implements OnInit, OnDestroy {
  form!: FormGroup;
  loading    = true;
  saving     = false;
  showReview = false;
  editId:    number | null = null;

  soldadores:   SelectOption[]   = [];
  empresas:     SelectOption[]   = [];
  normas:       Norma[]          = [];
  inspectores:  Inspector[]      = [];
  jointDesigns: JointDesign[]    = [];
  catalogData:  CatalogoNorma | null = null;
  camposNorma:  CampoNorma[]     = [];

  // ── Wizard ─────────────────────────────────────────────────────────────────
  get steps(): Array<{ key: CertFormStep; label: string; helper: string }> {
    const base: Array<{ key: CertFormStep; label: string; helper: string }> = [
      { key: 'solicitante', label: 'Solicitante', helper: 'Soldador, empresa y norma de calificación.' },
      { key: 'variables',   label: 'Variables',   helper: 'Datos técnicos específicos de la norma.' },
      { key: 'fechas',      label: 'Fechas',      helper: 'Calificación, vencimiento y observaciones.' },
    ];
    if (this.isApi1104) {
      base.splice(2, 0, { key: 'pasadas', label: 'Pasadas', helper: 'Variables de soldadura por pasada (API 1104).' });
    }
    return base;
  }
  currentStep: CertFormStep = 'solicitante';
  private stepsTouched = new Set<CertFormStep>();

  get currentStepIndex(): number { return this.steps.findIndex(s => s.key === this.currentStep); }
  get isFirstStep(): boolean     { return this.currentStepIndex === 0; }
  get isLastStep():  boolean     { return this.currentStepIndex === this.steps.length - 1; }
  get currentStepMeta()          { return this.steps[this.currentStepIndex]; }
  get currentStepIssues(): string[] { return this.stepIssues(this.currentStep); }

  stepStatus(step: CertFormStep): 'complete' | 'warning' | 'pending' {
    if (!this.stepsTouched.has(step)) return 'pending';
    return this.stepIssues(step).length === 0 ? 'complete' : 'warning';
  }

  stepStatusLabel(step: CertFormStep): string {
    return ({ complete: 'Listo', warning: 'Revisar', pending: 'Pendiente' })[this.stepStatus(step)];
  }

  stepStatusClass(step: CertFormStep): string {
    return ({ complete: 'text-success', warning: 'text-warning', pending: 'text-muted' })[this.stepStatus(step)];
  }

  get summarySoldador(): string { return this.soldadores.find(x => x.id === this.f('soldador_id').value)?.name ?? '—'; }
  get summaryEmpresa():  string { return this.empresas.find(x => x.id === this.f('empresa_id').value)?.name ?? '—'; }
  get summaryNorma():    string { return this.normas.find(x => x.id === +this.f('norma_id').value)?.nombre ?? '—'; }
  get summaryProceso():  string { return this.variablesForm?.get('proceso')?.value ?? '—'; }
  get summaryPosicion(): string { return this.variablesForm?.get('posicion')?.value ?? '—'; }

  private stepIssues(step: CertFormStep): string[] {
    switch (step) {
      case 'solicitante': {
        const iss: string[] = [];
        if (!this.f('numero').value)                      iss.push('N° RCS requerido.');
        if (this.f('numero').hasError('numeroDuplicado')) iss.push('N° RCS ya existe para este año.');
        if (!this.f('soldador_id').value)                 iss.push('Soldador requerido.');
        if (!this.f('empresa_id').value)                  iss.push('Empresa requerida.');
        if (!this.f('norma_id').value)                    iss.push('Norma requerida.');
        if (!this.f('inspector_id').value)                iss.push('Inspector requerido.');
        if (!this.f('eps_numero').value)                  iss.push('EPS/WPS requerido.');
        return iss;
      }
      case 'variables':
        return this.camposNorma
          .filter(c => c.requerido && shouldShow(c, this.variablesForm.value) && !this.variablesForm.get(c.campo)?.value)
          .map(c => `${getCampoLabel(c.campo)} requerido.`);
      case 'pasadas':
        return this.pasadasArray.length === 0 ? ['Al menos una pasada requerida para API 1104.'] : [];
      case 'fechas': {
        const iss: string[] = [];
        if (!this.f('fecha_calificacion').value) iss.push('Fecha de calificación requerida.');
        return iss;
      }
    }
  }

  private revealStepIssues(step: CertFormStep): void {
    switch (step) {
      case 'solicitante':
        ['numero', 'soldador_id', 'empresa_id', 'norma_id', 'tipo', 'eps_numero']
          .forEach(k => this.form.get(k)?.markAsTouched());
        break;
      case 'variables':
        this.variablesForm.markAllAsTouched();
        break;
      case 'pasadas':
        this.pasadasArray.controls.forEach(g => g.markAllAsTouched());
        break;
      case 'fechas':
        this.f('fecha_calificacion').markAsTouched();
        break;
    }
  }

  goToStep(step: CertFormStep): void { this.currentStep = step; }

  nextStep(): void {
    this.stepsTouched.add(this.currentStep);
    if (this.stepIssues(this.currentStep).length > 0) { this.revealStepIssues(this.currentStep); return; }
    if (!this.isLastStep) this.currentStep = this.steps[this.currentStepIndex + 1].key;
  }

  prevStep(): void {
    if (!this.isFirstStep) this.currentStep = this.steps[this.currentStepIndex - 1].key;
  }
  // ── /Wizard ────────────────────────────────────────────────────────────────

  private editCert:       Certificado | null = null;
  private editCertEstado: string | null      = null;
  private destroy$    = new Subject<void>();
  private patchingEdit = false;
  vencimientoPreset: 0 | 6 | 12 = 0;

  get isDraftCert(): boolean { return this.editCertEstado === 'borrador'; }
  get isEdit():      boolean { return !!this.editId; }
  get variablesForm(): FormGroup { return this.form.get('variables') as FormGroup; }

  get isApi1104(): boolean {
    const norma = this.normas.find(n => n.id === +this.f('norma_id').value);
    return !!norma?.nombre?.includes('1104');
  }

  get showJointDesign(): boolean {
    return !!this.f('norma_id').value && !this.isApi1104;
  }

  get pasadasArray(): FormArray { return this.form.get('pasadas') as FormArray; }

  get reviewFields(): ReviewField[] {
    const v    = this.form.getRawValue();
    const vars = v.variables ?? {};
    const sol  = this.soldadores.find(s => s.id === v.soldador_id);
    const emp  = this.empresas.find(e => e.id === v.empresa_id);
    const nor  = this.normas.find(n => n.id === v.norma_id);
    return [
      { label: 'N° RCS',   value: `${v.numero}-${String(v.anio).slice(-2)}-Rev.${v.revision ?? 0}` },
      { label: 'Soldador', value: sol?.name  ?? `ID ${v.soldador_id}` },
      { label: 'Empresa',  value: emp?.name  ?? `ID ${v.empresa_id}`  },
      { label: 'Norma',    value: nor?.nombre ?? `ID ${v.norma_id}`   },
      { label: 'Tipo',     value: v.tipo },
      { label: 'EPS/WPS',  value: v.eps_numero },
      ...this.camposNorma
        .filter(c => shouldShow(c, vars))
        .map(c => ({ label: CAMPO_LABELS[c.campo] ?? c.campo, value: vars[c.campo] ?? '—' })),
      { label: 'Diseño de junta', value: this.jointDesigns.find(j => j.id === +v.joint_design_id)?.nombre ?? '—' },
      { label: 'Detalle junta',   value: v.joint_detail ?? '—' },
      { label: 'Fecha calificación', value: v.fecha_calificacion },
      { label: 'Vencimiento',        value: v.fecha_vencimiento ?? '—' },
    ];
  }

  cycleVencimiento(): void {
    const seq: Array<0 | 6 | 12> = [0, 6, 12];
    this.vencimientoPreset = seq[(seq.indexOf(this.vencimientoPreset) + 1) % seq.length];
    const base = this.f('fecha_calificacion').value;
    if (this.vencimientoPreset === 0 || !base) { this.f('fecha_vencimiento').setValue(null); return; }
    const d = new Date(base);
    d.setMonth(d.getMonth() + this.vencimientoPreset);
    this.f('fecha_vencimiento').setValue(d.toISOString().slice(0, 10));
  }

  constructor(
    private fb:       FormBuilder,
    private route:    ActivatedRoute,
    private router:   Router,
    private cdr:      ChangeDetectorRef,
    private dataSvc:  CertificadoFormDataService,
    private toast:    ToastService,
  ) {}

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) this.editId = +id;

    this.buildBaseForm();

    this.form.get('norma_id')!.valueChanges
      .pipe(takeUntil(this.destroy$))
      .subscribe(normaId => { if (normaId) this.onNormaChange(normaId); });

    this.dataSvc.loadDropdowns().subscribe({
      next: res => {
        this.soldadores   = res.soldadores;
        this.empresas     = res.empresas;
        this.normas       = res.normas;
        this.inspectores  = res.inspectores;
        this.jointDesigns = res.jointDesigns;

        if (!this.isEdit) {
          this.form.patchValue({ numero: res.siguiente.numero, anio: res.siguiente.anio });
        }

        if (this.isEdit) {
          this.dataSvc.loadCertificado(this.editId!).subscribe({
            next:  cert => this.loadEdit(cert),
            error: ()   => { this.loading = false; this.back(); },
          });
        } else {
          this.loading = false;
          this.cdr.markForCheck();
        }
      },
      error: () => {
        this.loading = false;
        this.cdr.markForCheck();
        this.toast.error('Error al cargar datos.');
      },
    });
  }

  ngOnDestroy(): void { this.destroy$.next(); this.destroy$.complete(); }

  // ── Form building ──────────────────────────────────────────────────────────

  private buildBaseForm(): void {
    const anio = new Date().getFullYear();
    this.form = this.fb.group({
      numero:             [null, [Validators.required, Validators.min(1)], [this.numeroAsyncValidator()]],
      anio:               [anio],
      revision:           [0],
      soldador_id:        [null, Validators.required],
      empresa_id:         [null, Validators.required],
      norma_id:           [null, Validators.required],
      inspector_id:       [null, Validators.required],
      tipo:               ['inicial', Validators.required],
      eps_numero:         ['', [Validators.required, Validators.maxLength(50)]],
      pqr_numero:         [null],
      fecha_calificacion: ['', Validators.required],
      fecha_vencimiento:  [null],
      joint_design_id:    [null],
      joint_detail:       [null],
      observaciones:      [''],
      pasadas:            this.fb.array([]),
      variables:          this.fb.group({}),
    });

    if (this.isEdit) {
      this.form.get('numero')?.disable();
      this.form.get('anio')?.disable();
      this.form.get('revision')?.disable();
    }
  }

  // ── Passes (API 1104) ──────────────────────────────────────────────────────

  private buildPassGroup(): FormGroup {
    return this.fb.group({
      etiqueta:             ['', Validators.required],
      proceso_id:           [null],
      clasificacion_aporte: [null],
      diametro_aporte_mm:   [null],
      polaridad:            [null],
      amperaje_min:         [null],
      amperaje_max:         [null],
      voltaje_min:          [null],
      voltaje_max:          [null],
      velocidad_avance_min: [null],
      velocidad_avance_max: [null],
      progresion:           [null],
    });
  }

  addPasada(): void { this.pasadasArray.push(this.buildPassGroup()); }
  removePasada(i: number): void { this.pasadasArray.removeAt(i); }
  getPassGroup(i: number): FormGroup { return this.pasadasArray.at(i) as FormGroup; }

  // ── /Passes ────────────────────────────────────────────────────────────────

  private buildVariablesGroup(campos: CampoNorma[]): void {
    const controls: Record<string, FormControl> = {};
    for (const campo of campos) {
      controls[campo.campo] = new FormControl(null);
    }
    this.form.setControl('variables', this.fb.group(controls));
    this.updateConditionalValidators();
    this.subscribeCascades();
  }

  private updateConditionalValidators(): void {
    for (const campo of this.camposNorma) {
      const ctrl = this.variablesForm.get(campo.campo);
      if (!ctrl) continue;
      if (campo.requerido && shouldShow(campo, this.variablesForm.value)) {
        ctrl.setValidators([Validators.required]);
      } else {
        ctrl.clearValidators();
      }
      ctrl.updateValueAndValidity({ emitEvent: false });
    }
  }

  private subscribeCascades(): void {
    const vars = this.variablesForm;

    for (const campo of ['p_number', 'grupo_base_metal']) {
      vars.get(campo)?.valueChanges
        .pipe(distinctUntilChanged(), takeUntil(this.destroy$))
        .subscribe(() => {
          if (this.patchingEdit) return;
          vars.get('f_number')?.setValue(null, { emitEvent: false });
          vars.get('grupo_consumible')?.setValue(null, { emitEvent: false });
          vars.get('electrodo')?.setValue(null, { emitEvent: false });
          vars.get('posicion')?.setValue(null, { emitEvent: false });
        });
    }

    for (const campo of ['f_number', 'grupo_consumible']) {
      vars.get(campo)?.valueChanges
        .pipe(distinctUntilChanged(), takeUntil(this.destroy$))
        .subscribe(() => {
          if (this.patchingEdit) return;
          vars.get('electrodo')?.setValue(null, { emitEvent: false });
          vars.get('posicion')?.setValue(null, { emitEvent: false });
        });
    }

    for (const trigger of ['tipo_cupon', 'tipo_junta', 'proceso', 'tipo_producto']) {
      vars.get(trigger)?.valueChanges
        .pipe(distinctUntilChanged(), takeUntil(this.destroy$))
        .subscribe(() => this.updateConditionalValidators());
    }
  }

  private loadEdit(cert: Certificado): void {
    this.editCert       = cert;
    this.editCertEstado = cert.estado;
    this.form.patchValue({
      numero: cert.numero, anio: cert.anio, revision: cert.revision,
      soldador_id: cert.soldador_id, empresa_id: cert.empresa_id,
      norma_id: cert.norma_id, inspector_id: cert.inspector_id ?? null,
      tipo: cert.tipo, eps_numero: cert.eps_numero,
      pqr_numero: cert.pqr_numero ?? null,
      fecha_calificacion: cert.fecha_calificacion,
      fecha_vencimiento:  cert.fecha_vencimiento ?? null,
      joint_design_id: cert.joint_design_id ?? null,
      joint_detail:    cert.joint_detail    ?? null,
      observaciones: cert.observaciones ?? '',
    });
    this.pasadasArray.clear();
    for (const pass of (cert.passes ?? [])) {
      const g = this.buildPassGroup();
      g.patchValue(pass, { emitEvent: false });
      this.pasadasArray.push(g);
    }
    if (!cert.norma_id) {
      this.editCert = null;
      this.loading  = false;
      this.cdr.markForCheck();
    }
  }

  private onNormaChange(normaId: number): void {
    this.dataSvc.loadCatalogo(normaId).subscribe({
      next: data => {
        this.catalogData = data;
        this.camposNorma = data.campos_norma ?? [];
        this.buildVariablesGroup(this.camposNorma);

        if (this.editCert) {
          const varsForPatch = {
            proceso:    this.editCert.proceso,
            posicion:   this.editCert.posicion,
            progresion: this.editCert.progresion ?? null,
            tipo_cupon: this.editCert.tipo_cupon,
            ...(this.editCert.variables ?? {}),
          };
          this.patchingEdit = true;
          this.variablesForm.patchValue(varsForPatch, { emitEvent: false });
          this.patchingEdit = false;
          this.editCert = null;
        }

        this.loading = false;
        this.cdr.markForCheck();
      },
      error: () => {
        this.loading = false;
        this.cdr.markForCheck();
        this.toast.error('Error al cargar catálogos de la norma.');
      },
    });
  }

  // ── Async validator ────────────────────────────────────────────────────────

  private numeroAsyncValidator(): AsyncValidatorFn {
    return (ctrl: AbstractControl): Observable<ValidationErrors | null> => {
      if (!ctrl.value) return of(null);
      const anio = this.form?.value?.anio ?? new Date().getFullYear();
      return timer(500).pipe(
        switchMap(() => this.dataSvc.checkNumero(ctrl.value, anio)),
        map(r => r.disponible ? null : { numeroDuplicado: true }),
        catchError(() => of(null)),
        first(),
      );
    };
  }

  // ── Catalog helpers (template-facing wrappers) ─────────────────────────────

  f(name: string) { return this.form.get(name)!; }

  getCatalogOptions(catalogName: string): unknown[] {
    if (!this.catalogData) return [];
    return getCatalogOptions(catalogName, this.catalogData, this.variablesForm?.value ?? {});
  }

  getCatalogLabel(item: unknown, catalogName: string): string { return getCatalogLabel(item, catalogName); }
  getCatalogValue(item: unknown, catalogName: string): string { return getCatalogValue(item, catalogName); }
  getCampoLabel(campo: string):   string { return getCampoLabel(campo); }
  shouldShow(campo: CampoNorma):  boolean { return shouldShow(campo, this.form?.get('variables')?.value ?? {}); }
  isFieldInvalid(campo: string):  boolean {
    const ctrl = this.variablesForm?.get(campo);
    return !!ctrl && ctrl.invalid && ctrl.touched;
  }
  getFieldServerError(campo: string): string | null {
    return this.variablesForm.get(campo)?.getError('serverError') ?? null;
  }

  // ── Save flow ──────────────────────────────────────────────────────────────

  openReview(): void {
    for (const step of this.steps) {
      this.stepsTouched.add(step.key);
      const issues = this.stepIssues(step.key);
      if (issues.length > 0) {
        this.revealStepIssues(step.key);
        this.currentStep = step.key;
        return;
      }
    }
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.showReview = true;
  }

  onConfirmed(): void {
    this.showReview = false;
    this.saving = true;
    const payload = buildCertPayload(this.form.getRawValue());
    const obs$ = this.isEdit
      ? this.dataSvc.update(this.editId!, payload)
      : this.dataSvc.create(payload);

    obs$.subscribe({
      next: () => {
        this.toast.success(this.isEdit ? 'Certificado actualizado.' : 'Certificado creado.');
        this.router.navigate(['/certificados']);
      },
      error: (err) => {
        this.saving = false;
        this.cdr.markForCheck();
        if (err.status === 422 && err.error?.errors) {
          this.applyServerErrors(err.error.errors);
        } else {
          this.toast.error('Error al guardar. Revisá los datos.');
        }
      },
    });
  }

  saveDraft(): void {
    if (this.saving) return;
    this.saving = true;
    const payload = buildDraftPayload(this.form.getRawValue());
    const obs$ = this.isEdit
      ? this.dataSvc.update(this.editId!, payload)
      : this.dataSvc.create(payload);

    obs$.subscribe({
      next: () => {
        this.toast.success('Borrador guardado.');
        this.router.navigate(['/certificados']);
      },
      error: () => {
        this.saving = false;
        this.cdr.markForCheck();
        this.toast.error('Error al guardar el borrador.');
      },
    });
  }

  private applyServerErrors(errors: Record<string, string[]>): void {
    let firstMessage: string | null = null;
    for (const [key, messages] of Object.entries(errors)) {
      const message = messages[0];
      if (!firstMessage) firstMessage = message;
      const ctrl = key.startsWith('variables.')
        ? this.variablesForm.get(key.replace('variables.', ''))
        : (this.variablesForm.get(key) ?? this.form.get(key));
      if (ctrl) { ctrl.setErrors({ serverError: message }); ctrl.markAsTouched(); }
    }
    if (firstMessage) this.toast.error(firstMessage);
  }

  back(): void { this.router.navigate(['/certificados']); }
}
