import { Component, OnInit } from '@angular/core';
import { CatalogoNorma, CatalogoService, Norma } from '../core/services/catalogo.service';

@Component({
  selector: 'app-referencia',
  standalone: false,
  templateUrl: './referencia.component.html',
})
export class ReferenciaComponent implements OnInit {
  normas:   Norma[]              = [];
  normaId:  number | null        = null;
  catalogo: CatalogoNorma | null = null;
  loading   = false;

  readonly normDescripciones: Record<string, string> = {
    'ASME IX':    'Código de calificación de soldadores y operadores del ASME. Aplica a recipientes a presión, calderas y tuberías de proceso. Define variables esenciales y suplementarias que, si se modifican, requieren recalificación.',
    'ASME B31.3': 'Código de tuberías de proceso del ASME para plantas petroquímicas, refinerías y procesamiento de gas. Las calificaciones se rigen por ASME IX con requisitos adicionales de materiales y servicio.',
    'ASME B31.8': 'Código de sistemas de transporte de gas en tubería (gasoductos). Hereda la calificación de ASME IX e incorpora restricciones específicas de impacto y trabajo en campo.',
    'AWS D1.1':   'Código de soldadura estructural para aceros al carbono y de baja aleación (AWS). Aplica a estructuras de edificios, puentes y estructuras industriales. Define precalificación de WPS y criterios propios de aceptación.',
    'AWS D1.6':   'Código de soldadura estructural para aceros inoxidables (AWS). Cubre aceros austeníticos, ferríticos, martensíticos y dúplex. Prohíbe SAW y tiene requisitos especiales de gas de protección.',
    'API 1104':   'Norma del API para soldadura de tuberías e instalaciones en industria del petróleo y gas. Aplica a ductos de transmisión en campo. Requiere prueba de nick-break además de doblado y VT.',
    'API 650':    'Norma del API para tanques de almacenamiento de petróleo y derivados. Aplica a tanques de techo fijo y flotante de gran volumen. Admite SAW para uniones de cuerpo.',
    'IRAM':       'Norma argentina basada en ISO 9606-1. Califica soldadores para soldadura por fusión de aceros. Usa códigos de proceso ISO (111=SMAW, 135=GMAW, 141=GTAW) y categoriza por tipo de producto (P=chapa, T=tubería).',
  };

  get descripcionNorma(): string {
    if (!this.catalogo) return '';
    return this.normDescripciones[this.catalogo.norma] ?? '';
  }

  readonly sectionTooltips: Record<string, string> = {
    procesos:     'Procesos de soldadura habilitados por esta norma. Al registrar un certificado, solo estos procesos serán aceptados.',
    metalBase:    'Agrupaciones de materiales base según la norma. Cada grupo reúne aceros de propiedades metalúrgicas similares. La columna derecha muestra qué grupos de consumible son compatibles.',
    consumible:   'Grupos de consumible y sus clasificaciones de electrodos. Un soldador calificado con F-No. 4 (p.ej.) puede usar cualquier electrodo listado en ese grupo.',
    matriz:       'Tabla cruzada de compatibilidad. Una celda marcada (✓) indica que ese grupo de consumible puede usarse con ese material base según las tablas de la norma.',
    posiciones:   'Restricciones de posición por grupo de consumible. Los grupos que no aparecen aquí admiten todas las posiciones. P.ej. F-No. 1 (hierro en polvo) solo puede usarse en plana y horizontal.',
    calificados:  'Alcance de calificación: al probar con el material/consumible de la izquierda, se obtiene la calificación para todos los listados a la derecha (principio de downward qualification).',
  };


  constructor(private catalogoSvc: CatalogoService) {}

  ngOnInit(): void {
    this.catalogoSvc.getNormas().subscribe(ns => this.normas = ns);
  }

  onNormaChange(): void {
    if (!this.normaId) { this.catalogo = null; return; }
    this.loading  = true;
    this.catalogo = null;
    this.catalogoSvc.porNorma(this.normaId).subscribe({
      next:  c  => { this.catalogo = c; this.loading = false; },
      error: () => { this.loading = false; },
    });
  }

  // ── Helpers de template ──────────────────────────────────────────────────

  get metalGroups(): string[] {
    return Object.keys(this.catalogo?.metal_consumible ?? {});
  }

  get consumibleGroups(): string[] {
    if (!this.catalogo) return [];
    return (this.catalogo.grupos_consumible ?? []).map((g: any) => g.codigo);
  }

  isCompatible(metal: string, consumible: string): boolean {
    return (this.catalogo?.metal_consumible?.[metal] ?? []).includes(consumible);
  }

  posicionesRestringidas(): Array<{ grupo: string; posiciones: string[] }> {
    const map = this.catalogo?.consumible_posiciones ?? {};
    return Object.entries(map).map(([grupo, posiciones]) => ({ grupo, posiciones }));
  }

  calificadosMetal(): Array<{ probado: string; califica: string[] }> {
    const map = this.catalogo?.metal_calificados ?? {};
    return Object.entries(map).map(([probado, califica]) => ({ probado, califica }));
  }

  calificadosConsumible(): Array<{ probado: string; califica: string[] }> {
    const map = this.catalogo?.consumible_calificados ?? {};
    return Object.entries(map).map(([probado, califica]) => ({ probado, califica }));
  }

  electrodosDeGrupo(codigoGrupo: string): string[] {
    return (this.catalogo?.consumibles ?? [])
      .filter((c: any) => c.grupo?.codigo === codigoGrupo)
      .map((c: any) => c.clasificacion);
  }
}
