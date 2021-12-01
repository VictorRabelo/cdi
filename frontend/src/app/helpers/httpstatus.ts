import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class HTTPStatus {
    
    private requestInFlight$: BehaviorSubject<boolean>;
    private statusProgress$: BehaviorSubject<boolean>;
    
    constructor() {
        this.requestInFlight$ = new BehaviorSubject(false);
        this.statusProgress$ = new BehaviorSubject(false);
    }

    setProgressBar(statusProgress: boolean) {
        this.statusProgress$.next(statusProgress);
    }

    getProgressBar(): Observable<boolean> {
        return this.statusProgress$.asObservable();
    }
    
    setHttpStatus(inFlight: boolean) {
        this.requestInFlight$.next(inFlight);
    }

    getHttpStatus(): Observable<boolean> {
        return this.requestInFlight$.asObservable();
    }
}