import { Component } from '@angular/core';

import { ControllerBase } from '@app/controller/controller.base';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

import { EstoqueService } from '@app/services/estoque.service';
import { DashboardService } from '@app/services/dashboard.service';
import { EstoqueFormComponent } from '@app/components/estoque-form/estoque-form.component';

import { MessageService } from 'primeng/api';

import { SubSink } from 'subsink';

import 'bootstrap';
import { NgxIzitoastService } from 'ngx-izitoast';
declare let $: any;

@Component({
  selector: 'app-estoque',
  templateUrl: './estoque.component.html',
  styleUrls: ['./estoque.component.css'],
  providers: [ MessageService ]
})
export class EstoqueComponent extends ControllerBase {
  
  private sub = new SubSink();

  loading: Boolean = false;
  progress: Boolean = false;

  estoques: any[] = [];

  queryParams: any = { status: 'all'};
  term: string;

  enviados: number = 0;
  pagos: number = 0;
  estoque: number = 0;
  vendidos: number = 0;
  totalEstoque: number = 0;

  constructor(
    private estoqueService: EstoqueService,
    private modalCtrl: NgbModal,
    private dashboardService: DashboardService,
    private iziToast: NgxIzitoastService,
    ) {
    super();
  }

  ngOnInit() {
    this.getStart();
  }

  getStart(){
    this.loading = true;
    this.getAll(this.queryParams);
    this.getProdutosEstoque();
    this.getProdutosEnviados();
    this.getProdutosPagos();
    this.getProdutosVendidos();
    this.getProdutosCadastrados();
  }

  openForm(crud, item = undefined){
    const modalRef = this.modalCtrl.open(EstoqueFormComponent, { size: 'lg', backdrop: 'static' });
    modalRef.componentInstance.data = item;
    modalRef.componentInstance.crud = crud;
    modalRef.result.then(res => {
      if(res.message){
        this.iziToast.success({
          title: 'Sucesso!',
          message: res.message,
          position: 'topRight'
        });
      }
      this.getStart();
    })
  }

  getAll(queryParams: any = undefined){
    
    this.sub.sink = this.estoqueService.getAll(queryParams).subscribe(
      (res: any) => {
        this.estoques = res;
      },
      error => {
        this.loading = false;
        console.log(error)
      },
      () => {
        this.loading = false;
      })
  }
  
  rangeStatus() {
    this.loading = true;

    this.sub.sink = this.estoqueService.getAll(this.queryParams).subscribe(
      (res: any) => {
        this.estoques = res;
      },
      error => {
        this.loading = false;
        console.log(error)
      },
      () => {
        this.loading = false;
      })
  }

  getProdutosEnviados(){

    this.sub.sink = this.dashboardService.getProdutosEnviados().subscribe((res: any) => {
      this.loading = false;
      this.enviados = res;
    },
    error => {
      console.log(error)
      this.loading = false;
    });
  }
  
  getProdutosCadastrados(){
    
    this.sub.sink = this.dashboardService.getProdutosCadastrados().subscribe((res: any) => {
      this.loading = false;
      this.totalEstoque = res;
    },
    error => {
      console.log(error)
      this.loading = false;
    });
  }

  getProdutosPagos(){

    this.sub.sink = this.dashboardService.getProdutosPagos().subscribe((res: any) => {
      this.loading = false;
      this.pagos = res;
    },
    error => {
      console.log(error)
      this.loading = false;
    });
  }
  
  getProdutosEstoque(){

    this.sub.sink = this.dashboardService.getProdutosEstoque().subscribe((res: any) => {
      this.loading = false;
      this.estoque = res;
    },
    error => {
      console.log(error)
      this.loading = false;
    });
  }
  
  getProdutosVendidos(){

    this.sub.sink = this.dashboardService.getProdutosVendidos().subscribe((res: any) => {
      this.loading = false;
      this.vendidos = res;
    },
    error => {
      console.log(error)
      this.loading = false;
    });
  }

  delete(id){
    this.loading = true;

    this.sub.sink = this.estoqueService.delete(id).subscribe(
      (res: any) => {
        this.loading = false;
        this.iziToast.success({
          title: 'Sucesso!',
          message: "Deletado com sucesso!",
          position: 'topRight'
        });
        this.getStart();
      },
      error => {
        this.iziToast.error({
          title: 'Atenção!',
          message: error,
          position: 'topRight'
        });
        console.log(error)
        this.loading = false;
      }
    );
  }

  ngOnDestroy(){
    this.sub.unsubscribe();
  }

}
