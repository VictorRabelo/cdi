import { Component, OnInit, HostListener } from '@angular/core';
import { Router } from '@angular/router';
import { AuthenticationService } from '@app/services/authentication.service';

declare let $: any;

@Component({
  selector: 'app-nav-top',
  templateUrl: './nav-top.component.html',
  styleUrls: ['./nav-top.component.css']
})
export class NavTopComponent implements OnInit {

  screenHeight: any
  screenWidth: any
  background: any;

  constructor(private router: Router, private authenticationService: AuthenticationService) { }

  ngOnInit() {
    let body = document.getElementsByTagName('body')[0];
    this.background = JSON.parse(localStorage.getItem('background'));
    
    if(!this.background) {
      this.background = {dark: true, color: 'black', class: 'dark-mode'}
      localStorage.setItem('background', JSON.stringify(this.background));
    }

    if(this.background.class == null) {
      body.classList.remove("dark-mode");
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

  logout() {
    this.authenticationService.logout().subscribe(
      (res: any) => {
        localStorage.removeItem('currentUser')
        this.router.navigate(['/signin']);
      }
    );
  }

  backgroundColor(){
    let body = document.getElementsByTagName('body')[0];
    
    if(this.background.dark) {
      this.background.color = 'white';
      this.background.dark = false;
      this.background.class = null;
      body.classList.remove("dark-mode");
      localStorage.setItem('background', JSON.stringify(this.background));
    } else {
      this.background.color = 'black';
      this.background.dark = true;
      this.background.class = 'dark-mode';
      body.classList.add("dark-mode");
      localStorage.setItem('background', JSON.stringify(this.background));
    }
  }
}
