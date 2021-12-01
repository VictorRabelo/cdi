import { Component } from '@angular/core';
import { NgForm } from '@angular/forms';
import { UserService } from '@app/services/user.service';
import { MessageService } from 'primeng/api';

@Component({
  selector: 'app-perfil',
  templateUrl: './perfil.component.html',
  styleUrls: ['./perfil.component.css'],
  providers: [ MessageService ]
})
export class PerfilComponent {
  
  loading: Boolean = false;
  dados: any = {};

  constructor(
    private userService: UserService,
    private messageService: MessageService
  ) { }

  submit(form: NgForm) {

    if (!form.valid) {
      return false;
    }

    if(this.dados.password !== this.dados.confirm_password) {
      return false;
    }
    this.loading = true;
    this.userService.alterSenha(form.value).subscribe(res => {
      this.messageService.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: 'Senha atualizcada com Sucesso!'});
    },
    error => console.log(error),
    ()=>{
      this.loading = false;
    })

  }

}
