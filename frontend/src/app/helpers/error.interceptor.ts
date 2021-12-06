import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

import { NgxIzitoastService } from "ngx-izitoast";
import { AuthService } from '@app/services/auth.service';
import { MessageService } from '@app/services/message.service';
import { Store } from '@ngrx/store';
import { Logout } from '@app/core/actions/auth.action';

@Injectable()
export class ErrorInterceptor implements HttpInterceptor {
  constructor(
    public message: MessageService, 
    public store: Store<any>, 
    private authService: AuthService, 
    private router: Router
  ) { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(request).pipe(catchError(error => {
      if (error.status == 0) {
        this.message.alertNet();
      }

      if(error.status == 401){
        this.message.toastError('Usuário não autenticado');

        this.store.dispatch(new Logout());
      }
      
      if(error.status == 500){
        
        if(error.error.detailMessage){
          this.message.toastError(error.error.detailMessage);
        }

        if(error.error.telefone){
          this.message.toastError(error.error.telefone[0]);
        }
      }

      const err = error.error.message || error.error.code;
      
      return throwError(err);
    }));
  }
}
