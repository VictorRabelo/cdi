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

  vendaCurrent: any = { vendedor: {}, itens: [], payments: [] };

  productSearch = "";

  loading: boolean = false;

  user: any = { permissions: {} };

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
      this.vendaCurrent = res;
      let descontos = parseFloat(this.vendaCurrent.desconto) + parseFloat(this.vendaCurrent.descontos)
      this.vendaCurrent.descontos = descontos;
    }, error => {

    }, () => {
      this.loading = false;
    });
  }

  finishSale() {
    const modalRef = this.modalCtrl.open(SaleFinishComponent, { size: 'md', backdrop: 'static' });
    modalRef.componentInstance.data = Object.assign({}, this.vendaCurrent);
    modalRef.result.then(res => {
      if (res) {
        this.getById(this.vendaCurrent.uuid);
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

        this.vendaCurrent.cliente_id = res.uuid;
        this.vendaCurrent.cliente = res.nome;
        this.vendaCurrent.cpf = res.cpf;

        this.updateSale();
      }
    })
  }

  updateSale() {
    this.loading = true;
    this.service.update(this.vendaCurrent.uuid, this.vendaCurrent).subscribe(res => {
      this.vendaCurrent = res.resp;
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
      this.getById(this.vendaCurrent.uuid);
    })
  }

  openItem(item) {
    const modalRef = this.modalCtrl.open(ModalProductDadosComponent, { size: 'md', backdrop: 'static' });
    modalRef.componentInstance.data = Object.assign({}, item);
    modalRef.result.then(res => {
      this.getById(this.vendaCurrent.uuid);
    })
  }

  deleteItemConfirm(item, i) {
    this.message.swal.fire({
      title: 'Atenção!',
      icon: 'warning',
      html: `Deseja remover o item: ${i + 1} ?`,
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
