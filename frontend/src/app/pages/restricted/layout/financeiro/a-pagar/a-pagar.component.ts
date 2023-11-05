import { Component } from '@angular/core';
import { NgForm } from '@angular/forms';

import { ControllerBase } from '@app/controller/controller.base';
import { DespesaService } from '@app/services/despesa.service';

import { MessageService } from 'primeng/api';

import { SubSink } from 'subsink';

@Component({
  selector: 'app-a-pagar',
  templateUrl: './a-pagar.component.html',
  styleUrls: ['./a-pagar.component.css'],
  providers: [ MessageService ]
})
export class APagarComponent extends ControllerBase {

  private sub = new SubSink();

  loading: Boolean = false;
  loadingCreate: Boolean = false;

  term: string;
  despesas: any[] = [];

  saldo: number = 0;
  
  constructor(
    private messageService: MessageService, 
    private despesaService: DespesaService
  ) { 
    super();
  }

  ngOnInit() {
    this.loading = true;
    this.getAll();
  }
 
  getAll(){
    this.sub.sink = this.despesaService.getAll({despesa: false}).subscribe(
      (res: any) => {
        this.loading = false;
        this.despesas = res.response;
        this.saldo = res.saldo;
        
      },error => {
        console.log(error)
        this.messageService.add({key: 'bc', severity:'error', summary: 'Erro 500', detail: error});
        this.loading = false;
      })
  }

  onSubmit(form: NgForm){
    
    this.loadingCreate = true;

    if (!form.valid) {
      this.loadingCreate = false;
      return;
    }

    this.despesaService.store(form.value).subscribe(
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
        form.reset();
      }
    )
  }

  delete(id){
    
    this.loading = true;

    this.despesaService.delete(id).subscribe(
      (res: any) => {
        this.loading = true;
        this.getAll();
      },
      error => console.log(error),
      () => {
        this.messageService.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: 'Excluido com Sucesso!'});
        this.loading = false;
      }
    );
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }

}
