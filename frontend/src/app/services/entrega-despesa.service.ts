import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';


@Injectable({ 
    providedIn: 'root' 
})
export class EntregaDespesaService {
    
    baseUrl = environment.apiUrl;

    constructor(private http: HttpClient) { }

    getAll(queryParams: any = {}) {
        return this.http.get<any>(`${this.baseUrl}/despesas-entrega`, { params: queryParams });
    }
    
    getMovimentacao() {
        return this.http.get<any>(`${this.baseUrl}/despesas-entrega/movimentacao`).pipe(map(res =>{ return res.entity }));
    }

    getById(id: number) {
        return this.http.get<any>(`${this.baseUrl}/despesas-entrega/${id}`).pipe(map(res =>{ return res.entity }));
    }

    store(store: any){
        return this.http.post<any>(`${this.baseUrl}/despesas-entrega`, store);
    }

    update(update: any){
        return this.http.put<any>(`${this.baseUrl}/despesas-entrega/${update.id}`, update);
    }

    delete(id: number){
        return this.http.delete<any>(`${this.baseUrl}/despesas-entrega/${id}`);
    }

}
