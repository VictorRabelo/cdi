import { Component, Input, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { MessageService } from '@app/services/message.service';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { VendaService } from 'src/app/services/venda.service';

@Component({
  selector: 'app-modal-product-dados',
  templateUrl: './modal-product-dados.component.html',
  styleUrls: ['./modal-product-dados.component.css']
})
export class ModalProductDadosComponent implements OnInit {

  dados: any = {};

  loading: boolean = false;

  @Input() data: any;

  constructor(
    private activeModel: NgbActiveModal,
    private serviceSale: VendaService,
    private message: MessageService,
  ) { }

  ngOnInit(): void {
    if (this.data) {
      if (this.data.id) {
        this.getDados(this.data.id);
      } else {
        this.dados = this.data;
        this.dados.preco_venda = this.data;
      }
    }
  }

  close(params = undefined) {
    this.activeModel.close(params);
  }

  submit(form: NgForm) {
    if (!form.valid) {
      return;
    }

    if (this.data) {

      if (this.data.id) {
        this.update();
      } else {
        this.create();
      }

    }

  }

  getDados(id) {
    this.loading = true;

    this.serviceSale.getItemById(id).subscribe(res => {
      this.dados = res;
      this.configInputs(this.dados);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

  create() {
    this.loading = true;
    
    if(!this.verifica()){
      this.loading = false;
      return;
    }

    this.dados.preco_venda = this.dados.preco;
    this.dados.lucro_venda = this.dados.preco - this.dados.valor_total;

    this.serviceSale.createItem(this.dados).subscribe(res => {
      this.message.toastSuccess();
      this.close(res);
    }, error => {
      console.log(error)
      this.loading = false;
      this.message.toastError(error.message);
    }, () => {
      this.loading = false;
    });
  }

  update() {
    this.loading = true;

    if(!this.verifica()){
      this.loading = false;
      return;
    }

    this.dados.preco_venda = this.dados.preco;
    this.dados.lucro_venda = this.dados.preco - this.dados.valor_total;

    this.serviceSale.updateItem(this.dados.id, this.dados).subscribe(res => {
      this.message.toastSuccess('Atualizada com sucesso!');
      this.close(res);
    }, error => {
      console.log(error)
      this.loading = false;
      this.message.toastError(error.message);
    }, () => {
      this.loading = false;
    });
  }

  verifica(){
    if (this.dados.preco == 0 || this.dados.preco < 0) {
      this.message.toastError('Valor abaixo do permitido!');
      return false;
    }
    if (this.dados.qtd_venda > this.dados.und || this.dados.qtd_venda == 0) {
      this.message.toastError('Quantidade não permitida!');
      return false;
    }

    return true;
  }

  configInputs(dados){
    this.dados.preco = dados.preco_venda;
    this.dados.und = dados.produto.estoque.und;
    this.dados.path = dados.produto.path;
    this.dados.valor_total = dados.produto.valor_total;
    this.dados.name = dados.produto.name;
  }
}
