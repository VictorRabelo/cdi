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

        return this.http.get<any>(`${environment.apiUrl}/movition`, { params: params });
    }
    
    store(store: any){
        return this.http.post<any>(`${environment.apiUrl}/movition`, store);
    }

    delete(id: number){
        return this.http.delete<any>(`${environment.apiUrl}/movition/${id}`);
    }

}
