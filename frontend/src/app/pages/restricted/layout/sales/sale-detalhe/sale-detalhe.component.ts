import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ProdutoService } from '@app/services/produto.service';
import { VendaService } from '@app/services/venda.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from '@app/services/message.service';
import { SaleFinishComponent } from './sale-finish/sale-finish.component';
import { ModalProductsComponent } from '@app/components/modal-products/modal-products.component';
import { ModalProductDadosComponent } from '@app/components/modal-product-dados/modal-product-dados.component';
import { ModalPessoalComponent } from '@app/components/modal-pessoal/modal-pessoal.component';

@Component({
  selector: 'app-sale-detalhe',
  templateUrl: './sale-detalhe.component.html',
  styleUrls: ['./sale-detalhe.component.css']
})
export class SaleDetalheComponent implements OnInit {

  vendaCurrent: any = { cliente: 'Selecione um cliente', itens: [] };

  loading: boolean = false;

  constructor(
    private modalCtrl: NgbModal,
    private activeRoute: ActivatedRoute,
    private router: Router,
    private productService: ProdutoService,
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

  calcRestante() {
    const debitar = (this.vendaCurrent.debitar)?this.vendaCurrent.debitar:0.00;
    this.vendaCurrent.restante -= debitar;
    this.vendaCurrent.pago += debitar;
  }

  configCalc(){
    const debitar = (this.vendaCurrent.debitar)?this.vendaCurrent.debitar:0.00;
    this.vendaCurrent.restante += debitar;
    this.vendaCurrent.pago -= debitar;
  }

  debitar(){
    this.loading = true;

    if(this.vendaCurrent.restante == 0){
      this.vendaCurrent.status = 'pago';
    }

    const dados = {
      debitar: true,
      creditar: this.vendaCurrent.debitar,
      restante: this.vendaCurrent.restante,
      pago: this.vendaCurrent.pago,
      cliente: this.vendaCurrent.cliente,
      caixa: this.vendaCurrent.caixa,
    }

    this.service.update(this.vendaCurrent.id_venda, this.vendaCurrent).subscribe(res => {
      this.getById(this.vendaCurrent.id_venda);
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
      this.getById(this.vendaCurrent.id_venda);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

}
