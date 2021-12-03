import { Component, ElementRef, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { animate, style, transition, trigger } from '@angular/animations';
import { DomSanitizer } from '@angular/platform-browser';
import { NgForm } from '@angular/forms';

import { SubSink } from 'subsink';

import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';

import { EstoqueService } from '@app/services/estoque.service';
import { CategoriaService } from '@app/services/categoria.service';
import { FornecedorService } from '@app/services/fornecedor.service';

import { ModalPessoalComponent } from '../modal-pessoal/modal-pessoal.component';
import { ClienteFormComponent } from '../cliente-form/cliente-form.component';
import { ControllerBase } from '@app/controller/controller.base';
import { NgxIzitoastService } from 'ngx-izitoast';

@Component({
  selector: 'app-estoque-form',
  templateUrl: './estoque-form.component.html',
  styleUrls: ['./estoque-form.component.css'],
  animations: [
    trigger(
      'enterAnimation', [
        transition(':enter', [
          style({transform: 'translateY(100%)', opacity: 0}),
          animate('500ms', style({transform: 'translateY(0)', opacity: 1}))
        ]),
        transition(':leave', [
          style({transform: 'translateY(0)', opacity: 1}),
          animate('500ms', style({transform: 'translateY(100%)', opacity: 0}))
        ])
      ]
    )
  ],
  providers: [
    EstoqueService
  ]
})
export class EstoqueFormComponent extends ControllerBase {
  @ViewChild('inputProduto', {static: false}) inputProduto: ElementRef;

  private sub = new SubSink();

  loading: boolean = false;

  @Input() data: any;
  @Input() crud: string;

  dados: any = {
    categoria_id: '',
    tipo: '',
    status: 'pago',
  };

  categories: any = {};

  constructor(
    private activeModal: NgbActiveModal,
    private modalCtrl: NgbModal,
    private service: EstoqueService,
    private categoriaService: CategoriaService, 
    private fornecedorService: FornecedorService,
    private sanitizer: DomSanitizer,
    private iziToast: NgxIzitoastService,
  ) {
    super();
  }

  ngOnInit() {
    this.getStart();
    if(this.data !== undefined){
      this.getById(this.data);
    }
  }
  
  getStart(){
    this.getAllCategorias();
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  openFornecedores() {
    const modalRef = this.modalCtrl.open(ModalPessoalComponent, { size: 'lg', backdrop: 'static' });
    modalRef.componentInstance.type = 'fornecedores';
    modalRef.result.then(res => {
      this.dados.fornecedor = res.fornecedor;
      this.dados.fornecedor_id = res.id_fornecedor;
    })
  }

  addCategory(){
    const modalRef = this.modalCtrl.open(ClienteFormComponent, { size: 'sm', backdrop: 'static' });
    modalRef.componentInstance.module = 'categorias';
    modalRef.componentInstance.crud = 'cadastrar';
    modalRef.result.then(res => {
      this.getAllCategorias();
    })
  }

  getAllCategorias(){
    
    this.sub.sink = this.categoriaService.getAll().subscribe(
      (res: any) => {
        this.loading = false;
        this.categories = res;
      },
      error => {
        this.loading = false;
        console.log(error)
      })
  }

  handleFileInput(files: FileList) {
    
    if (files.length > 0) {
      this.dados.file = files[0]
      let reader = new FileReader();
      reader.readAsDataURL(this.dados.file);
      reader.onload = e => {
        let img =  reader.result as string;
        this.dados.path = this.sanitizer.bypassSecurityTrustResourceUrl(img);
        this.dados.file = img;
      }
    }
  
  }

  getById(id) {
    this.loading = true;
    this.sub.sink = this.service.getById(id).subscribe(
      (res: any) => {
        this.loading = false;
        this.dados = res;
      },
      error => {
        console.log(error)
      });
  }

  submit(form: NgForm) {
    if (!form.valid) {
      return false;
    }
    
    if (this.data) {
      this.update();
    } else {
      this.create();
    }

  }

  create() {
    this.loading = true;

    this.service.store(this.dados).subscribe(
      (res: any) => {
        res.message = "Cadastro bem sucedido!"
        this.close(res);
      },
      error => {
        this.loading = false;
        console.log(error)
      }
    )
  }

  update() {
    this.loading = true;

    this.service.update(this.data, this.dados).subscribe(
      (res: any) => {
        this.iziToast.success({
          title: 'Sucesso!',
          message: "Atualização bem sucedido!",
          position: 'topRight'
        });
        this.close(res);
      },
      error => {
        this.loading = false;
        this.iziToast.error({
          title: 'Atenção!',
          message: error,
          position: 'topRight'
        });
        console.log(error)
      }
    )
  }
  
  openPhotoPicker() {
    this.inputProduto.nativeElement.click();
  }

  clearForm(){
    this.dados = {
      path: this.dados.path? this.dados.path:'/assets/img/sem_foto.jpg',
      file: this.dados.file? this.dados.file:'',
      status: 'pago',
      tipo: this.dados.tipo,
      valor_site: 0,
      dolar: 0,
      total_site: 0,
      frete_mia_pjc: 0,
      dolar_frete: 0,
      total_frete_mia_pjc: 0,
      frete_pjc_gyn: 0,
      total_frete: 0,
      valor_total: 0
    };
  }

  calcCompra(){
    const valorSite = (this.dados.valor_site) ? this.dados.valor_site : 0;
    const dolar = (this.dados.dolar) ? this.dados.dolar : 0;
    let totalSite = 0;
    
    if(valorSite > 0 && dolar > 0){
      totalSite = valorSite * dolar;
    }
    this.dados.valor_custo = valorSite;
    this.dados.dolar = dolar;
    this.dados.total_site = totalSite;
  }
  
  calcFreteMiami(){
    const freteMiami = (this.dados.frete_mia_pjc) ? this.dados.frete_mia_pjc : 0;
    const dolarFrete = (this.dados.dolar_frete) ? this.dados.dolar_frete : 0;
    let totalFrete = 0;
    
    if(freteMiami > 0 && dolarFrete > 0){
      totalFrete = freteMiami * dolarFrete;
    }
    this.dados.frete_mia_pjc = freteMiami;
    this.dados.dolar_frete = dolarFrete;
    this.dados.total_frete_mia_pjc = totalFrete;
  }
  
  calcFrete(){
    const totalFreteMiami = (this.dados.total_frete_mia_pjc) ? this.dados.total_frete_mia_pjc : 0;
    const fretePjcGyn = (this.dados.frete_pjc_gyn) ? this.dados.frete_pjc_gyn : 0;
    let totalFrete = 0;
    
    if(totalFreteMiami > 0 && fretePjcGyn > 0){
      totalFrete = totalFreteMiami + fretePjcGyn;
      this.dados.total_frete = totalFrete;
      this.dados.valor_total = this.dados.total_site + totalFrete;
    }

    if(fretePjcGyn > 0 && totalFreteMiami == 0){
      this.dados.valor_total = this.dados.total_site + fretePjcGyn;
    }

  }
  
  calcBr(){
    const totalSite = (this.dados.total_site) ? this.dados.total_site : 0;
    const totalFrete = (this.dados.total_frete) ? this.dados.total_frete : 0;
    let valorTotal = 0;

    if(totalSite > 0 && totalFrete > 0){
      valorTotal = totalSite + totalFrete;
    }

    this.dados.total_site = totalSite;
    this.dados.total_frete = totalFrete;
    this.dados.valor_total = valorTotal;
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }
}