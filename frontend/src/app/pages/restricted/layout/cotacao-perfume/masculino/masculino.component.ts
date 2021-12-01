import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { DomSanitizer } from '@angular/platform-browser';

import { ControllerBase } from '@app/controller/controller.base';

import { ProdutoService } from '@app/services/produto.service';

import { MessageService } from 'primeng/api';

import { SubSink } from 'subsink';

@Component({
  selector: 'app-masculino',
  templateUrl: './masculino.component.html',
  styleUrls: ['./masculino.component.css'],
  providers: [ MessageService ]
})
export class MasculinoComponent extends ControllerBase {

  private sub = new SubSink();

  createForm: FormGroup;
  
  loading: Boolean = false;
  loadingCreate = false;
  
  term: string;

  perfumes: any[];
  
  constructor(private messageService: MessageService, private formBuilder: FormBuilder, private produtoService: ProdutoService, private sanitizer: DomSanitizer) {
    super();
  }

  ngOnInit() {
    this.loading = true;

    this.getAll();
    
    this.createForm = this.formBuilder.group({
      dolar: ['', Validators.required]
    });
  }

  getAll(){
    this.sub.sink = this.produtoService.getMasculino().subscribe(
      (res: any) => {
        this.loading = false;
        this.perfumes = res;
        this.perfumes.forEach((valor, chave) => {
          let imgURL = 'data:image/'+ this.getExtensionFileName(valor.img) +';base64, '+ valor.path;
          valor.path =  this.sanitizer.bypassSecurityTrustResourceUrl(imgURL);
        })
      },
      error => {
        this.loading = false;
        console.log(error)
      })
  }

  get f() { return this.createForm.controls; }
  
  atualizar(){
    this.loadingCreate = true;

    if (this.createForm.invalid) {
      return;
    }

    const store = {
      dolar: this.f.dolar.value
    }
    
    this.produtoService.storeDolarMasculino(store).subscribe(
      (res: any) => {
        this.loading = true;
        this.getAll();
      },
      error => {
        console.log(error)
        this.loading = false;
        this.loadingCreate = false;
      },
      () => {
        this.messageService.add({key: 'bc', severity:'success', summary: 'Sucesso', detail: 'Atualizado com Sucesso!'});
        this.loadingCreate = false;
        this.createForm.reset()
      }
    )
    
  }

  getExtensionFileName(img): string {
    const parts: string[] = img.split(/[\.]/g);
    return parts[parts.length -1];
  }

  ngOnDestroy(){
    this.sub.unsubscribe();
  }

}
