import { Component, ViewEncapsulation } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import { FormBuilder, FormGroup, NgForm, Validators } from '@angular/forms';

import { first } from 'rxjs/operators';
import { MessageService } from 'primeng/api';

import { AuthenticationService } from '@app/services/authentication.service';
import { ControllerBase } from '@app/controller/controller.base';
import { NgxIzitoastService } from 'ngx-izitoast';

@Component({
  selector: 'app-signin',
  templateUrl: './signin.component.html',
  styleUrls: ['./signin.component.css'],
  providers: [ MessageService ],
  encapsulation: ViewEncapsulation.None
})
export class SigninComponent extends ControllerBase {

  loading: boolean  = false;
  loadingOk: boolean  = false;

  returnUrl: string;
  error = '';
  param: string;

  dados: any = {};

  constructor(
    public iziToast: NgxIzitoastService, 
    private title: Title,
    private messageService: MessageService,
    private router: Router, 
    private snap: ActivatedRoute,
    private service: AuthenticationService
  ) {
    super();


    this.snap.queryParams.subscribe(params => {
      this.param = params['error'];
    });

    if (this.currentUser) {
      this.router.navigate(['/']);
    }
  }

  ngOnInit() {
    // Seta o title da pagina
    this.title.setTitle('CDI | Login');
    
    if(this.param){
      this.iziToast.error({
        title: 'Error 401!',
        message: this.param,
        position: 'topRight'
      });
    }

  }
  
  onSubmit(form: NgForm) {

    if (!form.valid) {
      return false;
    }
    
    this.loading = true;
    
    this.service.logar(this.dados.login, this.dados.password)
      .pipe(first())
      .subscribe(
        data => {
          this.messageService.add({key: 'bc', severity:'success', summary: `Bem - Vindo ${data.name}`, detail: this.getMessage()});
          this.loading = false;
          this.loadingOk = true;
        },
        error => {
          this.messageService.add({key: 'bc', severity:'error', summary: 'Atenção', detail: error});
          this.loading = false;
        },
        () => {
          setTimeout(() => { 
            this.router.navigate(['/restricted']); 
          }, 2000);
        }
      );
  }

}
