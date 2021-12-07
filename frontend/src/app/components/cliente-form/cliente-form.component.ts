import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { CrudService } from '@app/services/crud.service';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { SubSink } from 'subsink';

@Component({
  selector: 'app-cliente-form',
  templateUrl: './cliente-form.component.html',
  styleUrls: ['./cliente-form.component.css'],
  providers: [
    CrudService
  ]
})
export class ClienteFormComponent implements OnInit, OnDestroy {

  private sub = new SubSink();

  loading: boolean = false;

  @Input() data: any;
  @Input() crud: string;
  @Input() module: string;

  dados: any = {};
  title: string;

  constructor(
    private activeModal: NgbActiveModal,
    private service: CrudService
  ) {}

  ngOnInit() {
    if (this.module) {
      this.service.setEndPoint(this.module);
      
      if(this.module == 'clientes'){
        this.title = 'cliente';
        if(this.data){
          this.getById(this.data);
        }
      }
      
      if(this.module == 'fornecedores'){
        this.title = 'fornecedor';
        if(this.data){
          this.getById(this.data);
        }
      }
      
      if(this.module == 'categorias'){
        this.title = 'categoria';
        if(this.data){
          this.getById(this.data);
        }
      }
      
      if(this.module == 'users'){
        this.title = 'Usuário';
        if(this.data){
          this.getById(this.data);
        }
      }
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
        
        if(this.module == 'fornecedores'){
          this.dados.name = res.fornecedor;
          this.dados.id = res.id_fornecedor;
        }
        
        if(this.module == 'clientes'){
          this.dados.id = res.id_cliente;
        }
        
        if(this.module == 'users'){
          this.dados.role = res.role.role;
        }
        
        if(this.module == 'categorias'){
          this.dados.id = res.id_categoria;
          this.dados.name = res.categoria;
        }
        
      },
      error => {
        console.log(error)
      });
  }

  submit(form: NgForm) {
    if (!form.valid) {
      return false;
    }
    
    if(this.module == 'fornecedores'){
      this.dados.fornecedor = this.dados.name;
    }
    
    if(this.module == 'categorias'){
      this.dados.categoria = this.dados.name;
    }
    
    if (this.dados.id) {
      this.update();
    } else {
      this.create();
    }

  }

  create() {
    this.loading = true;

    this.service.store(this.dados).subscribe(
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

    this.service.update(this.dados).subscribe(
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
