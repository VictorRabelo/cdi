import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { CategoriasComponent } from './categorias/categorias.component';
import { ClientesComponent } from './clientes/clientes.component';
import { UsersComponent } from './users/users.component';
import { EntregasComponent } from './entregas/entregas.component';
import { EstoqueComponent } from './estoque/estoque.component';
import { FornecedoresComponent } from './fornecedores/fornecedores.component';
import { HomeComponent } from './home/home.component';
import { PerfilComponent } from './perfil/perfil.component';
import { RelatoriosComponent } from './relatorios/relatorios.component';
import { SalesComponent } from './sales/sales.component';
import { SaleDetalheComponent } from './sales/sale-detalhe/sale-detalhe.component';
import { MovitionComponent } from './movition/movition.component';

const routes: Routes = [
  
  {path: '', pathMatch: 'full', redirectTo: 'home'},
      
  {path: 'home', component: HomeComponent},

  {path: 'perfil', component: PerfilComponent},

  {
    path: 'vendas', children: [
      { path: '', component: SalesComponent },
      { path: ':id', component: SaleDetalheComponent },
    ]
  },
      
  {path: 'estoque', component: EstoqueComponent},

  {path: 'entregas', component: EntregasComponent},
      
  {path: 'clientes', component: ClientesComponent},

  {path: 'usuarios', component: UsersComponent},

  {path: 'fornecedores', component: FornecedoresComponent},

  {path: 'relatorios', component: RelatoriosComponent},
      
  {path: 'categorias', component: CategoriasComponent},

  {
    path: 'movimentacao', children: [
      { path: 'geral', component: MovitionComponent },
      { path: 'eletronico', component: MovitionComponent },
      { path: 'historico', component: MovitionComponent },
    ]
  },

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