import { Component } from '@angular/core';
import { NgForm } from '@angular/forms';
import { ControllerBase } from '@app/controller/controller.base';
import { DolarService } from '@app/services/dolar.service';
import { MessageService } from '@app/services/message.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { NgxSpinnerService } from 'ngx-spinner';

import { SubSink } from 'subsink';

@Component({
  selector: 'app-caixa-dolar',
  templateUrl: './caixa-dolar.component.html',
  styleUrls: ['./caixa-dolar.component.css'],
  providers: [ MessageService ]
})
export class CaixaDolarComponent extends ControllerBase {

  private sub = new SubSink();
  
  dados: any[] = [];
  
  loading: Boolean = false;
  
  term: string;

  saldo: number = 0;
  media: number = 0;
  valor_dolar: number = 0;

  constructor(
    private modalCtrl: NgbModal,
    private spinner: NgxSpinnerService,
    private messageService: MessageService, 
    private dolarService: DolarService
  ) { 
    super();
  }

  ngOnInit() {
    this.loading = true;
    this.getAll();
  }

  getAll(){
    this.sub.sink = this.dolarService.getAll().subscribe(
      (res: any) => {
        this.loading = false;
        this.dados = res.dados
        this.media = res.media
        this.saldo = res.saldo
      },
      error => {
        console.log(error)
        this.messageService.toastError();
        this.loading = false;
      })
  }

  create() {
    
  }

  delete(id){
    
    this.loading = true;
    this.spinner.show();

    this.dolarService.delete(id).subscribe(
      (res: any) => {
        this.spinner.hide();
        this.loading = true;
        this.getAll();
      },
      error => console.log(error),
      () => {
        this.messageService.toastSuccess('Excluido com Sucesso!');
        this.loading = false;
      }
    );
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }

}
