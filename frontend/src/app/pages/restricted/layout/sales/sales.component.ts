import { Component, OnDestroy, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { MessageService } from '@app/services/message.service';
import { VendaService } from '@app/services/venda.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { SubSink } from 'subsink';

@Component({
  selector: 'app-sales',
  templateUrl: './sales.component.html',
  styleUrls: ['./sales.component.css']
})
export class SalesComponent implements OnInit, OnDestroy {
  private sub = new SubSink();
  public today: number = Date.now();

  dataSource: any[] = [];

  loading: boolean = false;

  filters: any = { date: '' };

  totalVendas: number = 0;
  totalMensal: number = 0;
  recebido: number = 0;
  lucro: number = 0;
  
  term: string;

  constructor(
    private router: Router,
    private service: VendaService,
    private message: MessageService,
  ) { }

  ngOnInit(): void {
    this.getStart();
  }

  getStart(){
    this.getAll();
  }

  getAll() {
    this.loading = true;

    this.sub.sink = this.service.getAll(this.filters).subscribe(res => {
  
      this.dataSource = res.vendas;
      this.totalVendas = res.totalVendas;
      this.totalMensal = res.totalMensal;
      this.recebido = res.pago;
      this.lucro = res.lucro;
      this.today = res.data;
      this.filters.date = res.mounth;

    },error =>{
      
      this.loading = false;
      this.message.toastError(error.message);
      console.log(error);

    },()=> {
      this.loading = false;
    });
  }

  filterDate(){

  }

  add() {
    this.message.swal.fire({
      title: 'Iniciar nova venda?',
      icon: 'question',
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Voltar',
      showCancelButton: true
    }).then(res => {
      if (res.isConfirmed) {
        this.createVenda();
      }
    })
  }

  createVenda() {
    this.loading = true;
    this.service.store({}).subscribe(res => {
      this.router.navigate([`/restricted/vendas/${res.id_venda}`]);
    }, error =>{
      this.loading = false;
      this.message.toastError(error.message)
      console.log(error)
    })
  }

  editVenda(id) {
    this.router.navigate([`/restricted/vendas/${id}`]);
  }

  delete(id){
    this.loading = true;
    this.service.delete(id).subscribe(res => {
      this.getAll();
    },error =>{
      this.loading = false;
      this.message.toastError(error.message)
      console.log(error)
    });
  }
  
  ngOnDestroy() {
    this.sub.unsubscribe();
  }

}
