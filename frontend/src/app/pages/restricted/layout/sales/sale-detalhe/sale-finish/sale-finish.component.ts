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

  loading: boolean = false;
  validVenda: boolean = true;

  @Input() data: any;
  @Input() type: any;

  constructor(
    private ref: ChangeDetectorRef,
    private activeModal: NgbActiveModal,
    private service: VendaService,
    private message: MessageService,
  ) { }

  ngOnInit(): void {
    
    if (!this.data) {
      this.close();
    }
    
    this.dados = this.data;

  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  finish() {

    if (!this.checkFinish()) {
      return;
    }

    this.loading = true;
    this.service.finishSale(this.data).subscribe(res => {
      this.close(true);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = true;
    });
  }

  checkFinish() {
    if (!this.dados.caixa) {
      this.message.toastWarning('Tipo de caixa não selecionado!');
      return false;
    }
    if (!this.dados.status) {
      this.message.toastWarning('Status da venda não selecionado!');
      return false;
    }
    if (!this.dados.pagamento) {
      this.message.toastWarning('Forma de pagamento não selecionado!');
      return false;
    }

    return true;
  }

  calcRestante() {
    this.dados.restante -= this.dados.debitar;
    this.dados.pago += this.dados.debitar;
  }

  configCalc(){
    this.dados.restante += this.dados.debitar;
    this.dados.pago -= this.dados.debitar;
  }

}