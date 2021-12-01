import { Component, ViewEncapsulation } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

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

  loginForm: FormGroup;
  loading   = false;
  submitted = false;
  returnUrl: string;
  error = '';
  param: string;

  constructor(
    public iziToast: NgxIzitoastService, 
    private title: Title, 
    private formBuilder: FormBuilder, 
    private messageService: MessageService,
    private router: Router, 
    private snap: ActivatedRoute
  ) {
    super();


    this.snap.queryParams.subscribe(params => {
      this.param = params['error'];
    });

    if (this.authenticationService.currentUserValue) {
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

    this.loginForm = this.formBuilder.group({
      login: ['', Validators.required],
      password: ['', Validators.required]
    });
  }

  get f() { return this.loginForm.controls; }
  
  onSubmit() {
    
    this.submitted = true;
    
    if (this.loginForm.invalid) {
      return;
    }
    
    this.loading = true;
    
    this.authenticationService.login(this.f.login.value, this.f.password.value)
      .pipe(first())
      .subscribe(
        data => {
          this.messageService.add({key: 'bc', severity:'success', summary: `Bem - Vindo ${data.name}`, detail: this.getMessage()});
          this.loading = false;
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

  getMessage(){
        
    let d = new Date();
    let hour = d.getHours();
    
    if(hour < 5) {
        return "Uma Ótima Madrugada";
    }
    
    if(hour < 8) {
        return "Uma Ótimo Dia";
    }

    
    if(hour < 12) {
        return "Uma Ótimo Dia";
    }

    if(hour < 18) {
        return "Uma Ótima Tarde";
    } else {
        return "Uma Ótima Noite";
    }
  }
}
