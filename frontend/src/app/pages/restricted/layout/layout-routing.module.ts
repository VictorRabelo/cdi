import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { CategoriasComponent } from './categorias/categorias.component';
import { ClientesComponent } from './clientes/clientes.component';
import { UsersComponent } from './users/users.component';
import { EntregasComponent } from './entregas/entregas.component';
import { EstoqueComponent } from './estoque/estoque.component';
import { FornecedoresComponent } from './fornecedores/fornecedores.component';
import { HomeComponent } from './home/home.component';
import { RelatoriosComponent } from './relatorios/relatorios.component';
import { SalesComponent } from './sales/sales.component';
import { SaleDetalheComponent } from './sales/sale-detalhe/sale-detalhe.component';
import { MovitionComponent } from './movition/movition.component';
import { EntregaDetalheComponent } from './entregas/entrega-detalhe/entrega-detalhe.component';
import { Role } from '@app/models/role';
import { AuthGuard } from '@app/guards/auth.guard';
import { EntregasDespesasComponent } from './entregas/entregas-despesas/entregas-despesas.component';

const routes: Routes = [
  
  {path: '', pathMatch: 'full', redirectTo: 'home'},
      
  {path: 'home', component: HomeComponent, data: { animation: 'HomePage' }},

  {
    path: 'vendas', 
    canActivate: [AuthGuard],
    data: { roles: [Role.admin, Role.vendedor], animation: 'VendasPage' },
    children: [
      { path: '', component: SalesComponent},
      { path: ':id', component: SaleDetalheComponent},
    ]
  },

  {
    path: 'entregas', children: [
      { path: '', component: EntregasComponent},
      { path: 'despesas', component: EntregasDespesasComponent},
      { path: ':id', component: EntregaDetalheComponent},
    ], data: { animation: 'EntregasPage' }
  },
      
  {path: 'estoque', component: EstoqueComponent, data: { animation: 'EstoquePage' }},
      
  {path: 'clientes', component: ClientesComponent, canActivate: [AuthGuard], data: { roles: [Role.admin, Role.vendedor], animation: 'ClientesPage' }},

  {path: 'usuarios', component: UsersComponent, data: { animation: 'UsuariosPage' }},

  {path: 'fornecedores', component: FornecedoresComponent, data: { animation: 'FornecedoresPage' }},

  {path: 'relatorios', component: RelatoriosComponent, data: { animation: 'RelatoriosPage' }},
      
  {path: 'categorias', component: CategoriasComponent, data: { animation: 'CategoriasPage' }},

  {
    path: 'movimentacao', children: [
      { path: 'geral', component: MovitionComponent,},
      { path: 'eletronico', component: MovitionComponent },
      { path: 'historico', component: MovitionComponent },
    ],
    data: { animation: 'MovimentacaoPage' }
  },

  {path: 'caixa', loadChildren: () => import('./caixa/caixa.module').then(m => m.CaixaModule), data: { animation: 'CaixaPage' }},
  
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