import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { FemininoComponent } from './feminino/feminino.component';
import { MasculinoComponent } from './masculino/masculino.component';


const routes: Routes = [
  {path: '', pathMatch: 'full', redirectTo: 'masculino'},
      
  {path: 'masculino', component: MasculinoComponent},

  {path: 'feminino', component: FemininoComponent},
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CotacaoPerfumeRoutingModule { }
