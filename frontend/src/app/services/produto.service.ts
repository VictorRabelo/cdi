import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class ProdutoService {

  constructor(private http: HttpClient) { }

  getAll() {
    return this.http.get<any>(`${environment.apiUrl}/produtos`).pipe(map(res =>{ return res.entity }));
  }
  
  getEnviados() {
    return this.http.get<any>(`${environment.apiUrl}/produtos/enviados`).pipe(map(res =>{ return res.entity }));
  }
  
  getMasculino() {
    return this.http.get<any>(`${environment.apiUrl}/produtos/masculino`).pipe(map(res =>{ return res.entity }));
  }
  
  getFeminino() {
    return this.http.get<any>(`${environment.apiUrl}/produtos/feminino`).pipe(map(res =>{ return res.entity }));
  }
  
  getPagos() {
    return this.http.get<any>(`${environment.apiUrl}/produtos/pago`).pipe(map(res =>{ return res.entity }));
  }
  
  getEstoque() {
    return this.http.get<any>(`${environment.apiUrl}/produtos/estoque`).pipe(map(res =>{ return res.entity }));
  }
  
  getVendidos() {
    return this.http.get<any>(`${environment.apiUrl}/produtos/vendidos`).pipe(map(res =>{ return res.entity }));
  }

  getById(id: number) {
    return this.http.get<any>(`${environment.apiUrl}/produtos/${id}`).pipe(map(res =>{ return res.entity }));
  }

  store(store: any){
    return this.http.post<any>(`${environment.apiUrl}/produtos`, store);
  }
  
  storeDolarFeminino(store: any){
    return this.http.post<any>(`${environment.apiUrl}/produtos/feminino`, store);
  }
  
  storeDolarMasculino(store: any){
    return this.http.post<any>(`${environment.apiUrl}/produtos/masculino`, store);
  }

  update(update: any){
    return this.http.put<any>(`${environment.apiUrl}/produtos/${update.id}`, update);
  }

  delete(id: number){
    return this.http.delete<any>(`${environment.apiUrl}/produtos/${id}`);
  }

}
