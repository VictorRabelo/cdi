import { Component, ViewEncapsulation } from '@angular/core';
import { ControllerBase } from '@app/controller/controller.base';
import { MovitionService } from '@app/services/movition.service';

declare let $: any;

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class SidebarComponent extends ControllerBase {
  tiposCaixas: any[] = [];

  constructor(
    private service: MovitionService
  ) { 
    super();
  }

  ngOnInit() {
    this.getTiposCaixas();
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

  closeSide(){
    const mobile: number = window.innerWidth;

    if(mobile > 420) {
      return;
    }

    document.body.classList.remove('sidebar-open');
    document.body.classList.add('sidebar-closed');
    document.body.classList.add('sidebar-collapse');
  }

  getTiposCaixas() {
    this.service.getAllItem().subscribe(res => {
      this.tiposCaixas = res;
    },error =>console.log(error));
  }
}
