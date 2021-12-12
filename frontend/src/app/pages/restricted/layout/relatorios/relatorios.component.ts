import { Component } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';

import { ControllerBase } from '@app/controller/controller.base';
import { RelatorioService } from '@app/services/relatorio.service';

import { MessageService } from 'primeng/api';

import { SubSink } from 'subsink';

@Component({
  selector: 'app-relatorios',
  templateUrl: './relatorios.component.html',
  styleUrls: ['./relatorios.component.css'],
  providers: [ MessageService ]
})
export class RelatoriosComponent extends ControllerBase {

  private sub = new SubSink();

  loading = false;

  constructor(
    private relatorioService: RelatorioService
  ) { 

    super();
  }

  ngOnInit() {
  }

  downloadVendas(){
    this.loading = true;
    this.sub.sink = this.relatorioService.getVendas().subscribe(
      (res: any) => {
        this.downloadPDF(res.file, res.data, 'vendas')
      },
      error => console.log(error),
      ()=>{
        this.loading = false;
      })
  }

  downloadEntregas(){
    this.loading = true;
    this.sub.sink = this.relatorioService.getEntregas().subscribe(
      (res: any) => {
        this.downloadPDF(res.file, res.data, 'entregas')
      },
      error => console.log(error),
      ()=>{
        this.loading = false;
      })
  }

  downloadEstoque(){
    this.loading = true;
    this.sub.sink = this.relatorioService.getEstoque().subscribe(
      (res: any) => {
        this.downloadPDF(res.file, res.data, 'estoque')
      },
      error => console.log(error),
      ()=>{
        this.loading = false;
      })
  }

  downloadClientes(){
    this.loading = true;
    this.sub.sink = this.relatorioService.getClientes().subscribe(
      (res: any) => {
        this.downloadPDF(res.file, res.data, 'clientes')
      },
      error => console.log(error),
      ()=>{
        this.loading = false;
      })
  }

  downloadVendidos(){
    this.loading = true;
    this.sub.sink = this.relatorioService.getVendidos().subscribe(
      (res: any) => {
        this.downloadPDF(res.file, res.data, 'vendido')
      },
      error => console.log(error),
      ()=>{
        this.loading = false;
      })
  }

  downloadPDF(file, data, type) {
    
    let fileName = data +'_'+ type + '.pdf';
    const bytes: Uint8Array = this.base64ToArrayBuffer(file);
    const filePdf: Blob = new Blob([bytes], {type: 'application/pdf'});
    let fileURL = URL.createObjectURL(filePdf);

    let link = document.createElement("a");
    link.href = fileURL;
    link.download = fileName;
    document.body.append(link);

    link.click();
    link.remove();
    // in case the Blob uses a lot of memory
    window.addEventListener('focus', e=>URL.revokeObjectURL(fileURL), {once:true});
  }

  base64ToArrayBuffer(base64: string): Uint8Array {
    var binaryString = window.atob(base64);
    var binaryLen = binaryString.length;
    var bytes = new Uint8Array(binaryLen);
    for (var i = 0; i < binaryLen; i++) {
      var ascii = binaryString.charCodeAt(i);
      bytes[i] = ascii;
    }
    return bytes;
  }

  ngOnDestroy(){
    this.sub.unsubscribe();
  }

}
