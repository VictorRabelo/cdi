import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';


@Injectable({ 
    providedIn: 'root' 
})
export class DolarService {
    
    constructor(private http: HttpClient) { }

    getAll() {
        return this.http.get<any>(`${environment.apiUrl}/dolars`).pipe(map(res =>{ return res.entity }));
    }

    getById(id: number) {
        return this.http.get<any>(`${environment.apiUrl}/dolars/${id}`).pipe(map(res =>{ return res.entity }));
    }

    store(store: any){
        return this.http.post<any>(`${environment.apiUrl}/dolars`, store);
    }

    update(update: any){
        return this.http.put<any>(`${environment.apiUrl}/dolars/${update.id}`, update);
    }

    delete(id: number){
        return this.http.delete<any>(`${environment.apiUrl}/dolars/${id}`);
    }

}
