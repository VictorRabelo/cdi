import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class FormaPagamentoService {
  baseUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getAll(queryParams: any = {}) {
    let params
    if(queryParams.status){
      params = new HttpParams().set('status', queryParams.status);
    }
    return this.http.get<any>(`${this.baseUrl}/forma-pagamento`, { params: queryParams, reportProgress: true }).pipe(map(res =>{ return res.response }));
  }
  
  getById(id: number) {
    return this.http.get<any>(`${this.baseUrl}/forma-pagamento/${id}`);
  }

  store(store: any){
    return this.http.post<any>(`${this.baseUrl}/forma-pagamento`, store);
  }

  update(id:number, update: any){
    return this.http.put<any>(`${this.baseUrl}/forma-pagamento/${id}`, update);
  }

  delete(id: number){
    return this.http.delete<any>(`${this.baseUrl}/forma-pagamento/${id}`);
  }

}