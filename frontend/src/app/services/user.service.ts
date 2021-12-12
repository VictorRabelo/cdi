import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import {environment} from '../../environments/environment';
import { map } from 'rxjs/operators';

@Injectable({ providedIn: 'root' })
export class UserService {
  baseUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getAll() {
    return this.http.get<any>(`${this.baseUrl}/users`).pipe(map(res =>{ return res.response }));
  }

  getById(id: number) {
    return this.http.get<any>(`${this.baseUrl}/users/${id}`).pipe(map(res =>{ return res.response }));
  }

  store(store: any){
    return this.http.post<any>(`${this.baseUrl}/users`, store);
  }

  update(update: any){
    return this.http.put<any>(`${this.baseUrl}/users/${update.id}`, update).pipe(map(res =>{ return res.response }));
  }

  delete(id: number){
    return this.http.delete<any>(`${this.baseUrl}/users/${id}`);
  }
}
