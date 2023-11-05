import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { AReceberComponent } from './a-receber/a-receber.component';
import { CaixaDolarComponent } from './caixa-dolar/caixa-dolar.component';
import { DespesasComponent } from './despesas/despesas.component';
import { APagarComponent } from './a-pagar/a-pagar.component';

const routes: Routes = [

  {path: '', pathMatch: 'full', redirectTo: 'a-receber'},
  
  {path: 'a-receber', component: AReceberComponent},
  
  {path: 'a-pagar', component: APagarComponent},
  
  {path: 'despesas', component: DespesasComponent},

  {path: 'caixa-dolar', component: CaixaDolarComponent},

];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FinanceiroRoutingModule { }
