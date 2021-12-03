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
      if (this.data.uuid) {
        this.getDados(this.data.uuid);
      } else {
        this.dados = this.data;
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

      if (this.data.uuid) {
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
      this.calcTotais();
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

  create() {
    let service = this.serviceSale;
    this.loading = true;

    service.createItem(this.dados).subscribe(res => {
      console.log(res);
      this.message.toastSuccess();
      this.close(res);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

  update() {
    let service = this.serviceSale;
    this.loading = true;

    service.updateItem(this.dados.uuid, this.dados).subscribe(res => {
      console.log(res);
      this.message.toastSuccess('Atualizada com sucesso!');
      this.close(res);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

  calcTotais(type = 0) {
    let subtotal = parseFloat(this.dados.valor_unitario) * parseFloat(this.dados.quantidade);
    let p_desconto = parseFloat(this.dados.p_desconto);
    let desconto = parseFloat(this.dados.desconto);
    let total = 0;

    if (type == 0) {
      p_desconto = (desconto / subtotal) * 100;
      total = subtotal - desconto;
      this.dados.p_desconto = p_desconto;
    } else {
      desconto = (p_desconto / 100) * subtotal;
      total = subtotal - desconto;
      this.dados.desconto = desconto;
    }

    this.dados.subtotal = subtotal;
    this.dados.total = total;
  }

}
