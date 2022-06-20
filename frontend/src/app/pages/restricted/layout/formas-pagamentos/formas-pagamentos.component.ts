import { Component } from '@angular/core';
import { FormaPagamentoFormComponent } from '@app/components/forma-pagamento-form/forma-pagamento-form.component';
import { ControllerBase } from '@app/controller/controller.base';
import { FormaPagamentoService } from '@app/services/forma-pagamento.service';
import { MessageService } from '@app/services/message.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { SubSink } from 'subsink';

@Component({
  selector: 'app-formas-pagamentos',
  templateUrl: './formas-pagamentos.component.html',
  styleUrls: ['./formas-pagamentos.component.css']
})
export class FormasPagamentosComponent extends ControllerBase {

  private sub = new SubSink();

  loading: boolean = false;

  dataSource: any = [];

  term: string;

  constructor(
    private formaPagamentoService: FormaPagamentoService, 
    private messageService: MessageService,
    private modalCtrl: NgbModal,
  ) {
    super();
  }

  ngOnInit() {
    this.getAll();
  }

  openForm(crud, item = undefined){
    const modalRef = this.modalCtrl.open(FormaPagamentoFormComponent, { size: 'sm', backdrop: 'static' });
    modalRef.componentInstance.data = item;
    modalRef.componentInstance.crud = crud;
    modalRef.result.then(res => {
      if(res.message){
        this.messageService.toastSuccess(res.message);
      }
      this.getAll();
    })
  }

  getAll(){
    this.loading = true;
    this.sub.sink = this.formaPagamentoService.getAll().subscribe(
      (res: any) => {
        this.loading = false;
        this.dataSource = res;
      },error => console.log(error))
  }

  delete(id){
    
    this.loading = true;

    this.formaPagamentoService.delete(id).subscribe(
      (res: any) => {
        this.getAll();
      },
      error => console.log(error),
      () => {
        this.messageService.toastSuccess('Excluido com Sucesso!');
        this.loading = false;
      }
    );
  }

  ngOnDestroy(){
    this.sub.unsubscribe();
  }
}
