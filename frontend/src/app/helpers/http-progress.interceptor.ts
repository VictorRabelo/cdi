import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor, HttpClient, HttpEventType } from '@angular/common/http';
import { HTTPStatus } from './httpstatus';

import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class HttpProgressInterceptor implements HttpInterceptor {
  constructor(private status: HTTPStatus) {}

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    if (request.reportProgress) {
      // only intercept when the request is configured to report its progress
      return next.handle(request).pipe(
        tap((event: HttpEvent<Object>) => {
          if (event.type === HttpEventType.DownloadProgress) {
            this.status.setProgressBar(true);
          } else if (event.type === HttpEventType.Response) {
            this.status.setProgressBar(false);
          }
        }, error => {
          this.status.setProgressBar(false);
        })
      );
    } else {
      return next.handle(request);
    }
  }
}
