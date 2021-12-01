import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { CategoriasComponent } from './categorias/categorias.component';
import { ClientesComponent } from './clientes/clientes.component';
import { EntregasComponent } from './entregas/entregas.component';
import { EstoqueComponent } from './estoque/estoque.component';
import { FornecedoresComponent } from './fornecedores/fornecedores.component';
import { HomeComponent } from './home/home.component';
import { PerfilComponent } from './perfil/perfil.component';
import { RelatoriosComponent } from './relatorios/relatorios.component';
import { VendaComponent } from './venda/venda.component';

const routes: Routes = [
  
  {path: '', pathMatch: 'full', redirectTo: 'home'},
      
  {path: 'home', component: HomeComponent},

  {path: 'perfil', component: PerfilComponent},

  {path: 'vendas', component: VendaComponent},
      
  {path: 'estoque', component: EstoqueComponent},

  {path: 'entregas', component: EntregasComponent},
      
  {path: 'clientes', component: ClientesComponent},

  {path: 'fornecedores', component: FornecedoresComponent},

  {path: 'relatorios', component: RelatoriosComponent},
      
  {path: 'categorias', component: CategoriasComponent},

  {path: 'movimentacao', loadChildren: () => import('./movition/movition.module').then(m => m.MovitionModule) },
  
  {path: 'cotacao-perfume', loadChildren: () => import('./cotacao-perfume/cotacao-perfume.module').then(m => m.CotacaoPerfumeModule) },
  
  {path: 'caixa', loadChildren: () => import('./caixa/caixa.module').then(m => m.CaixaModule) },
  
];
  
@NgModule({
    imports: [
      RouterModule.forChild(routes)
    ],
    exports: [
      RouterModule
    ]
})
export class LayoutRoutingModule {}