import { Component} from '@angular/core';
import { Title } from '@angular/platform-browser';
import { RouterOutlet } from '@angular/router';

import { ControllerBase } from 'src/app/controller/controller.base';
import { slideInLayoutAnimation } from '@app/animations';

import * as $ from 'jquery';


@Component({
  selector: 'app-layout',
  templateUrl: './layout.component.html',
  styleUrls: ['./layout.component.css'],
  animations: [
    slideInLayoutAnimation
  ]
})
export class LayoutComponent extends ControllerBase {

  constructor(private title: Title) { 
    super();
  }

  ngOnInit() {
    
    this.title.setTitle('CDI | Dashboard');

  }

  prepareRoute(outlet: RouterOutlet) {
    return outlet.activatedRouteData.animation;
  }

}
