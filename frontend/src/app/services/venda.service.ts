import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';
import { Subject } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class VendaService {
    
    // Observable string sources
    private dia = new Subject<number>();
    private mes = new Subject<number>();
    private total = new Subject<number>();

    // Observable string streams
    dia$ = this.dia.asObservable();
    mes$ = this.mes.asObservable();
    total$ = this.total.asObservable();
    
    constructor(private http: HttpClient) { }

    getAll() {
        return this.http.get<any>(`${environment.apiUrl}/vendas`).pipe(map(res =>{ return res.entity }));
    }

    all() {
        return this.http.get<any>(`${environment.apiUrl}/vendas/all`).pipe(map(res =>{ return res.entity }));
    }

    getDia() {
        return this.http.get<any>(`${environment.apiUrl}/vendas/dia`).pipe(map(res =>{
            return res.entity 
        }));
    }
    
    getMes() {
        return this.http.get<any>(`${environment.apiUrl}/vendas/mes`).pipe(map(res =>{ 
            return res.entity 
        }));
    }

    getMesEspecifico(date: any) {
        return this.http.get<any>(`${environment.apiUrl}/vendas/especifica`, { params: date }).pipe(map(res =>{ 
            return res.entity 
        }));
    }

    getTotal() {
        return this.http.get<any>(`${environment.apiUrl}/vendas/total`).pipe(map(res =>{ 
            return res.entity 
        }));
    }
    
    getContas() {
        return this.http.get<any>(`${environment.apiUrl}/vendas/a-receber`).pipe(map(res =>{ 
            return res.entity 
        }));
    }
    
    getById(id: number) {
        return this.http.get<any>(`${environment.apiUrl}/vendas/${id}`).pipe(map(res =>{ return res.entity }));
    }

    store(store: any){
        return this.http.post<any>(`${environment.apiUrl}/vendas`, store);
    }

    update(id: number, update: any){
        return this.http.put<any>(`${environment.apiUrl}/vendas/${id}`, update);
    }
    
    updateReceber(update: any){
        return this.http.put<any>(`${environment.apiUrl}/vendas/${update.id}/receber`, update);
    }

    delete(id: number){
        return this.http.delete<any>(`${environment.apiUrl}/vendas/${id}`);
    }

}
