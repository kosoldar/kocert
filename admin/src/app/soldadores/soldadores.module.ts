import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

import { PaginationComponent } from '../shared/components/pagination/pagination.component';
import { ConfirmDialogComponent } from '../shared/components/confirm-dialog/confirm-dialog.component';
import { DataReviewModalComponent } from '../shared/components/data-review-modal/data-review-modal.component';
import { SortHeaderComponent } from '../shared/components/sort-header/sort-header.component';
import { SoldadoresListComponent } from './list/soldadores-list.component';
import { SoldadorFormComponent } from './form/soldador-form.component';

@NgModule({
  declarations: [
    SoldadoresListComponent,
    SoldadorFormComponent,
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
      { path: '',          component: SoldadoresListComponent },
      { path: 'new',       component: SoldadorFormComponent   },
      { path: ':id/edit',  component: SoldadorFormComponent   },
    ]),
  ],
})
export class SoldadoresModule {}
