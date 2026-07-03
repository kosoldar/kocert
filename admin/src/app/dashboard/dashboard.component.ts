import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';

interface Stats {
  soldadores: number;
  vigentes: number;
  vencidos: number;
  por_vencer: number;
  emitidos_mes: number;
}

interface UltimoCert {
  id: number;
  numero: string;
  soldador: string;
  norma: string;
  estado: string;
  vencimiento: string;
}

@Component({
  selector: 'app-dashboard',
  standalone: false,
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.scss',
})
export class DashboardComponent implements OnInit {
  stats: Stats | null = null;
  ultimos: UltimoCert[] = [];
  loading = true;
  error   = '';

  constructor(private http: HttpClient, private cdr: ChangeDetectorRef) {}

  ngOnInit(): void {
    this.http.get<{ stats: Stats; ultimos_certificados: UltimoCert[] }>(
      `${environment.apiUrl}/dashboard/stats`
    ).subscribe({
      next: res => {
        this.stats   = res.stats;
        this.ultimos = res.ultimos_certificados;
        this.loading = false;
        this.cdr.markForCheck();
      },
      error: () => {
        this.error   = 'Error al cargar estadísticas.';
        this.loading = false;
        this.cdr.markForCheck();
      },
    });
  }

  estadoBadge(estado: string): string {
    return estado === 'vigente' ? 'success' : estado === 'vencido' ? 'danger' : 'secondary';
  }
}
