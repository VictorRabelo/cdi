import { Component } from '@angular/core';

import { ControllerBase } from '@app/controller/controller.base';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MessageService } from 'primeng/api';

import { SubSink } from 'subsink';
import { ClienteFormComponent } from '@app/components/cliente-form/cliente-form.component';
import { UserService } from '@app/services/user.service';
import { Column } from '@app/models/Column';

@Component({
  selector: 'app-users',
  templateUrl: './users.component.html',
  styleUrls: ['./users.component.css'],
  providers: [ MessageService ]
})
export class UsersComponent extends ControllerBase {
  
  private sub = new SubSink();

  isLoading: boolean = false;

  dados: any = [];

  columns: Column[];

  title: string = 'users';
  term: string;

  constructor(
    private service: UserService, 
    private messageService: MessageService,
    private modalCtrl: NgbModal,
  ) {
    super();
  }

  ngOnInit() {
    this.columns = [
      { field: 'id', header: '#ID', id: 'id', sortIcon: true, crud: false, mask: 'none' },
      { field: 'name', header: 'Nome', id: 'id', sortIcon: true, crud: false, mask: 'none' },
      { field: 'email', header: 'Email', id: 'id', sortIcon: true, crud: false, mask: 'none' },
      { field: 'login', header: 'Login', id: 'id', sortIcon: true, crud: false, mask: 'none' },
      { field: 'action', header: ' ', id: 'id', sortIcon: false, crud: true, mask: 'none' },
    ];

    this.getAll();
  }

  openForm(crud: any, item: any = undefined){
    const modalRef = this.modalCtrl.open(ClienteFormComponent, { size: 'sm', backdrop: 'static' });
    modalRef.componentInstance.data = item;
    modalRef.componentInstance.crud = crud;
    modalRef.componentInstance.module = this.title;
    modalRef.result.then(res => {
      if(res.message){
        this.messageService.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: res.message});
      }
      this.getAll();
    })
  }

  getAll(){
    this.isLoading = true;
    this.sub.sink = this.service.getAll().subscribe(
      (res: any) => {
        this.isLoading = false;
        this.dados = res;
      },error => console.log(error))
  }

  crudInTable(res: any){
    if(res.crud == 'delete'){
      this.delete(res.id)
    } else {
      this.openForm(res.crud, res.id)
    }
  }

  delete(id: any){
    this.isLoading = true;

    this.service.delete(id).subscribe(
      (res: any) => {
        this.getAll();
      },
      error => console.log(error),
      () => {
        this.messageService.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: 'Deletado!'});
        this.isLoading = false;
      }
    );
  }

  ngOnDestroy(){
    this.sub.unsubscribe();
  }
}