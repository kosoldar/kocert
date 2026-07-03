import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

import { PaginationComponent } from '../shared/components/pagination/pagination.component';
import { ConfirmDialogComponent } from '../shared/components/confirm-dialog/confirm-dialog.component';
import { DataReviewModalComponent } from '../shared/components/data-review-modal/data-review-modal.component';
import { SearchableSelectComponent } from '../shared/components/searchable-select/searchable-select.component';

import { CertificadosListComponent } from './list/certificados-list.component';
import { CertificadoFormComponent } from './form/certificado-form.component';

@NgModule({
  declarations: [
    CertificadosListComponent,
    CertificadoFormComponent,
  ],
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    PaginationComponent,
    ConfirmDialogComponent,
    DataReviewModalComponent,
    SearchableSelectComponent,
    RouterModule.forChild([
      { path: '',         component: CertificadosListComponent },
      { path: 'new',      component: CertificadoFormComponent  },
      { path: ':id/edit', component: CertificadoFormComponent  },
    ]),
  ],
})
export class CertificadosModule {}
