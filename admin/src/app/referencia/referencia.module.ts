import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { NgbTooltipModule } from '@ng-bootstrap/ng-bootstrap';
import { ReferenciaComponent } from './referencia.component';

const routes: Routes = [
  { path: '', component: ReferenciaComponent },
];

@NgModule({
  declarations: [ReferenciaComponent],
  imports: [
    CommonModule,
    FormsModule,
    NgbTooltipModule,
    RouterModule.forChild(routes),
  ],
})
export class ReferenciaModule {}
