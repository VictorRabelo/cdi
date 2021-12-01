import { Component } from '@angular/core';
import { FormGroup } from '@angular/forms';
import { DomSanitizer } from '@angular/platform-browser';

import { ControllerBase } from '@app/controller/controller.base';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

import { HTTPStatus } from '@app/helpers/httpstatus';
import { CategoriaService } from '@app/services/categoria.service';
import { EstoqueService } from '@app/services/estoque.service';
import { FornecedorService } from '@app/services/fornecedor.service';

import { MessageService } from 'primeng/api';

import { SubSink } from 'subsink';

import 'bootstrap';
import { EstoqueFormComponent } from '@app/components/estoque-form/estoque-form.component';
import { DashboardService } from '@app/services/dashboard.service';
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

  estoques: any;

  status: any = 'all';
  term: string;

  enviados: number = 0;
  pagos: number = 0;
  estoque: number = 0;
  vendidos: number = 0;

  constructor(
    private estoqueService: EstoqueService, 
    private httpStatus: HTTPStatus, 
    private messageService: MessageService, 
    private sanitizer: DomSanitizer, 
    private modalCtrl: NgbModal,
    private dashboardService: DashboardService,
    ) {
    super();
  }

  ngOnInit() {
    this.getStart();
  }

  getStart(){
    this.loading = true;
    this.getAll();
    this.getProdutosEstoque();
    this.getProdutosEnviados();
    this.getProdutosPagos();
    this.getProdutosVendidos();
  }

  openForm(crud, item = undefined){
    const modalRef = this.modalCtrl.open(EstoqueFormComponent, { size: 'lg', backdrop: 'static' });
    modalRef.componentInstance.data = item;
    modalRef.componentInstance.crud = crud;
    modalRef.result.then(res => {
      if(res.message){
        this.messageService.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: res.message});
      }
      this.getStart();
    })
  }

  progressBar(){
    this.httpStatus.getProgressBar().subscribe((status: boolean) => {
        if(status) {
          this.progress = true;
        }
        else {
          this.progress = false;
        }
      }
    );
  }

  getAll(){
    
    this.progressBar();
    
    this.sub.sink = this.estoqueService.getAll().subscribe(
      (res: any) => {
        this.estoques = res.estoque;
        this.estoques.forEach((valor, chave) => {
          let imgURL = 'data:image/'+ this.getExtensionFileName(valor.img) +';base64, '+ valor.path;
          valor.path =  this.sanitizer.bypassSecurityTrustResourceUrl(imgURL);
        })
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
    this.progressBar();

    if(this.status == 0) {
      return this.getAll();
    }

    let req = {
      status: this.status
    }

    this.sub.sink = this.estoqueService.getEspecifico(req).subscribe(
      (res: any) => {
        this.estoques = res;
        this.estoques.forEach((valor, chave) => {
          let imgURL = 'data:image/'+ this.getExtensionFileName(valor.img) +';base64, '+ valor.path;
          valor.path =  this.sanitizer.bypassSecurityTrustResourceUrl(imgURL);
        })
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

  ngOnDestroy(){
    this.sub.unsubscribe();
  }

}
