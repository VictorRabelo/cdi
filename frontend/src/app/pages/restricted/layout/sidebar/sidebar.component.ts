import { Component, ViewEncapsulation } from '@angular/core';
import { ControllerBase } from '@app/controller/controller.base';

declare let $: any;

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class SidebarComponent extends ControllerBase {

  caixa: Boolean = false;
  cota: Boolean = false;
  move: Boolean = false;
  
  constructor() { 
    super();
  }

  ngOnInit() {
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
  }

  toggleCota(cot: HTMLElement, el: HTMLElement){
    if(this.cota){
      // el.style.display = 'none';
      cot.classList.remove("menu-is-opening");
      cot.classList.remove("menu-open");
      this.cota = false;
    } else {
      // el.style.display = 'block';
      cot.classList.add("menu-is-opening");
      cot.classList.add("menu-open");
      this.cota = true;
    }
  }
  
  toggleCaixa(cax: HTMLElement, el: HTMLElement){
    if(this.caixa){
      // el.style.display = 'none';
      cax.classList.remove("menu-is-opening")
      cax.classList.remove("menu-open")
      this.caixa = false;
    } else {
      // el.style.display = 'block';
      cax.classList.add("menu-is-opening")
      cax.classList.add("menu-open")
      this.caixa = true;
    }
  }
  
  toggleMove(mov: HTMLElement, el: HTMLElement){
    if(this.move){
      mov.classList.remove("menu-is-opening");
      mov.classList.remove("menu-open");
      this.move = false;
    } else {
      mov.classList.add("menu-is-opening");
      mov.classList.add("menu-open");
      this.move = true;
    }
  }
}
