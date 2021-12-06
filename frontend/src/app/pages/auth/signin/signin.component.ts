import { Component, ViewEncapsulation } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import { FormBuilder, FormGroup, NgForm, Validators } from '@angular/forms';

import { first } from 'rxjs/operators';
import { MessageService } from 'primeng/api';

import { ControllerBase } from '@app/controller/controller.base';
import { NgxIzitoastService } from 'ngx-izitoast';
import { Store } from '@ngrx/store';
import { Login } from '@app/core/actions/auth.action';
import { AuthService } from '@app/services/auth.service';
import { environment } from '@env/environment';

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
    private service: AuthService,
    public store: Store<any>
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

    let lembraLogin = localStorage.getItem('lembrarLogin');
    
    this.dados.login = lembraLogin? lembraLogin:null;
  }
  
  onSubmit(form: NgForm) {

    if (!form.valid) {
      return false;
    }

    if(this.dados.lembrarLogin) {
      localStorage.setItem('lembrarLogin', this.dados.login);
    }
    
    this.loading = true;
    
    this.service.login(this.dados.login, this.dados.password)
      .pipe(first())
      .subscribe(
        res => {
          this.store.dispatch(new Login({ token: res.token }));
          localStorage.setItem(environment.tema, res.tema);
          this.messageService.add({key: 'bc', severity:'success', summary: `Bem - Vindo ${res.name}`, detail: this.getMessage()});
          this.loading = false;
          this.loadingOk = true;

        },
        error => {
          this.messageService.add({key: 'bc', severity:'error', summary: 'Atenção', detail: error});
          this.loading = false;
          this.loadingOk = false;
        },
        () => {
          setTimeout(() => { 
            this.router.navigate(['/restricted']); 
          }, 1500);
        }
      );
  }

}
