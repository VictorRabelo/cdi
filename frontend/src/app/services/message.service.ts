import { Injectable } from "@angular/core";

import { NgxIzitoastService } from "ngx-izitoast";
import Swal from "sweetalert2";

@Injectable({
  providedIn: 'root'
})
export class MessageService {
  public swal = Swal;
  
  constructor(
    private iziToast: NgxIzitoastService
  ) { }

  public toastError(msg = 'Falha de servidor', title = 'Ops!') {
    this.iziToast.error({
      title: title,
      message: msg,
      position: 'topRight'
    });
  }

  public toastSuccess(msg = 'Cadastrado com sucesso!', title = 'Prontinho!') {
    this.iziToast.success({
      title: title,
      message: msg,
      position: 'topRight'
    });
  }

  public toastWarning(msg = 'Algo está faltando!', title = 'Opa!') {
    this.iziToast.warning({
      title: title,
      message: msg,
      position: 'topRight'
    });
  }
}
