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

  dataSource: any[] = [];

  filters = {status: 'ok'}
  loading: boolean = false;

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
      this.dataSource = res;
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

  openProduct(produto) {
    const modalRef = this.modalCtrl.open(ModalProductDadosComponent, { size: 'md', backdrop: 'static' });
    modalRef.componentInstance.data = produto;
    
    if (this.data) {
      
      if(this.data.id_venda){
        produto.venda_id = this.data.id_venda;
      }

      if(this.data.id_entrega){
        produto.entrega_id = this.data.id_entrega;
      }
      
      modalRef.componentInstance.data = produto;
    }
  }

}
