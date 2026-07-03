import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes, RouterLinkActive, RouterLink } from '@angular/router';
import { LayoutComponent } from './layout.component';
import { ToastComponent } from '../shared/components/toast/toast.component';

const routes: Routes = [
  {
    path: '',
    component: LayoutComponent,
    children: [
      { path: '',          redirectTo: 'dashboard', pathMatch: 'full' },
      {
        path: 'dashboard',
        loadChildren: () => import('../dashboard/dashboard.module').then(m => m.DashboardModule),
      },
      {
        path: 'soldadores',
        loadChildren: () => import('../soldadores/soldadores.module').then(m => m.SoldadoresModule),
      },
      {
        path: 'empresas',
        loadChildren: () => import('../empresas/empresas.module').then(m => m.EmpresasModule),
      },
      {
        path: 'certificados',
        loadChildren: () => import('../certificados/certificados.module').then(m => m.CertificadosModule),
      },
      {
        path: 'usuarios',
        loadChildren: () => import('../usuarios/usuarios.module').then(m => m.UsuariosModule),
      },
      {
        path: 'inspectores',
        loadChildren: () => import('../inspectores/inspectores.module').then(m => m.InspectoresModule),
      },
      {
        path: 'referencia',
        loadChildren: () => import('../referencia/referencia.module').then(m => m.ReferenciaModule),
      },
      {
        path: 'plantillas',
        loadChildren: () => import('../plantillas/plantillas.module').then(m => m.PlantillasModule),
      },
    ],
  },
];

@NgModule({
  declarations: [LayoutComponent],
  imports: [
    CommonModule,
    RouterModule.forChild(routes),
    RouterLink,
    RouterLinkActive,
    ToastComponent,
  ],
})
export class LayoutModule {}
