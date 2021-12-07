import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { animate, style, transition, trigger } from '@angular/animations';

import { VendaService } from '@app/services/venda.service';

import { SaleFinishComponent } from './sale-finish/sale-finish.component';
import { ModalProductsComponent } from '@app/components/modal-products/modal-products.component';
import { ModalProductDadosComponent } from '@app/components/modal-product-dados/modal-product-dados.component';
import { ModalPessoalComponent } from '@app/components/modal-pessoal/modal-pessoal.component';
import { ModalDebitarComponent } from '@app/components/modal-debitar/modal-debitar.component';

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '@app/services/message.service';


@Component({
  selector: 'app-sale-detalhe',
  templateUrl: './sale-detalhe.component.html',
  styleUrls: ['./sale-detalhe.component.css'],
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
export class SaleDetalheComponent implements OnInit {

  vendaCurrent: any = { cliente: 'Selecione um cliente', itens: [] };

  loading: boolean = false;

  constructor(
    private modalCtrl: NgbModal,
    private activeRoute: ActivatedRoute,
    private service: VendaService,
    private message: MessageService,
  ) { }

  ngOnInit(): void {
    this.activeRoute.params.subscribe(params => {
      this.getById(params.id);
    }).unsubscribe();
  }

  getById(id) {
    this.loading = true;
    this.service.getById(id).subscribe(res => {
      this.vendaCurrent = res.dadosVenda;
      
      this.verificaDados(this.vendaCurrent);
      
      this.vendaCurrent.itens = res.dadosProdutos;
      
      this.loading = false;
    }, error => {
      this.loading = false;
      this.message.toastError(error.message)
      console.log(error)
    });
  }

  detailSale(){
    const modalRef = this.modalCtrl.open(SaleFinishComponent, { size: 'md', backdrop: 'static' });
    modalRef.componentInstance.data = Object.assign({}, this.vendaCurrent);
    modalRef.componentInstance.type = 'detail';
  }

  finishSale() {
    if(this.vendaCurrent.itens.length == 0){
      this.message.toastError('Está sem produtos!');
      return;
    }
    
    if(!this.vendaCurrent.cliente){
      this.message.toastError('Está faltando o cliente!');
      return;
    }

    if(this.vendaCurrent.restante == undefined){
      this.vendaCurrent.restante = this.vendaCurrent.total_final;
      this.vendaCurrent.pago = 0.00;
      this.vendaCurrent.debitar = 0.00;
      this.vendaCurrent.pagamento = '';
      this.vendaCurrent.status = '';
      this.vendaCurrent.caixa = '';
    } else {
      this.vendaCurrent.debitar = 0.00;
    }

    const modalRef = this.modalCtrl.open(SaleFinishComponent, { size: 'md', backdrop: 'static' });
    modalRef.componentInstance.data = Object.assign({}, this.vendaCurrent);
    modalRef.componentInstance.type = 'finish';
    modalRef.result.then(res => {
      if (res) {
        this.getById(this.vendaCurrent.id_venda);
      }
    })
  }

  updateSale() {
    this.loading = true;
    this.service.update(this.vendaCurrent.id_venda, this.vendaCurrent).subscribe(res => {
      this.message.toastSuccess(res);
      this.getById(this.vendaCurrent.id_venda);
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
    modalRef.componentInstance.type = 'clientes';
    modalRef.result.then(res => {
      if (res) {
        this.vendaCurrent.cliente_id = res.id_cliente;
        this.vendaCurrent.cliente = res.name;

        this.updateSale();
      }
    })
  }

  openProducts() {
    const modalRef = this.modalCtrl.open(ModalProductsComponent, { size: 'xl', backdrop: 'static' });
    modalRef.componentInstance.data = this.vendaCurrent;
    modalRef.result.then(res => {
      this.getById(this.vendaCurrent.id_venda);
    })
  }

  openItem(item) {
    const modalRef = this.modalCtrl.open(ModalProductDadosComponent, { size: 'md', backdrop: 'static' });
    modalRef.componentInstance.data = {id:item, crud: 'Alterar'};
    modalRef.result.then(res => {
      this.getById(this.vendaCurrent.id_venda);
    })
  }

  openDebitar(){
    const modalRef = this.modalCtrl.open(ModalDebitarComponent, { size: 'sm', backdrop: 'static' });
    modalRef.componentInstance.data = this.vendaCurrent;
    modalRef.result.then(res => {
      this.getById(this.vendaCurrent.id_venda);
    })
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
      this.getById(this.vendaCurrent.id_venda);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }
  
  private verificaDados(res) {
    if(res.cliente == null) {
      this.vendaCurrent.cliente = 'Cliente não informado';
    }
  }
}
