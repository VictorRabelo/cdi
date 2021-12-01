import { Component} from '@angular/core';
import { Title } from '@angular/platform-browser';
import { VendaService } from '@app/services/venda.service';

import { ControllerBase } from 'src/app/controller/controller.base';

import * as $ from 'jquery';


@Component({
  selector: 'app-layout',
  templateUrl: './layout.component.html',
  styleUrls: ['./layout.component.css']
})
export class LayoutComponent extends ControllerBase {

  constructor(private title: Title, private vendaService: VendaService) { 
    super();
  }

  ngOnInit() {
    
    this.title.setTitle('CDI | Dashboard');

  }


}
