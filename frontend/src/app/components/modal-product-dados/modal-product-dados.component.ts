import { Component, Input, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';

import { VendaService } from '@app/services/venda.service';
import { EntregaService } from '@app/services/entrega.service';
import { MessageService } from '@app/services/message.service';

import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

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
    private serviceEntrega: EntregaService,
    private message: MessageService,
  ) { }

  ngOnInit(): void {
    
    if (this.data) {
      if (!this.data.type) {
        if(this.data.id){
          this.getDados(this.data.id);
        }
      }
      
      if(this.data.type == 'entregas') {
        this.getDadosItemEntrega(this.data.id);
      }else {
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
      if(this.data.entrega_id || this.dados.entrega_id) {
        if (this.data.id) {
          this.updateItemEntrega();
        } else {
          this.createItemEntrega();
        }
      } 
      
      if(this.data.venda_id || this.dados.venda_id) {
        if (this.data.id) {
          this.updateItemVenda();
        } else {
          this.createItemVenda();
        }
      }
    }

  }

  getDadosItemEntrega(id) {
    this.loading = true;

    this.serviceEntrega.getItemById(id).subscribe(res => {
      this.dados = res;
      this.configInputsEntrega(this.dados);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
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

  createItemEntrega() {
    this.loading = true;
    
    if(!this.verifica()){
      this.loading = false;
      return;
    }

    this.dados.preco_venda = this.dados.preco;
    this.dados.lucro_venda = this.dados.preco - this.dados.valor_total;

    this.serviceEntrega.createItem(this.dados).subscribe(res => {
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

  updateItemEntrega() {
    this.loading = true;
  
    if(!this.verifica()){
      this.loading = false;
      return;
    }

    this.dados.preco_venda = this.dados.preco;
    this.dados.lucro_venda = this.dados.preco - this.dados.valor_total;

    this.serviceEntrega.updateItem(this.dados.id, this.dados).subscribe(res => {
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
  
  createItemVenda() {
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

  updateItemVenda() {
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

  configInputsEntrega(dados){
    this.dados.preco = dados.preco_entrega;
    this.dados.und = dados.produto.estoque.und;
    this.dados.path = dados.produto.path;
    this.dados.valor_total = dados.produto.valor_total;
    this.dados.name = dados.produto.name;
    this.dados.qtd_venda = dados.qtd_produto;
  }
}
