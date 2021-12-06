import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';


@Injectable({ 
    providedIn: 'root' 
})
export class DespesaService {
    
    constructor(private http: HttpClient) { }

    getAll() {
        return this.http.get<any>(`${environment.apiUrl}/despesas`);
    }
    
    getMovimentacao() {
        return this.http.get<any>(`${environment.apiUrl}/despesas/movimentacao`).pipe(map(res =>{ return res.entity }));
    }

    getById(id: number) {
        return this.http.get<any>(`${environment.apiUrl}/despesas/${id}`).pipe(map(res =>{ return res.entity }));
    }

    store(store: any){
        return this.http.post<any>(`${environment.apiUrl}/despesas`, store);
    }

    update(update: any){
        return this.http.put<any>(`${environment.apiUrl}/despesas/${update.id}`, update);
    }

    delete(id: number){
        return this.http.delete<any>(`${environment.apiUrl}/despesas/${id}`);
    }

}
