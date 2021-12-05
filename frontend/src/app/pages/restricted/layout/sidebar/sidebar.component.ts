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

  constructor() { 
    super();
  }

  ngOnInit() {
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
  }

  toggleSideBar(el: HTMLElement){
    let toggle = el.classList.toggle('itemToggle');
    let classList = el.classList;
    
    if(!toggle){
      classList.add("menu-is-opening");
      classList.add("menu-open");
    } else {
      classList.remove("menu-is-opening");
      classList.remove("menu-open");
    }
  }
}
