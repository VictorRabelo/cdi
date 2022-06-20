import { Component } from '@angular/core';
import { ControllerBase } from './controller/controller.base';

import { NgxSpinnerService } from 'ngx-spinner';
import { HTTPStatus } from './helpers/httpstatus';
import { RouterOutlet } from '@angular/router';
import { slideInAppAnimation } from './animations';
import { PrimeNGConfig } from 'primeng-lts/api';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
  animations: [
    slideInAppAnimation
  ],
  providers: [
    PrimeNGConfig
  ]
})
export class AppComponent extends ControllerBase {
  
  title = 'Controle de Estoque';
  
  constructor(private spinner: NgxSpinnerService, private primengConfig: PrimeNGConfig) {
    super();
    // this.httpStatus.getHttpStatus().subscribe((status: boolean) => {
    //   if(status) {
    //     this.spinner.show();
    //   }
    //   else {
    //     this.spinner.hide();
    //   }
    // });
  }

  ngOnInit() {
    this.primengConfig.ripple = true;
  }

  prepareRoute(outlet: RouterOutlet) {
    return outlet.activatedRouteData.animation;
  }
}
