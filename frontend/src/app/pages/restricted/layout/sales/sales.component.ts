import { Component, OnDestroy, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FilterFormComponent } from '@app/components/filter-form/filter-form.component';
import { MessageService } from '@app/services/message.service';
import { VendaService } from '@app/services/venda.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { NgxSpinnerService } from 'ngx-spinner';
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
    private modalCtrl: NgbModal,
    private message: MessageService,
    private spinner: NgxSpinnerService,
  ) { }

  ngOnInit(): void {
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
      this.dataSource = res.vendas;
      this.totalVendas = res.totalVendas;
      this.totalMensal = res.totalMensal;
      this.recebido = res.pago;
      this.lucro = res.lucro;
      this.today = res.data;

    },error =>{
      
      this.loading = false;
      this.message.toastError(error.message);
      console.log(error);

    },()=> {
      this.loading = false;
    });
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
      if(res.message) {
        this.message.toastError(res.message);
        this.loading = false;
        return false;
      };
      
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

  deleteConfirm(id) {
    this.message.swal.fire({
      title: 'Atenção!',
      icon: 'warning',
      input: 'checkbox',
      inputValue: 0,
      inputPlaceholder: 'Voltar produtos para estoque ?',
      html: `Deseja excluir essa venda ? `,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Voltar',
      showCancelButton: true
    }).then(res => {
      if (res.isConfirmed) {
        this.delete(id, { extornarProduto: res.value });
      }
    })
  }

  delete(id, queryParams?: any) {
    this.loading = true;
    this.spinner.show();

    this.service.delete(id, queryParams).subscribe(res => {
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
