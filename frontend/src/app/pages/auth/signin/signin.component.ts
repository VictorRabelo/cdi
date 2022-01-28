import { Component, ViewEncapsulation } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import { NgForm } from '@angular/forms';

import { first } from 'rxjs/operators';
import { Store } from '@ngrx/store';
import { Login } from '@app/core/actions/auth.action';

import { ControllerBase } from '@app/controller/controller.base';
import { MessageService } from '@app/services/message.service';
import { AuthService } from '@app/services/auth.service';
import { environment } from '@env/environment';
import { enterAnimationIcon } from '@app/animations';

@Component({
  selector: 'app-signin',
  templateUrl: './signin.component.html',
  styleUrls: ['./signin.component.css'],
  animations: [
    enterAnimationIcon
  ],
  encapsulation: ViewEncapsulation.None
})
export class SigninComponent extends ControllerBase {

  loading: boolean  = false;
  loadingOk: boolean  = false;
  loadingError: boolean  = false;

  show: boolean = false;
  type: string = 'password';

  returnUrl: string;
  error = '';
  param: string;

  dados: any = {};

  constructor(
    private title: Title,
    private message: MessageService,
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
      this.message.toastError(this.param, 'Error 401!');
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
  
    if(this.loadingError) {
      this.loadingError = false;
    }

    this.loading = true;
    
    this.service.login(this.dados.login, this.dados.password).pipe(first())
      .subscribe(
        (res) => {
          if(res == undefined){
            return this.errorLogin();
          }

          this.store.dispatch(new Login({ token: res.token }));
          localStorage.setItem(environment.tema, res.tema);
          
          const welcome: string = `Bem - Vindo ${res.name},`;
          const message: string = this.getMessage();
          this.message.toastSuccess(message, welcome);

          this.loading = false;
          this.loadingOk = true;

          setTimeout(() => { 
            this.router.navigate(['/restricted']); 
          }, 1500);
        },
        error => {
          this.errorLogin();
        }
      );
  }

  public changePassword(){
    if(!this.show){
      this.type = 'text';
      this.show = true;
    } else {
      this.show = false;
      this.type = 'password'
    }
  }
  
  public errorLogin() {
    this.loading = false;
    this.loadingOk = false;
    this.loadingError = true;

    return this.message.toastError('Falha no login');
    
  }
}
