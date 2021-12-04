import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class EstoqueService {
  
  baseUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getAll(queryParams: any = {}) {
    let params
    if(queryParams.status){
      params = new HttpParams().set('status', queryParams.status);
    }
    return this.http.get<any>(`${this.baseUrl}/estoques`, { params: queryParams, reportProgress: true }).pipe(map(res =>{ return res.response }));
  }

  getEmEstoque() {
    return this.http.get<any>(`${this.baseUrl}/estoques/em-estoque`, { reportProgress: true }).pipe(map(res =>{ return res.entity }));
  }
  
  getById(id: number) {
    return this.http.get<any>(`${this.baseUrl}/estoques/${id}`);
  }

  store(store: any){
    return this.http.post<any>(`${this.baseUrl}/estoques`, store);
  }

  update(id:number, update: any){
    return this.http.put<any>(`${this.baseUrl}/estoques/${id}`, update);
  }

  delete(id: number){
    return this.http.delete<any>(`${this.baseUrl}/estoques/${id}`);
  }

}