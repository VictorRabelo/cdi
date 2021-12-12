import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';

@Injectable({ providedIn: 'root' })
export class EntregaService {
    
    baseUrl = environment.apiUrl;
    
    constructor(private http: HttpClient) { }

    getAll(queryParams: any = {}) {
        let params
        
        if(queryParams.date){
            params = new HttpParams().set('date', queryParams.date);
        }
        
        if(queryParams.aReceber){
            params = new HttpParams().set('aReceber', queryParams.aReceber);
        }

        return this.http.get<any>(`${this.baseUrl}/entregas`, { params: params }).pipe(map(res =>{ return res.response }));
    }

    getById(id: number) {
        return this.http.get<any>(`${this.baseUrl}/entregas/${id}`);
    }
    
    update(id: number, update: any) {
        return this.http.put<any>(`${this.baseUrl}/entregas/${id}`, update);
    }

    baixaEntrega(id: number, dados: any) {
        return this.http.put<any>(`${this.baseUrl}/entregas/${id}/dar-baixa`, dados);
    }

    store(store: any){
        return this.http.post<any>(`${this.baseUrl}/entregas`, store).pipe(map(res =>{ return res.response }));
    }

    finishEntrega(dados: any) {
        return this.http.post<any>(`${this.baseUrl}/entregas/finish`, dados);
    }

    delete(id: number){
        return this.http.delete<any>(`${this.baseUrl}/entregas/${id}`);
    }
    
    //itens
    createItem(dados: any) {
        return this.http.post<any>(`${this.baseUrl}/entregas/item`, dados);
    }
    getItemById(id) {
        return this.http.get<any>(`${this.baseUrl}/entregas/item/${id}`);
    }
    updateItem(id, dados) {
        return this.http.put<any>(`${this.baseUrl}/entregas/item/${id}`, dados);
    }
    deleteItem(id) {
        return this.http.delete<any>(`${this.baseUrl}/entregas/item/${id}`);
    }
}
