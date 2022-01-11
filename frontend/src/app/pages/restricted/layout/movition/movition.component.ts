import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FilterFormComponent } from '@app/components/filter-form/filter-form.component';

import { ModalMovitionComponent } from '@app/components/modal-movition/modal-movition.component';

import { MessageService } from '@app/services/message.service';
import { MovitionService } from '@app/services/movition.service';

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { NgxSpinnerService } from 'ngx-spinner';

import { SubSink } from 'subsink';

@Component({
  selector: 'app-movition',
  templateUrl: './movition.component.html',
  styleUrls: ['./movition.component.css']
})
export class MovitionComponent implements OnInit {
  private sub = new SubSink();
  public today: number = Date.now();

  dataSource: any[] = [];
  
  loading: boolean = false;

  filters: any = { date: '' };

  saldoTotal: number = 0;
  saldoMes: number = 0;
  
  type: string;

  term: string;

  constructor(
    private router: Router,
    private modalCtrl: NgbModal,
    private service: MovitionService,
    private message: MessageService,
    private spinner: NgxSpinnerService,
  ) { }

  ngOnInit(): void {
    this.type = this.router.url.split("/")[3];
    this.filters.type = this.type
    this.getStart();
  }

  getStart(){
    this.loading = true;
    this.getAll();
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
      console.log(error)
    }, () => {
      this.spinner.hide();
    });
  }
  
  ngOnDestroy() {
    this.sub.unsubscribe();
  }

}
