import { Component } from '@angular/core';
import { NgForm } from '@angular/forms';

import { ControllerBase } from '@app/controller/controller.base';
import { CrudService } from '@app/services/crud.service';
import { EntregaDespesaService } from '@app/services/entrega-despesa.service';
import { FilterFormComponent } from '@app/components/filter-form/filter-form.component';

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

import { MessageService } from 'primeng/api';

import { SubSink } from 'subsink';

@Component({
  selector: 'app-entregas-despesas',
  templateUrl: './entregas-despesas.component.html',
  styleUrls: ['./entregas-despesas.component.css'],
  providers: [ MessageService ]
})
export class EntregasDespesasComponent extends ControllerBase {

  private sub = new SubSink();

  loading: Boolean = false;
  loadingCreate: Boolean = false;

  filters: any = { adm: true, date: '' };

  term: string;

  despesas: any[] = [];
  entregadores: any = {};
  
  saldo: number = 0;
  
  constructor(
    private modalCtrl: NgbModal,
    private messageService: MessageService, 
    private despesaService: EntregaDespesaService,
    private entregadoresService: CrudService
  ) { 
    super();
    entregadoresService.setEndPoint('users')
  }

  ngOnInit() {
    this.getStart();
  }
  
  getStart(){
    this.loading = true;
    this.getAll();
    this.getEntregadores();
  }

  
  filterDate() {
    const modalRef = this.modalCtrl.open(FilterFormComponent, { size: 'sm', backdrop: 'static' });
    modalRef.result.then(res => {
      if(res.date){
        this.filters.date = res.date;
  
        this.loading = true;
        this.getAll();
      }
    })
  }

  getEntregadores() {
    this.sub.sink = this.entregadoresService.getAll().subscribe(
      (res: any) => {
        this.loading = false;
        this.entregadores = res;
      }, error => {
        console.log(error)
        this.messageService.add({key: 'bc', severity:'error', summary: 'Erro 500', detail: error});
        this.loading = false;
      })
  }

  getAll(){
    this.sub.sink = this.despesaService.getAll(this.filters).subscribe(
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
