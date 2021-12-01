import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { DomSanitizer } from '@angular/platform-browser';
import { ControllerBase } from '@app/controller/controller.base';

import { MovitionService } from '@app/services/movition.service';

import { MessageService } from 'primeng/api';

import { SubSink } from 'subsink';

@Component({
  selector: 'app-move-all',
  templateUrl: './move-all.component.html',
  styleUrls: ['./move-all.component.css'],
  providers: [ MessageService ]
})
export class MoveAllComponent extends ControllerBase {
  
  private sub = new SubSink();

  createForm: FormGroup;
  
  loading: Boolean = false;
  loadingCreate: Boolean = false;
  loadingDelete: Boolean = false;

  term: string;

  historico: any[];

  saldo: number = 0;

  constructor(private messageService: MessageService, private formBuilder: FormBuilder, private movitionService: MovitionService, private sanitizer: DomSanitizer) { 
    super();
  }

  ngOnInit() {
    this.loading = true;

    this.getAll();

    this.createForm = this.formBuilder.group({
      valor: ['', Validators.required],
      descricao: ['', Validators.required],
      tipo: ['', Validators.required],
      status: ['', Validators.required]
    });
  }

  getAll(){
    this.sub.sink = this.movitionService.getAll().subscribe(
      (res: any) => {
        console.log(res)
        this.loading = false;
        this.historico = res.movition;
        this.saldo = res.saldo;
      },
      error => {
        this.loading = false;
        console.log(error)
      })
  }

  get f() { return this.createForm.controls; }

  onSubmit(){
    
    this.loadingCreate = true;

    if (this.createForm.invalid) {
      return;
    }

    const store = {
      valor: this.f.valor.value,
      descricao: this.f.descricao.value,
      tipo: this.f.tipo.value,
      status_movition: this.f.status.value
    }

    this.movitionService.store(store).subscribe(
      (res: any) => {
        this.loading = true;
        this.getAll();
      },
      error => {
        console.log(error)
        this.loading = false;
      },
      () => {
        this.messageService.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: 'Cadastrado com Sucesso!'});
        this.loadingCreate = false;
        this.createForm.reset()
      }
    )
  }
  
  delete(id: number){
    
    this.loadingDelete = true;

    this.movitionService.delete(id).subscribe(
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

  ngOnDestroy(){
    this.sub.unsubscribe();
  }

}