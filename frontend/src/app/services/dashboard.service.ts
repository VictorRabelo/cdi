import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';

@Injectable({ providedIn: 'root' })
export class DashboardService {

    constructor(private http: HttpClient) { }

    getVendasDia() {
        return this.http.get<any>(`${environment.apiUrl}/dashboard/vendas-dia`);
    }
    
    getVendasMes() {
        return this.http.get<any>(`${environment.apiUrl}/dashboard/vendas-mes`);
    }
    
    getVendasTotal() {
        return this.http.get<any>(`${environment.apiUrl}/dashboard/vendas-total`);
    }
    
    getTotalClientes() {
        return this.http.get<any>(`${environment.apiUrl}/dashboard/clientes-total`);
    }
    
    getProdutosEnviados() {
        return this.http.get<any>(`${environment.apiUrl}/dashboard/produtos-enviados`);
    }
    
    getProdutosCadastrados() {
        return this.http.get<any>(`${environment.apiUrl}/dashboard/produtos-cadastrados`);
    }
    
    getProdutosPagos() {
        return this.http.get<any>(`${environment.apiUrl}/dashboard/produtos-pagos`);
    }
    
    getProdutosEstoque() {
        return this.http.get<any>(`${environment.apiUrl}/dashboard/produtos-estoque`);
    }
    
    getProdutosVendidos() {
        return this.http.get<any>(`${environment.apiUrl}/dashboard/produtos-vendidos`);
    }
    
    getContasReceber() {
        return this.http.get<any>(`${environment.apiUrl}/dashboard/contas-receber`);
    }

}