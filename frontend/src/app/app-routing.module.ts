import { NgModule } from '@angular/core';
import { Routes, RouterModule, PreloadAllModules } from '@angular/router';

import { SigninComponent } from './pages/auth/signin/signin.component';
import { LayoutComponent } from './pages/restricted/layout/layout.component';
import { NotFoundComponent } from './components/not-found/not-found.component';

import { Role } from './models/role';
import { AuthGuard } from './guards/auth.guard';

const routes: Routes = [
  {path: '', pathMatch: 'full', redirectTo: 'signin'},

  {path: 'signin', component: SigninComponent},

  {path: 'restricted', component: LayoutComponent, canActivate: [AuthGuard], data: { roles: [Role.admin] }, loadChildren: () => import('./pages/restricted/layout/layout.module').then(m => m.LayoutModule) },

  {path: '**', component: NotFoundComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })],
  exports: [RouterModule]
})
export class AppRoutingModule { }
 