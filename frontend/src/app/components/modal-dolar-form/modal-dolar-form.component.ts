import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { SubSink } from 'subsink';
import { MessageService } from '@app/services/message.service';
import { DolarService } from '@app/services/dolar.service';
import { animate, style, transition, trigger } from '@angular/animations';

@Component({
  selector: 'app-modal-dolar-form',
  templateUrl: './modal-dolar-form.component.html',
  styleUrls: ['./modal-dolar-form.component.css'],
  animations: [
    trigger(
      'enterAnimation', [
        transition(':enter', [
          style({opacity: 0}),
          animate('500ms', style({opacity: 1}))
        ]),
        transition(':leave', [
          style({opacity: 1}),
          animate('500ms', style({opacity: 0}))
        ])
      ]
    )
  ],
})
export class ModalDolarFormComponent implements OnInit, OnDestroy {

  private sub = new SubSink();

  loading: boolean = false;

  @Input() data: any;
  @Input() crud: string;

  dados: any = {};
  title: string;

  constructor(
    private activeModal: NgbActiveModal,
    private service: DolarService,
    private message: MessageService,
  ) {}

  ngOnInit() {
    if(this.data){
      this.dados = this.data;
    }
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }
  
  submit(form: NgForm) {
    if (!form.valid) {
      return false;
    }
    
    this.loading = true;
    
    if(this.data){
      this.update(this.dados);
    } else {
      this.store(this.dados);
    }

  }

  store(dados){
    this.service.store(dados).subscribe(res => {
      this.close(true);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }
  
  update(dados){
    this.service.update(dados.id_dolar, dados).subscribe(res => {
      this.close(true);
    }, error => {
      console.log(error)
      this.message.toastError(error.message);
      this.loading = false;
    }, () => {
      this.loading = false;
    });
  }

  calcRestante() {
    const montante = (this.dados.montante)?this.dados.montante:0.00;
    const valorDolar = (this.dados.valor_dolar)?this.dados.valor_dolar:0.00;

    this.dados.valor_pago = montante * valorDolar;
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }
}
