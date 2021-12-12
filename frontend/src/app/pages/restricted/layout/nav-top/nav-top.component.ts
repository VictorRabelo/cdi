import { Component, HostListener } from '@angular/core';
import { Router } from '@angular/router';
import { ControllerBase } from '@app/controller/controller.base';
import { Logout } from '@app/core/actions/auth.action';
import { AuthService } from '@app/services/auth.service';
import { MessageService } from '@app/services/message.service';
import { UserService } from '@app/services/user.service';
import { environment } from '@env/environment';
import { select, Store } from '@ngrx/store';
import { currentUser } from '@app/core/selectors/auth.selector';
import { NgxSpinnerService } from 'ngx-spinner';
import { animate, style, transition, trigger } from '@angular/animations';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ModalAlterPasswordComponent } from '@app/components/modal-alter-password/modal-alter-password.component';

declare let $: any;

@Component({
  selector: 'app-nav-top',
  templateUrl: './nav-top.component.html',
  styleUrls: ['./nav-top.component.css'],
  animations: [
    trigger(
      'enterAnimation', [
        transition(':enter', [
          style({opacity: 0}),
          animate('600ms', style({opacity: 1}))
        ])
      ]
    )
  ],
  providers: [ MessageService ]
})
export class NavTopComponent extends ControllerBase {

  screenHeight: any;
  screenWidth: any;
  
  navDarkMode: boolean;
  body = document.getElementsByTagName('body')[0];

  user: any = {};

  constructor(
    private router: Router, 
    private spinner: NgxSpinnerService,
    private modalCtrl: NgbModal,
    private message: MessageService,
    private service: UserService,
    public store: Store<any>
  ) { 
    super();
    this.store.pipe(select(currentUser)).subscribe(res => {
      if (res) {
        this.user = res
      }
    })
  }

  ngOnInit() {
    const tema = localStorage.getItem(environment.tema);
    
    if(tema == 'light') {
      this.body.classList.remove("dark-mode");
      this.navDarkMode = false;
    }

    if(tema == 'dark') {
      this.body.classList.add("dark-mode");
      this.navDarkMode = true;
    }

    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });

    this.onResize();
    
  }

  @HostListener('window:resize', ['$event'])
  onResize(event?) {
    this.screenHeight = window.innerHeight;
    this.screenWidth = window.innerWidth;
  }

  alterPassword(){
    const modalRef = this.modalCtrl.open(ModalAlterPasswordComponent, { size: 'sm', backdrop: 'static' });
    modalRef.result.then(res => {
      if(res){
        this.message.toastSuccess('Senha atualizada com sucesso!');
      }
    })
  }

  logout() {
    this.message.swal.fire({
      title: 'Atenção!',
      icon: 'warning',
      html: 'Deseja realmente sair ?',
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Voltar',
      showCancelButton: true
    }).then(res => {
      if (res.isConfirmed) {
        this.store.dispatch(new Logout());
      }
    });
  }

  backgroundColor(){
    if(this.navDarkMode) {
      this.body.classList.remove("dark-mode");
      this.navDarkMode = false;
    } else {
      this.body.classList.add("dark-mode");
      this.navDarkMode = true;
    }

    this.atualizaTema();
  }

  atualizaTema(){
    let tema: string;
    if (!this.user) {
      return;
    }

    if(this.navDarkMode) {
      localStorage.setItem(environment.tema, 'dark');
      tema = 'dark';
    } 
    
    if (!this.navDarkMode) {
      localStorage.setItem(environment.tema, 'light');
      tema = 'light';
    }

    let request = {
      id: this.user.id,
      tema: tema
    }

    this.service.update(request).subscribe(res => {
      this.message.toastSuccess('Tema atualizado com sucesso!')
    },error => {
      console.log(error)
    });
  }
}
