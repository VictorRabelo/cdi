import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';


@Injectable({ 
    providedIn: 'root' 
})
export class MovitionService {
    
    constructor(private http: HttpClient) { }

    getAll() {
        return this.http.get<any>(`${environment.apiUrl}/movition`).pipe(map(res =>{ return res.entity }));
    }
    
    getGeral() {
        return this.http.get<any>(`${environment.apiUrl}/movition/geral`).pipe(map(res =>{ return res.entity }));
    }
    
    getEletronico() {
        return this.http.get<any>(`${environment.apiUrl}/movition/eletronico`).pipe(map(res =>{ return res.entity }));
    }

    getById(id: number) {
        return this.http.get<any>(`${environment.apiUrl}/movition/${id}`).pipe(map(res =>{ return res.entity }));
    }

    store(store: any){
        return this.http.post<any>(`${environment.apiUrl}/movition`, store);
    }

    update(update: any){
        return this.http.put<any>(`${environment.apiUrl}/movition/${update.id}`, update);
    }

    delete(id: number){
        return this.http.delete<any>(`${environment.apiUrl}/movition/${id}`);
    }

}
