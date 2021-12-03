import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class EstoqueService {

  constructor(private http: HttpClient) { }

  getAll(queryParams: any = {}) {
    return this.http.get<any>(`${environment.apiUrl}/estoques`, { params: queryParams, reportProgress: true }).pipe(map(res =>{ return res.response }));
  }

  getEmEstoque() {
    return this.http.get<any>(`${environment.apiUrl}/estoques/em-estoque`, { reportProgress: true }).pipe(map(res =>{ return res.entity }));
  }
  
  getById(id: number) {
    return this.http.get<any>(`${environment.apiUrl}/estoques/${id}`);
  }

  store(store: any){
    return this.http.post<any>(`${environment.apiUrl}/estoques`, store);
  }

  update(id:number, update: any){
    return this.http.put<any>(`${environment.apiUrl}/estoques/${id}`, update);
  }

  delete(id: number){
    return this.http.delete<any>(`${environment.apiUrl}/estoques/${id}`);
  }

}