import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { HttpEvent, HttpHandler, HttpInterceptor, HttpRequest } from '@angular/common/http';

import { BehaviorSubject, Observable, throwError } from 'rxjs';
import { catchError, finalize, map } from 'rxjs/operators';
import { HTTPStatus } from './httpstatus';

@Injectable()
export class HTTPListener implements HttpInterceptor {

    private _requests = 0;
    
    constructor(private status: HTTPStatus, private router: Router) {}

    intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {

        ++this._requests;
        this.status.setHttpStatus(true);
        
        return next.handle(req).pipe(
            map(event => {
                console.log(event)
                return event;
            }),
            catchError(error => {
                if (error.status === 401) {
                    // this.auth.setToken(null);
                    // this.auth.setRedirectUrl(this.router.url);
                    this.router.navigate(['/access']);
                }

                return throwError(error);
            }),
            finalize(() => {
                --this._requests;
                this.status.setHttpStatus(this._requests > 0);
            })
        );
    }
}