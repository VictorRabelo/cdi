import { Component, ChangeDetectorRef, SimpleChanges  } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { FilterFormComponent } from '@app/components/filter-form/filter-form.component';

import { ModalMovitionComponent } from '@app/components/modal-movition/modal-movition.component';
import { Column } from '@app/models/Column';

import { MovitionService } from '@app/services/movition.service';

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { NgxSpinnerService } from 'ngx-spinner';
import { MessageService as Msg} from 'primeng-lts/api';
import { MessageService } from '@app/services/message.service';

import { SubSink } from 'subsink';
import { TiposFormComponent } from '@app/components/tipos-form/tipos-form.component';
import { ControllerBase } from '@app/controller/controller.base';

@Component({
  selector: 'app-caixa',
  templateUrl: './caixa.component.html',
  styleUrls: ['./caixa.component.css'],
  providers: [ Msg ]
})
export class CaixaComponent extends ControllerBase {
  private sub = new SubSink();
  public today: number = Date.now();

  dataSource: any[] = [];
  
  loading: boolean = false;

  filters: any = { date: '' };

  saldoTotal: number = 0;
  saldoMes: number = 0;
  
  type: string = null;
  columns: Column[];

  term: string;

  constructor(
    private router: Router,
    private modalCtrl: NgbModal,
    private service: MovitionService,
    private snap: ActivatedRoute,
    private message: MessageService,
    private msg: Msg,
    private spinner: NgxSpinnerService,
    private cdr: ChangeDetectorRef
  ) { super(); }

  ngOnInit(): void {
    this.snap.paramMap.subscribe(params => {
      this.type = this.router.url.split("/")[3];
      this.filters.type = this.type;
      
      this.getStart();
    });
  }

  getStart(){
    this.loading = true;
    if(this.type != 'tipos') this.getAll();
    else this.getTiposCaixas();
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

  getTiposCaixas() {
    this.columns = [
      { field: 'id', header: '#ID', id: 'id', sortIcon: true, crud: false, mask: 'none' },
      { field: 'tipo', header: 'Tipo', id: 'id', sortIcon: true, crud: false, mask: 'none' },
      { field: 'action', header: ' ', id: 'id', sortIcon: false, crud: true, mask: 'none' },
    ];

    this.sub.sink = this.service.getAllItem().subscribe(res => {
      this.dataSource = res;
      console.log(this.dataSource)
    },error =>{
    
      this.loading = false;
      this.message.toastError(error.message);
      console.log(error);

    },()=> {
      this.loading = false;
    });
  }

  getAll() {
    
    this.sub.sink = this.service.getAll(this.filters).subscribe(res => {
      this.dataSource = res.dados;
      this.saldoTotal = res.saldoTotal;
      if(this.type !== 'historico'){
        this.saldoMes = res.saldoMes;
      }

    },error =>{
      
      this.loading = false;
      this.message.toastError(error.message);
      console.log(error);

    },()=> {
      this.loading = false;
    });
  }
  
  crudInTable(res: any){
    if(res.crud == 'delete'){
      this.deleteItem(res.id);
    } else {
      this.openForm(res.crud, res.id)
    }
  }

  openForm(crud: any, item: any = undefined){
    const modalRef = this.modalCtrl.open(TiposFormComponent, { size: 'sm', backdrop: 'static' });
    modalRef.componentInstance.data = item;
    modalRef.componentInstance.crud = crud;
    modalRef.result.then(res => {
      if(res.message){
        this.msg.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: res.message});
      }
      this.getTiposCaixas();
    })
  }

  create() {
    const modalRef = this.modalCtrl.open(ModalMovitionComponent, { size: 'sm', backdrop: 'static' });
    if (this.type !== 'historico') {
      modalRef.componentInstance.type = this.type;
    }
    modalRef.result.then(res => {
      if(res){
        this.getAll();
      }
    })
  }

  deleteConfirm(item) {
    this.message.swal.fire({
      title: 'Atenção!',
      icon: 'warning',
      html: `Deseja excluir essa movimentação ?`,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Voltar',
      showCancelButton: true
    }).then(res => {
      if (res.isConfirmed) {
        this.delete(item);
      }
    })
  }

  delete(id){
    this.loading = true;
    this.spinner.show();

    this.service.delete(id).subscribe(res => {
      if (res.message) {
        this.message.toastSuccess(res.message)
      }
      this.getAll();
    },error =>{
      this.loading = false;
      this.message.toastError(error.message)
      this.spinner.hide();
      console.log(error)
    }, () => {
      this.spinner.hide();
    });
  }
  
  deleteItem(id: any){
    this.loading = true;
    this.spinner.show();

    this.service.deleteItem(id).subscribe(res => {
      if (res.message) {
        this.message.toastSuccess(res.message)
      }
      this.getTiposCaixas();
    },error =>{
      this.loading = false;
      this.spinner.hide();
      this.message.toastError(error.message)
      console.log(error)
    }, () => {
      this.spinner.hide();
    });
  }
  
  ngOnDestroy() {
    this.sub.unsubscribe();
  }

}
