import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';


@Injectable({ 
    providedIn: 'root' 
})
export class MovitionService {
    
    base_url = environment.apiUrl;

    constructor(private http: HttpClient) { }
    
    getAll(queryParams: any = {}) {
        let params = new HttpParams().set('type', queryParams.type);
        
        if(queryParams.date !== ''){
            params = params.append('date', queryParams.date);
        }

        return this.http.get<any>(`${this.base_url}/caixa`, { params: params });
    }
    
    store(store: any){
        return this.http.post<any>(`${this.base_url}/caixa`, store);
    }

    delete(id: number){
        return this.http.delete<any>(`${this.base_url}/caixa/${id}`);
    }

    //Tipos de caixa
    getAllItem() {
        return this.http.get<any>(`${this.base_url}/caixa/tipos`);
    }
    
    getItemById(id: any) {
        return this.http.get<any>(`${this.base_url}/caixa/tipos/${id}`);
    }
    
    createItem(dados: any) {
        return this.http.post<any>(`${this.base_url}/caixa/tipos`, dados);
    }

    updateItem(id: any, dados: any) {
        return this.http.put<any>(`${this.base_url}/caixa/tipos/${id}`, dados);
    }
    deleteItem(id: any) {
        return this.http.delete<any>(`${this.base_url}/caixa/tipos/${id}`);
    }
}
