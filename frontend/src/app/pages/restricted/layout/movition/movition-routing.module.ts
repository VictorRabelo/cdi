import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MoveAllComponent } from './move-all/move-all.component';
import { MoveEletronicoComponent } from './move-eletronico/move-eletronico.component';
import { MoveGeralComponent } from './move-geral/move-geral.component';


const routes: Routes = [
  
  {path: '', pathMatch: 'full', redirectTo: 'masculino'},
      
  {path: 'geral', component: MoveGeralComponent },

  {path: 'historico', component: MoveAllComponent },

  {path: 'eletronico', component: MoveEletronicoComponent }
  
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MovitionRoutingModule { }
