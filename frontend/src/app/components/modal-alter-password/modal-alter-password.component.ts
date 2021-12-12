import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { SubSink } from 'subsink';
import { AuthService } from '@app/services/auth.service';
import { MessageService } from '@app/services/message.service';
import { NgxSpinnerService } from 'ngx-spinner';

@Component({
  selector: 'app-modal-alter-password',
  templateUrl: './modal-alter-password.component.html',
  styleUrls: ['./modal-alter-password.component.css']
})
export class ModalAlterPasswordComponent implements OnInit, OnDestroy {

  private sub = new SubSink();

  show: boolean = false;

  type: string = 'password';

  dados: any = {};

  constructor(
    private activeModal: NgbActiveModal,
    private service: AuthService,
    private message: MessageService,
    private spinner: NgxSpinnerService
  ) {}

  ngOnInit() {}

  close(params = undefined) {
    this.activeModal.close(params);
  }
  
  submit(form: NgForm) {
    if (!form.valid) {
      return false;
    }
    
    this.spinner.show();

    if(this.dados.password !== this.dados.confirm_password) {
      this.message.toastError('As senhas não são iguais!');
      this.spinner.hide();
      return false;
    }

    this.service.alterSenha(form.value).subscribe(res => {
      this.spinner.hide();
    },
    error => console.log(error),
    ()=>{
      this.close(true);
    })
  }

  changePassword(){
    if(this.show){
      this.type = 'text';
    } else {
      this.type = 'password'
    }
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }
}
