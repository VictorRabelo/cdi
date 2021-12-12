import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { animate, style, transition, trigger } from '@angular/animations';

import { EntregaService } from '@app/services/entrega.service';
import { MessageService } from '@app/services/message.service';
import { RelatorioService } from '@app/services/relatorio.service';

import { ModalProductsComponent } from '@app/components/modal-products/modal-products.component';
import { ModalProductDadosComponent } from '@app/components/modal-product-dados/modal-product-dados.component';
import { ModalPessoalComponent } from '@app/components/modal-pessoal/modal-pessoal.component';

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { SubSink } from 'subsink';
import { ControllerBase } from '@app/controller/controller.base';
@Component({
  selector: 'app-entrega-detalhe',
  templateUrl: './entrega-detalhe.component.html',
  styleUrls: ['./entrega-detalhe.component.css'],
  animations: [
    trigger(
      'enterAnimation', [
        transition(':enter', [
          style({transform: 'translateY(50%)', opacity: 0}),
          animate('500ms', style({transform: 'translateY(0)', opacity: 1}))
        ])
      ]
    )
  ],
})
export class EntregaDetalheComponent extends ControllerBase {
  private sub = new SubSink();

  entregaCurrent: any = { cliente: 'Selecione um entregador', itens: [] };

  loading: boolean = false;

  constructor(
    private modalCtrl: NgbModal,
    private activeRoute: ActivatedRoute,
    private service: EntregaService,
    private serviceRelatorio: RelatorioService,
    private message: MessageService,
  ) {
    super();
  }

  ngOnInit(): void {
    this.activeRoute.params.subscribe(params => {
      this.getById(params.id);
    }).unsubscribe();
  }

  getById(id) {
    this.loading = true;
    this.sub.sink = this.service.getById(id).subscribe(res => {
      this.entregaCurrent = res.dadosEntrega;
      
      this.verificaDados(this.entregaCurrent);
      
      this.entregaCurrent.itens = res.dadosProdutos;
      
      this.loading = false;
    }, error => {
      this.loading = false;
      this.message.toastError(error.message)
      console.log(error)
    });
  }

  finishEntrega() {
    if(this.entregaCurrent.itens.length == 0){
      this.message.toastError('Está sem produtos!');
      return;
    }
    
    if(!this.entregaCurrent.entregador){
      this.message.toastError('Está faltando o entregador!');
      return;
    }

    this.loading = true;

    this.service.finishEntrega(this.entregaCurrent).subscribe(res => {
      this.message.toastSuccess(res);
      this.getById(this.entregaCurrent.id_entrega);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

  updateSale() {
    this.loading = true;
    this.service.update(this.entregaCurrent.id_entrega, this.entregaCurrent).subscribe(res => {
      this.message.toastSuccess(res);
      this.getById(this.entregaCurrent.id_entrega);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

  openPessoal() {
    const modalRef = this.modalCtrl.open(ModalPessoalComponent, { size: 'lg', backdrop: 'static' });
    modalRef.componentInstance.type = 'users';
    modalRef.result.then(res => {
      if (res) {
        this.entregaCurrent.entregador_id = res.id;
        this.entregaCurrent.entregador = res.name;

        this.updateSale();
      }
    })
  }

  openProducts() {
    const modalRef = this.modalCtrl.open(ModalProductsComponent, { size: 'xl', backdrop: 'static' });
    modalRef.componentInstance.data = this.entregaCurrent;
    modalRef.result.then(res => {
      this.getById(this.entregaCurrent.id_entrega);
    })
  }

  openItem(item) {
    const modalRef = this.modalCtrl.open(ModalProductDadosComponent, { size: 'md', backdrop: 'static' });
    modalRef.componentInstance.data = {id:item, crud: 'Alterar', type: 'entregas'};
    modalRef.result.then(res => {
      this.getById(this.entregaCurrent.id_entrega);
    })
  }

  darBaixa(){
    this.loading = true;

    this.service.baixaEntrega(this.entregaCurrent.id_entrega, this.entregaCurrent).subscribe(res => {
      this.getById(this.entregaCurrent.id_entrega);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });

  }

  deleteItemConfirm(item) {
    this.message.swal.fire({
      title: 'Atenção!',
      icon: 'warning',
      html: `Deseja remover o item: ${item.produto.name} ?`,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Voltar',
      showCancelButton: true
    }).then(res => {
      if (res.isConfirmed) {
        this.deleteItem(item);
      }
    })
  }

  deleteItem(item) {
    this.loading = true;
    this.service.deleteItem(item.id).subscribe(res => {
      this.message.toastSuccess(res.message);
      this.getById(this.entregaCurrent.id_entrega);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }
  
  private verificaDados(res) {
    if(res.entregador == null) {
      this.entregaCurrent.entregador = 'Entregador não informado';
    }
  }

  downloadRelatorio(){
    this.loading = true;
    this.sub.sink = this.serviceRelatorio.getEntregaDetalhes(this.entregaCurrent.id_entrega).subscribe(
      (res: any) => {
        this.downloadPDF(res.file, res.data, 'detalhes-entrega')
      },
      error => console.log(error),
      ()=>{
        this.loading = false;
      })
  }

  ngOnDestroy(){
    this.sub.unsubscribe();
  }
}
