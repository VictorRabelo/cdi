import { Component, Input } from '@angular/core';
import { NgForm } from '@angular/forms';
import { ControllerBase } from '@app/controller/controller.base';
import { FormaPagamentoService } from '@app/services/forma-pagamento.service';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { SubSink } from 'subsink';

@Component({
  selector: 'app-forma-pagamento-form',
  templateUrl: './forma-pagamento-form.component.html',
  styleUrls: ['./forma-pagamento-form.component.css']
})
export class FormaPagamentoFormComponent extends ControllerBase {

  private sub = new SubSink();

  loading: boolean = false;

  @Input() data: any;
  @Input() crud: string;

  dados: any = {};
  title: string;

  constructor(
    private activeModal: NgbActiveModal,
    private service: FormaPagamentoService
  ) {
    super();
  }
    
  ngOnInit() {
    if(this.data){
      this.getById(this.data);
    }
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  getById(id) {
    this.loading = true;
    this.sub.sink = this.service.getById(id).subscribe(
      (res: any) => {
        this.loading = false;
        this.dados = res;
      },
      error => {
        console.log(error)
      });
  }

  submit(form: NgForm) {
    if (!form.valid) {
      return false;
    }
    
    if (this.dados.id) {
      this.update(this.dados.id, this.dados);
    } else {
      this.create(this.dados);
    }

  }

  create(dados: any) {
    this.loading = true;

    this.service.store(dados).subscribe(
      (res: any) => {
        res.message = "Cadastro bem sucedido!"
        this.close(res);
      },
      error => {
        this.loading = false;
        console.log(error)
      }
    )
  }

  update(id: any, dados: any) {
    this.loading = true;

    this.service.update(id, dados).subscribe(
      (res: any) => {
        res.message = "Atualização bem sucedido!"
        this.close(res);
      },
      error => {
        this.loading = false;
        console.log(error)
      }
    )
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }
}
