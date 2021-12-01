import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

import { AuthenticationService } from '../services/authentication.service';
import { NgxIzitoastService } from "ngx-izitoast";

@Injectable()
export class ErrorInterceptor implements HttpInterceptor {
  constructor(public iziToast: NgxIzitoastService, private authenticationService: AuthenticationService, private router: Router) { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(request).pipe(catchError(err => {
      
      if(err.status == 401){
        this.router.navigate(['/']);
        this.iziToast.error({
          title: 'Atenção!',
          message: 'Usuário não autenticado',
          position: 'topRight'
        });
      }
      
      if(err.status == 500){
        this.iziToast.error({
          title: 'Error!',
          message: err.error.detailMessage,
          position: 'topRight'
        });
      }

      if ([401, 403].indexOf(err.error.codeStatus) !== -1) {

        this.iziToast.error({
          title: 'Atenção!',
          message: err.error.detailMessage,
          position: 'topRight'
        });

        if ([401].indexOf(err.error.codeStatus) !== -1) {
          location.reload(true);
        }

        this.authenticationService.logout();
        
      }

      const error = err.error.detailMessage || err.error.codeStatus;
      return throwError(error);
    }));
  }
}
