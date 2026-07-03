import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

import { PaginationComponent } from '../shared/components/pagination/pagination.component';
import { ConfirmDialogComponent } from '../shared/components/confirm-dialog/confirm-dialog.component';
import { DataReviewModalComponent } from '../shared/components/data-review-modal/data-review-modal.component';
import { SortHeaderComponent } from '../shared/components/sort-header/sort-header.component';
import { UsuariosListComponent } from './list/usuarios-list.component';
import { UsuarioFormComponent } from './form/usuario-form.component';

@NgModule({
  declarations: [
    UsuariosListComponent,
    UsuarioFormComponent,
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
      { path: '',         component: UsuariosListComponent },
      { path: 'new',      component: UsuarioFormComponent  },
      { path: ':id/edit', component: UsuarioFormComponent  },
    ]),
  ],
})
export class UsuariosModule {}
