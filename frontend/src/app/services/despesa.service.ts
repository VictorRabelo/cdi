import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';


@Injectable({ 
    providedIn: 'root' 
})
export class DespesaService {
    
    baseUrl = environment.apiUrl;
    
    constructor(private http: HttpClient) { }

    getAll() {
        return this.http.get<any>(`${this.baseUrl}/despesas`);
    }
    
    getMovimentacao() {
        return this.http.get<any>(`${this.baseUrl}/despesas/movimentacao`).pipe(map(res =>{ return res.entity }));
    }

    getById(id: number) {
        return this.http.get<any>(`${this.baseUrl}/despesas/${id}`).pipe(map(res =>{ return res.entity }));
    }

    store(store: any){
        return this.http.post<any>(`${this.baseUrl}/despesas`, store);
    }

    update(update: any){
        return this.http.put<any>(`${this.baseUrl}/despesas/${update.id}`, update);
    }

    delete(id: number){
        return this.http.delete<any>(`${this.baseUrl}/despesas/${id}`);
    }

}
