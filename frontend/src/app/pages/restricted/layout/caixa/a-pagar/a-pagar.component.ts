import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

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

  createForm: FormGroup;

  loading: Boolean = false;
  loadingCreate: Boolean = false;
  loadingDelete: Boolean = false;

  term: string;
  despesas: string = '';

  saldo: number = 0;
  
  constructor(
    private messageService: MessageService, 
    private despesaService: DespesaService, 
    private formBuilder: FormBuilder
  ) { 
    super();
  }

  ngOnInit() {
    this.loading = true;
    this.getAll();

    this.createForm = this.formBuilder.group({
      data: ['', Validators.required],
      valor: ['', Validators.required],
      descricao: ['', Validators.required]
    });
  }
 
  getAll(){
    this.sub.sink = this.despesaService.getAll().subscribe(
      (res: any) => {
        this.loading = false;
        this.despesas = res.despesas;
        this.saldo = res.saldo;
        
      },error => {
        console.log(error)
        this.messageService.add({key: 'bc', severity:'error', summary: 'Erro 500', detail: error});
        this.loading = false;
      })
  }
  
  get f() { return this.createForm.controls; }

  onSubmit(){
    
    this.loadingCreate = true;

    if (this.createForm.invalid) {
      return;
    }

    const store = {
      data: this.f.data.value,
      valor: this.f.valor.value,
      descricao: this.f.descricao.value
    }

    this.despesaService.store(store).subscribe(
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
        this.createForm.reset();
      }
    )
  }

  delete(id){
    
    this.loadingDelete = true;

    this.despesaService.delete(id).subscribe(
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

  ngOnDestroy() {
    this.sub.unsubscribe();
  }

}
