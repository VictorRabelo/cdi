import { Component, Input, OnInit } from '@angular/core';
import { EstoqueService } from '@app/services/estoque.service';
import { MessageService } from '@app/services/message.service';
import { ProdutoService } from '@app/services/produto.service';
import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ModalProductDadosComponent } from '../modal-product-dados/modal-product-dados.component';

@Component({
  selector: 'app-modal-products',
  templateUrl: './modal-products.component.html',
  styleUrls: ['./modal-products.component.css']
})
export class ModalProductsComponent implements OnInit {

  dataSource: any = {};

  loading: boolean = false;

  filters: any = { page: 1, per_page: 50, termo: '' };

  @Input() data: any;

  term: string;

  constructor(
    private modalCtrl: NgbModal,
    private activeModal: NgbActiveModal,
    private service: EstoqueService,
    private message: MessageService,
  ) { }

  ngOnInit(): void {
    this.listing();

  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  listing() {
    this.loading = true;
    this.service.getAll(this.filters).subscribe(res => {

      if (res.paginate) {
        this.dataSource = res.paginate.data;
      }

    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

  openProduct(produto) {
    console.log(produto)
    let item: any = {
      'produto': produto.nome,
      'quantidade': 1,
      'valor_custo': produto.valor_custo,
      'valor_unitario': produto.valor_venda,
      'p_desconto': 0,
      'desconto': 0,
      'total': produto.valor_venda,
      'produto_id': produto.uuid,
      'foto': produto.foto_capa.foto,
    }

    const modalRef = this.modalCtrl.open(ModalProductDadosComponent, { size: 'md', backdrop: 'static' });
    console.log('data', this.data);
    if (this.data) {
      item.venda_id = this.data.uuid;
      modalRef.componentInstance.data = item;
    }
  }

}
