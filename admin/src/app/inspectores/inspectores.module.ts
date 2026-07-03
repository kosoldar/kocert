import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

import { ConfirmDialogComponent } from '../shared/components/confirm-dialog/confirm-dialog.component';

import { InspectoresListComponent } from './list/inspectores-list.component';
import { InspectorFormComponent } from './form/inspector-form.component';

@NgModule({
  declarations: [
    InspectoresListComponent,
    InspectorFormComponent,
  ],
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    ConfirmDialogComponent,
    RouterModule.forChild([
      { path: '',         component: InspectoresListComponent },
      { path: 'new',      component: InspectorFormComponent   },
      { path: ':id/edit', component: InspectorFormComponent   },
    ]),
  ],
})
export class InspectoresModule {}
