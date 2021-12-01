import { Component } from '@angular/core';
import { ControllerBase } from '@app/controller/controller.base';
import { FormBuilder, FormGroup, NgForm, Validators } from '@angular/forms';

import { VendaService } from '@app/services/venda.service';
import { ClienteService } from '@app/services/cliente.service';
import { MessageService } from 'primeng/api';

import { SubSink } from 'subsink';

declare let $: any;
import 'bootstrap';

@Component({
  selector: 'app-a-receber',
  templateUrl: './a-receber.component.html',
  styleUrls: ['./a-receber.component.css'],
  providers: [ MessageService ]
})
export class AReceberComponent extends ControllerBase {

  private sub = new SubSink();

  dados: any = {areceber: true, restante: 0.00, total_final: 0.00, pago: 0.00};
  cli: any = [];

  createForm: FormGroup;

  term: string;

  loading: Boolean = false;
  loadingOk: Boolean = false;
  loadingCreate: Boolean = false;
  loadingId: Boolean = false;

  contas: any[] = [];
  cont: any = {};
  number: number;

  saldoReceber: number = 0;
  saldoPago: number = 0;
  totalRestante: number = 0;

  constructor(
    private messageService: MessageService,
    private vendaService: VendaService,
    private clienteService: ClienteService,
    private formBuilder: FormBuilder
  ) { 
    super();
  }

  ngOnInit() {
    this.loading = true;
    this.getContas();
    this.getClientes();

    this.createForm = this.formBuilder.group({
      pagoCreate: ['', Validators.required]
    });
  }

  ok(id: number){
    this.getById(id);
    $("#modal-ok").modal('show');
  }
  
  create(){
    $("#modal-create").modal('show');
  }

  getContas(){
    this.sub.sink = this.vendaService.getContas().subscribe((res: any) => {
      console.log(res)
      this.contas = res.vendas;
      this.saldoReceber = res.saldo_receber;
      this.saldoPago = res.saldo_pago;
      this.totalRestante = res.total_restante;
      this.number = res.numero;
    },
    error => {
      console.log(error)
      this.messageService.add({key: 'bc', severity:'error', summary: 'Erro 500', detail: error});
      this.loading = false;
    },
    () => {this.loading = false});
  }

  getById(id: number) {

    this.loadingId = true;
    this.sub.sink = this.vendaService.getById(id).subscribe((res: any) => {
      console.log(res)
      this.cont = res.venda;
    },
    error => {
      console.log(error)
      this.messageService.add({key: 'bc', severity:'error', summary: 'Erro 500', detail: error});
      this.loadingId = false;
    },
    () => {this.loadingId = false});
  }

  getClientes(){
    this.sub.sink = this.clienteService.getAll().subscribe((res: any) => {
      this.cli = res;
    },
    error => {
      console.log(error)
      this.messageService.add({key: 'bc', severity:'error', summary: 'Erro 500', detail: error});
    });
  }

  get f() { return this.createForm.controls; }

  submit(form: NgForm){
    
    if (!form.valid) {
      return false;
    }

    this.loadingCreate = true;

    this.vendaService.updateReceber(this.dados).subscribe(
      (res: any) => {
        this.loading = true;
        this.getContas();
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
        $("#modal-create").modal('hide');
      }
    );
  }
  
  onSubmit(){
    
    this.loadingId = true;

    if (this.createForm.invalid) {
      return;
    }

    const store = {
      id: this.cont.id_venda,
      pago: this.f.pagoCreate.value
    }

    this.vendaService.updateReceber(store).subscribe(
      (res: any) => {
        this.loading = true;
        this.getContas();
      },
      error => {
        console.log(error)
        this.messageService.add({key: 'bc', severity:'error', summary: 'Erro 500', detail: error});
        this.loadingId = false;
      },
      () => {
        this.messageService.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: 'Cadastrado com Sucesso!'});
        this.loadingId = false;
        this.createForm.reset();
        $("#modal-ok").modal('hide');
      }
    )
  }

  subTotal(){
    this.dados.restante = this.dados.total_final - this.dados.pago;
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }
  
}
