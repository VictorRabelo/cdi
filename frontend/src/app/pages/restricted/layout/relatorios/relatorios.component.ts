import { Component } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';

import { ControllerBase } from '@app/controller/controller.base';
import { CategoriaService } from '@app/services/categoria.service';
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

  categories: any = {};
  dados: any = {
    categoria_id: ''
  };

  constructor(
    private categoriaService: CategoriaService,
    private relatorioService: RelatorioService
  ) { 

    super();
  }

  ngOnInit() {
    this.getStart()
  }
  
  getStart(){
    this.loading = true;
    this.getAllCategorias()
  }

  getAllCategorias() {
    this.sub.sink = this.categoriaService.getAll().subscribe(
      (res: any) => {
        this.categories = res;
        this.loading = false;
      },
      error => {
        this.loading = false;
        console.log(error)
      })
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

  downloadCatalogo(){
    this.loading = true;
    this.sub.sink = this.relatorioService.getCatalogo(this.dados).subscribe(
      (res: any) => {
        this.downloadPDF(res.file, res.data, 'catalago')
      },
      error => console.log(error),
      ()=>{
        this.loading = false;
      })
  }
  
  ngOnDestroy(){
    this.sub.unsubscribe();
  }

}
