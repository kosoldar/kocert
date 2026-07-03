import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

import { PaginationComponent } from '../shared/components/pagination/pagination.component';
import { ConfirmDialogComponent } from '../shared/components/confirm-dialog/confirm-dialog.component';
import { DataReviewModalComponent } from '../shared/components/data-review-modal/data-review-modal.component';
import { SortHeaderComponent } from '../shared/components/sort-header/sort-header.component';
import { EmpresasListComponent } from './list/empresas-list.component';
import { EmpresaFormComponent } from './form/empresa-form.component';

@NgModule({
  declarations: [
    EmpresasListComponent,
    EmpresaFormComponent,
  ],
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    PaginationComponent,
    ConfirmDialogComponent,
    DataReviewModalComponent,
    SortHeaderComponent,
    RouterModule.forChild([
      { path: '',         component: EmpresasListComponent },
      { path: 'new',      component: EmpresaFormComponent  },
      { path: ':id/edit', component: EmpresaFormComponent  },
    ]),
  ],
})
export class EmpresasModule {}
