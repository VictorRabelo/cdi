import { Injectable } from '@angular/core';
import { Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { ControllerBase } from '@app/controller/controller.base';

@Injectable({ providedIn: 'root' })
export class AuthGuard implements CanActivate {
  constructor(private router: Router, private controllerBase: ControllerBase) {}

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
    const currentUser = this.controllerBase.currentUser;
    if (currentUser) {
        if (route.data.role && route.data.role.indexOf(currentUser.role) === -1) {
            this.router.navigate(['/']);
            return false;
        }

        return true;
    }

    this.router.navigate(['/signin'], { queryParams: { returnUrl: state.url, error: 'Não autorizado' }});
    
    return false;
  }
}
