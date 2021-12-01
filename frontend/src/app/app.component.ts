import { Component } from '@angular/core';
import { ControllerBase } from './controller/controller.base';

import { NgxSpinnerService } from 'ngx-spinner';
import { HTTPStatus } from './helpers/httpstatus';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent extends ControllerBase {
  
  title = 'Controle de Estoque';
  
  constructor(private spinner: NgxSpinnerService) {
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
  }
}
