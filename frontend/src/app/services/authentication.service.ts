import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { User } from '@app/models/user';
import { environment } from '../../environments/environment';

@Injectable({ providedIn: 'root' })
export class AuthenticationService {
    private currentUserSubject: BehaviorSubject<User>;
    public currentUser: Observable<User>;
    public userData = {};

    constructor(private http: HttpClient) {
        this.currentUserSubject = new BehaviorSubject<User>(JSON.parse(localStorage.getItem('currentUser')));
        this.currentUser = this.currentUserSubject.asObservable();
    }

    public get currentUserValue(): User {
        return this.currentUserSubject.value;
    }

  /**
   * Login
   * @param login
   * @param password
   */
    logar(login: string, password: string) {
        return this.http.post<any>(`${environment.apiUrl}/login`, { login, password })
            .pipe(map(resp => {
                if (resp && resp.token) {
                    localStorage.setItem('currentUser', JSON.stringify(resp.user));
                    this.currentUserSubject.next(resp.user);
                }

                return resp.user;
            }));
    }

    logout() {
        return this.http.get<any>(`${environment.apiUrl}/logout`)
            .pipe(map(res => {     
                localStorage.removeItem('currentUser');
                this.currentUserSubject.next(null);
            }));
    }

}
