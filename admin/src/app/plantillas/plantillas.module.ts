import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

import { PlantillasListComponent }     from './list/plantillas-list.component';
import { CertLayoutDesignerComponent } from './designer/cert-layout-designer.component';
import { DesignerCanvasComponent }     from './designer/canvas/designer-canvas.component';
import { BlockPaletteComponent }       from './designer/palette/block-palette.component';
import { PropertiesPanelComponent }    from './designer/properties/properties-panel.component';
import { CabeceraEditorComponent }     from './block-editors/cabecera/cabecera-editor.component';
import { VariablesEditorComponent }    from './block-editors/variables/variables-editor.component';
import { PasadasEditorComponent }      from './block-editors/pasadas/pasadas-editor.component';
import { ResultadosEditorComponent }   from './block-editors/resultados/resultados-editor.component';
import { JuntaEditorComponent }        from './block-editors/junta/junta-editor.component';

@NgModule({
  declarations: [
    PlantillasListComponent,
    CertLayoutDesignerComponent,
    DesignerCanvasComponent,
    BlockPaletteComponent,
    PropertiesPanelComponent,
    CabeceraEditorComponent,
    VariablesEditorComponent,
    PasadasEditorComponent,
    ResultadosEditorComponent,
    JuntaEditorComponent,
  ],
  imports: [
    CommonModule,
    FormsModule,
    RouterModule.forChild([
      { path: '',                                   component: PlantillasListComponent     },
      { path: 'nueva',                              component: CertLayoutDesignerComponent },
      { path: ':id/editar',                         component: CertLayoutDesignerComponent },
      { path: ':layoutId/editar/cabecera/:blockId',   component: CabeceraEditorComponent    },
      { path: ':layoutId/editar/soldador/:blockId',   component: CabeceraEditorComponent    },
      { path: ':layoutId/editar/variables/:blockId',  component: VariablesEditorComponent   },
      { path: ':layoutId/editar/pasadas/:blockId',    component: PasadasEditorComponent     },
      { path: ':layoutId/editar/resultados/:blockId', component: ResultadosEditorComponent  },
      { path: ':layoutId/editar/junta/:blockId',      component: JuntaEditorComponent       },
    ]),
  ],
})
export class PlantillasModule {}
