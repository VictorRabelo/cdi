import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { CrudService } from '@app/services/crud.service';
import { MovitionService } from '@app/services/movition.service';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { SubSink } from 'subsink';

@Component({
  selector: 'app-tipos-form',
  templateUrl: './tipos-form.component.html',
  styleUrls: ['./tipos-form.component.css']
})
export class TiposFormComponent implements OnInit, OnDestroy {

  private sub = new SubSink();

  loading: boolean = false;

  @Input() data: any;
  @Input() crud: string;

  dados: any = {};
  title: string;

  constructor(
    private activeModal: NgbActiveModal,
    private service: MovitionService
  ) {}

  ngOnInit() {
    if(this.data){
      this.getById(this.data);
    }
  }

  close(params: any = undefined) {
    this.activeModal.close(params);
  }

  getById(id: any) {
    this.loading = true;
    this.sub.sink = this.service.getItemById(id).subscribe(
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
      this.update();
    } else {
      this.create();
    }

  }

  create() {
    this.loading = true;

    this.service.createItem(this.dados).subscribe(
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

  update() {
    this.loading = true;

    this.service.updateItem(this.dados.id, this.dados).subscribe(
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