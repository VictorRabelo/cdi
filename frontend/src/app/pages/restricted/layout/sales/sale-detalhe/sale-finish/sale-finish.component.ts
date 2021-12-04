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

  dados: any = { pagamento: '', status_movition: '', status: '' };

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

    this.loading = true;
    this.service.finishSale(this.data).subscribe(res => {
      this.close(true);
    }, error => {

    }, () => {

    });
  }

  checkFinish() {
    if (this.data.restante > 0) {
      // // this.message.alertErro('Total pago não é suficiente!', 'Ops!');
      return false;
    }

    return true;
  }

  calcRestante() {
    this.dados.restante = this.dados.total_final - this.dados.pago;
  }

}