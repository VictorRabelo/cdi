import { ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from 'src/app/services/message.service';
import { VendaService } from 'src/app/services/venda.service';

@Component({
  selector: 'app-sale-finish',
  templateUrl: './sale-finish.component.html',
  styleUrls: ['./sale-finish.component.css']
})
export class SaleFinishComponent implements OnInit {

  dados: any = {};

  payments: any = [];
  selectedPayments = [];
  payment = '';

  loading: boolean = false;

  @Input() data: any;

  constructor(
    private ref: ChangeDetectorRef,
    private modalCtrl: NgbModal,
    private activeModal: NgbActiveModal,
    private service: VendaService,
    private message: MessageService,
  ) { }

  ngOnInit(): void {
    this.dados.subtotal = this.data.total;
    this.calcTotais();
    if (!this.data) {
      this.close();
    }
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  finish() {

    if (!this.checkFinish()) {
      return;
    }

    this.data.payments = this.selectedPayments;

    this.loading = true;
    this.service.finishSale(this.data).subscribe(res => {
      this.close(true);
    }, error => {

    }, () => {

    });
  }

  checkFinish() {
    if (this.data.resto > 0) {
      // this.message.alertErro('Total pago não é suficiente!', 'Ops!');
      return false;
    }

    return true;
  }

  calcTotais(type = 0) {
    let subtotal = parseFloat(this.dados.subtotal);
    let p_desconto = parseFloat(this.data.p_desconto);
    let desconto = parseFloat(this.data.desconto);
    let total = this.data.total;

    if (type == 0) {
      p_desconto = (desconto / subtotal) * 100;
      total = subtotal - desconto;
      this.data.p_desconto = p_desconto;
    } else {
      desconto = (p_desconto / 100) * subtotal;
      total = subtotal - desconto;
      this.data.desconto = desconto;
    }

    // this.data.subtotal = subtotal;
    this.data.total = total.toFixed(2);
    this.data.resto = total.toFixed(2);
  }

  set_payment(payment) {
    this.selectedPayments.push(payment);
    console.log(this.selectedPayments);
    this.calcResto();
  }
  
  remove_payment(i) {
    this.selectedPayments.splice(i, 1);
    this.calcResto();
  }

  calcResto() {
    let total_pago = 0;
    for (let pay of this.selectedPayments) {
      total_pago += pay.valor_pago;
    }

    let resto = this.data.total - total_pago;

    this.data.resto = parseFloat(resto.toFixed(2));
  }

}