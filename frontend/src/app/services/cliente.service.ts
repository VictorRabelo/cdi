import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';


@Injectable({ 
    providedIn: 'root' 
})
export class ClienteService {
    
    constructor(private http: HttpClient) { }

    getAll() {
        return this.http.get<any>(`${environment.apiUrl}/clientes`).pipe(map(res =>{ return res.response }));
    }

    getById(id: number) {
        return this.http.get<any>(`${environment.apiUrl}/clientes/${id}`).pipe(map(res =>{ return res.response }));
    }

    store(store: any){
        return this.http.post<any>(`${environment.apiUrl}/clientes`, store);
    }

    update(update: any){
        return this.http.put<any>(`${environment.apiUrl}/clientes/${update.id}`, update);
    }

    delete(id: number){
        return this.http.delete<any>(`${environment.apiUrl}/clientes/${id}`);
    }

}
