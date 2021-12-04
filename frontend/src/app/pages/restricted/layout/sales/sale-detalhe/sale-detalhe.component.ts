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

  finishSale() {
    if(this.vendaCurrent.itens.length == 0){
      this.message.toastError('Está sem produtos!');
      return;
    }
    
    if(!this.vendaCurrent.cliente){
      this.message.toastError('Está faltando o cliente!');
      return;
    }

    const modalRef = this.modalCtrl.open(SaleFinishComponent, { size: 'md', backdrop: 'static' });
    modalRef.componentInstance.data = Object.assign({}, this.vendaCurrent);
    modalRef.result.then(res => {
      if (res) {
        this.getById(this.vendaCurrent.id_venda);
      }
    })
  }

  confirmeSale() {
    this.message.swal.fire({
      title: 'Atenção!',
      icon: 'warning',
      html: `Finalizar Pedido ?`,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Voltar',
      showCancelButton: true
    }).then(res => {
      if (res.isConfirmed) {
        this.loading = true;
        this.service.update(this.vendaCurrent.id, { status_id: 5, status: 'Confirmado' }).subscribe((res) => {
          this.router.navigate(['/sales']);
        }, error => {
          console.log(error)
          this.message.toastError(error.message);
          this.loading = false;
        }, () => {
          this.loading = false;
        });
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
    modalRef.componentInstance.data = Object.assign({}, item);
    modalRef.result.then(res => {
      this.getById(this.vendaCurrent.id_venda);
    })
  }

  deleteItemConfirm(item) {
    this.message.swal.fire({
      title: 'Atenção!',
      icon: 'warning',
      html: `Deseja remover o item: ${item.name} ?`,
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
    this.service.deleteItem(item.uuid).subscribe(res => {
      this.getById(this.vendaCurrent.uuid);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }


  cancelConfirm() {
    this.message.swal.fire({
      title: 'Atenção!',
      icon: 'warning',
      html: `Deseja cancelar a venda ?`,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Voltar',
      showCancelButton: true
    }).then(res => {
      if (res.isConfirmed) {
        this.cancel();
      }
    })
  }

  cancel() {
    this.loading = true;

    this.service.update(this.vendaCurrent.uuid, { status_id: 0, status: 'Cancelada' }).subscribe((res) => {
      this.router.navigate(['/sales']);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

}
