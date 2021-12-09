import { Component, ViewEncapsulation } from '@angular/core';
import { Inject } from '@angular/core';
import { DOCUMENT } from '@angular/common';

import { ControllerBase } from 'src/app/controller/controller.base';
import { DashboardService } from '@app/services/dashboard.service';

import { SubSink } from 'subsink';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class HomeComponent extends ControllerBase {

  private sub = new SubSink();

  public dia: number = 0;
  public mes: number = 0;
  public total: number = 0;
  public clientes: number = 0;
  public enviados: number = 0;
  public pagos: number = 0;
  public estoque: number = 0;
  public vendidos: number = 0;
  public contasReceber: number = 0;
  public produtosCadastrados: number = 0;

  public loadingVendasMes: Boolean = false;
  public loadingVendasDia: Boolean = false;
  public loadingVendasTotal: Boolean = false;
  public loadingTotalClientes: Boolean = false;
  public loadingProdutosEnviados: Boolean = false;
  public loadingProdutosPagos: Boolean = false;
  public loadingProdutosEstoque: Boolean = false;
  public loadingProdutosVendidos: Boolean = false;
  public loadingContasReceber: Boolean = false;
  public loadingProdutosCadastrados: Boolean = false;

  public frasesDoDia: any = [
    'Ótimo Trabalho!',
    'Ótima Semana!',
    'Semana Abençoada!',
    'Vai da tudo certo!',
    'O Segredo do SUCESSO só depende de você!',
  ];

  public random: any;
  public today: number = Date.now();

  
  constructor(
    @Inject(DOCUMENT) private document: any, 
    private dashboardService: DashboardService
  ) { 
    super();
  }

  ngOnInit() {
    this.random = this.getRandonText();
    this.getStart();
  }

  getStart(){
    this.getVendasDia();
    this.getVendasMes();
    this.getVendasTotal();
    this.getTotalClientes();
    this.getProdutosEnviados();
    this.getProdutosPagos();
    this.getProdutosEstoque();
    this.getProdutosVendidos();
    this.getProdutosCadastrados();
    this.getContasReceber();
  }

  getVendasDia(){
    this.loadingVendasDia = true;

    this.sub.sink = this.dashboardService.getVendasDia().subscribe((res: any) => {
      this.loadingVendasDia = false;
      this.dia = res;
    },
    error => {
      console.log(error)
      this.loadingVendasDia = false;
    });
  }
  
  getVendasMes(){
    this.loadingVendasMes = true;

    this.sub.sink = this.dashboardService.getVendasMes().subscribe((res: any) => {
      this.loadingVendasMes = false
      this.mes = res;
    },
    error => {
      console.log(error)
      this.loadingVendasMes = false;
    });
  }
  
  getVendasTotal(){
    this.loadingVendasTotal = true;

    this.sub.sink = this.dashboardService.getVendasTotal().subscribe((res: any) => {
      this.loadingVendasTotal = false;
      this.total = res;
    },
    error => {
      console.log(error)
      this.loadingVendasTotal = false;
    });
  }
  
  getTotalClientes(){
    this.loadingTotalClientes = true;

    this.sub.sink = this.dashboardService.getTotalClientes().subscribe((res: any) => {
      this.loadingTotalClientes = false;
      this.clientes = res;
    },
    error => {
      console.log(error)
      this.loadingTotalClientes = false;
    });
  }
  
  getProdutosEnviados(){
    this.loadingProdutosEnviados = true;

    this.sub.sink = this.dashboardService.getProdutosEnviados().subscribe((res: any) => {
      this.loadingProdutosEnviados = false;
      this.enviados = res;
    },
    error => {
      console.log(error)
      this.loadingProdutosEnviados = false;
    });
  }
 
  getProdutosCadastrados(){
    this.loadingProdutosCadastrados = true;

    this.sub.sink = this.dashboardService.getProdutosCadastrados().subscribe((res: any) => {
      this.loadingProdutosCadastrados = false;
      this.produtosCadastrados = res;
    },
    error => {
      console.log(error)
      this.loadingProdutosEnviados = false;
    });
  }
  
  getProdutosPagos(){
    this.loadingProdutosPagos = true;

    this.sub.sink = this.dashboardService.getProdutosPagos().subscribe((res: any) => {
      this.loadingProdutosPagos = false;
      this.pagos = res;
    },
    error => {
      console.log(error)
      this.loadingProdutosPagos = false;
    });
  }
  
  getProdutosEstoque(){
    this.loadingProdutosEstoque = true;

    this.sub.sink = this.dashboardService.getProdutosEstoque().subscribe((res: any) => {
      this.loadingProdutosEstoque = false;
      this.estoque = res;
    },
    error => {
      console.log(error)
      this.loadingProdutosEstoque = false;
    });
  }
  
  getProdutosVendidos(){
    this.loadingProdutosVendidos = true;

    this.sub.sink = this.dashboardService.getProdutosVendidos().subscribe((res: any) => {
      this.loadingProdutosVendidos = false;
      this.vendidos = res;
    },
    error => {
      console.log(error)
      this.loadingProdutosVendidos = false;
    });
  }
  
  getContasReceber(){
    this.loadingContasReceber = true;

    this.sub.sink = this.dashboardService.getContasReceber().subscribe((res: any) => {
      this.loadingContasReceber = false;
      this.contasReceber = res;
    },
    error => {
      console.log(error)
      this.loadingContasReceber = false;
    });
  }

  getRandonText(){
    let dados = Math.floor(Math.random() * 5);
  
    return this.frasesDoDia[dados];
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }

}
