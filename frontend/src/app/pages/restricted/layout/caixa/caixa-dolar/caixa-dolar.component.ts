import { Component } from '@angular/core';
import { NgForm, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ControllerBase } from '@app/controller/controller.base';
import { MessageService } from 'primeng/api';
import { DolarService } from '@app/services/dolar.service';

import { SubSink } from 'subsink';

@Component({
  selector: 'app-caixa-dolar',
  templateUrl: './caixa-dolar.component.html',
  styleUrls: ['./caixa-dolar.component.css'],
  providers: [ MessageService ]
})
export class CaixaDolarComponent extends ControllerBase {

  private sub = new SubSink();
  
  dados: any = { montante: 0.00, valor_dolar: 0.00, valor_pago: 0.00 };
  
  createForm: FormGroup;

  loading: Boolean = false;
  loadingCreate: Boolean = false;
  loadingDelete: Boolean = false;
  
  term: string;
  dolars: any[] = [];

  saldo: number = 0;
  media: number = 0;
  valor_dolar: number = 0;

  constructor(private messageService: MessageService, private dolarService: DolarService, private formBuilder: FormBuilder) { 
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
        this.dolars = res.dolars
        this.media = res.media
        this.saldo = res.saldo
      },
      error => {
        console.log(error)
        this.messageService.add({key: 'bc', severity:'error', summary: 'Erro 500', detail: error});
        this.loading = false;
      })
  }

  get f() { return this.createForm.controls; }

  onSubmit(form: NgForm){
    
    this.loadingCreate = true;
    
    if (!form.valid) {
      return false;
    }

    this.dolarService.store(this.dados).subscribe(
      (res: any) => {
        this.loading = true;
        this.getAll();
      },
      error => {
        console.log(error)
        this.messageService.add({key: 'bc', severity:'error', summary: 'Erro 500', detail: error});
        this.loadingCreate = false;
      },
      () => {
        this.messageService.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: 'Cadastrado com Sucesso!'});
        this.loadingCreate = false;
        form.reset()
      }
    )
  }

  delete(id){
    
    this.loadingDelete = true;

    this.dolarService.delete(id).subscribe(
      (res: any) => {
        this.loading = true;
        this.getAll();
      },
      error => console.log(error),
      () => {
        this.messageService.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: 'Excluido com Sucesso!'});
        this.loadingDelete = false;
      }
    );
  }

  soma(){
    this.dados.valor_pago = this.dados.montante * this.dados.valor_dolar;
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }

}
