import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { DomSanitizer } from '@angular/platform-browser';

import { ControllerBase } from '@app/controller/controller.base';
import { ClienteService } from '@app/services/cliente.service';
import { VendaService } from '@app/services/venda.service';
import { EstoqueService } from '@app/services/estoque.service';
import { HTTPStatus } from '@app/helpers/httpstatus';

import { MessageService } from 'primeng/api';

import { SubSink } from 'subsink';

declare let $: any;
import 'bootstrap';
import { VendasResponse } from '@app/models/vendasResponse';

@Component({
  selector: 'app-venda',
  templateUrl: './venda.component.html',
  styleUrls: ['./venda.component.css'],
  providers: [MessageService]
})
export class VendaComponent extends ControllerBase {
  public today: number = Date.now();

  private sub = new SubSink();

  createForm: FormGroup;
  updateForm: FormGroup;
  clienteForm: FormGroup;

  loading: Boolean = false;
  progress: Boolean = false;
  loadingCreate: Boolean = false;
  loadingCliente: Boolean = false;
  loadingUpdate: Boolean = false;
  loadingDelete: Boolean = false;
  loadingProduto: Boolean = false;
  loadingUp: Boolean = false;
  createClose: Boolean = false;

  term: string;
  date: any = '';

  keywordCliente = 'name';

  vendas: any[] = [];

  vendaDetalhe: any;
  vendaProdutoDetalhe: any[];
  clientes: any;
  estoques: any;

  termProduto: string;

  resultsClientes: any[];
  resultsEstoque: any[];

  produto: any = '';
  produtosSelecionados: any[] = [];
  produtosSelecionadosUpdate: any[] = [];

  precoVenda: any;
  qtdVenda: any;

  precoVendaUpdate: any;
  qtdVendaUpdate: any;

  lucro: number = 0;
  pago: number = 0;
  total: number = 0;
  totalMensal: number = 0;

  dtOptions: any = {};

  constructor(private vendaService: VendaService, private httpStatus: HTTPStatus, private estoqueService: EstoqueService, private clienteService: ClienteService, private messageService: MessageService, private sanitizer: DomSanitizer, private formBuilder: FormBuilder) {
    super();
  }

  ngOnInit() {

    this.loading = true;

    this.getAll();
    this.getAllCliente();

    this.createForm = this.formBuilder.group({
      cliente: ['', Validators.required],
      total_final: ['', Validators.required],
      lucro: ['', Validators.required],
      pago: ['', Validators.required],
      pagamento: ['', Validators.required],
      status: ['', Validators.required],
      status_movition: ['', Validators.required],
      prazo: [''],
    });

    this.updateForm = this.formBuilder.group({
      clienteUpdate: [''],
      totalFinalUpdate: [''],
      lucroUpdate: [''],
      pagoUpdate: [''],
      pagamentoUpdate: [''],
      statusUpdate: [''],
    });

    this.clienteForm = this.formBuilder.group({
      nameCliente: ['', Validators.required],
      telefoneCliente: ['', Validators.required]
    });
  }

  get f() { return this.createForm.controls; }
  get u() { return this.updateForm.controls; }
  get c() { return this.clienteForm.controls; }

  // Get
  getAll() {

    this.sub.sink = this.vendaService.getAll().subscribe(
      (res: any) => {
        this.loading = false;
        this.vendas = res.vendas;
        this.lucro = res.lucro;
        this.totalMensal = res.totalMensal;
        this.total = res.total;
        this.pago = res.pago;
        this.date = res.mount;
      }, error => console.log(error))
  }

  async getAllCliente() {

    this.sub.sink = this.clienteService.getAll().subscribe(
      (res: any) => {
        this.loading = false;
        this.clientes = res;
      },
      error => {
        this.loading = false;
        console.log(error)
      })
  }

  getEmEstoque() {

    this.progressBar();

    this.sub.sink = this.estoqueService.getEmEstoque().subscribe(
      (res: any) => {
        this.loadingProduto = false;
        this.estoques = res;
        this.estoques.forEach((valor, chave) => {
          let imgURL = 'data:image/' + this.getExtensionFileName(valor.img) + ';base64, ' + valor.path;
          valor.path = this.sanitizer.bypassSecurityTrustResourceUrl(imgURL);
        })
      },
      error => {
        this.loadingProduto = false;
        console.log(error)
      })
  }

  all() {

    this.sub.sink = this.vendaService.all().subscribe(
      (res: any) => {
        this.loading = false;
        this.vendas = res.vendas;
        this.lucro = res.lucro;
        this.total = res.total;
        this.pago = res.pago;

      }, error => console.log(error))
  }

  rangeDate() {

    this.loading = true;

    if (this.date == 0) {
      return this.all();
    }

    let req = {
      date: this.date
    }

    this.sub.sink = this.vendaService.getMesEspecifico(req).subscribe(
      (res: any) => {
        this.loading = false;
        this.vendas = res.vendas;
        this.lucro = res.lucro;
        this.total = res.total;
        this.pago = res.pago;
      },
      error => {
        this.loading = false;
        console.log(error)
      })
  }

  progressBar() {
    this.httpStatus.getProgressBar().subscribe((status: boolean) => {
      if (status) {
        this.progress = true;
      }
      else {
        this.progress = false;
      }
    }
    );
  }

  openCliente() {
    $("#modal-cliente").modal('show');
  }

  // Listar os produtos disponiveis no modal
  listarProduto() {
    this.loadingProduto = true;
    $("#modal-add").modal('show');
    if (!this.estoques) {
      this.getEmEstoque();
    } else {
      this.loadingProduto = false;
    }
  }

  // Seta o produto no modal-view
  getProduto(produto: any) {
    this.produto = produto
    $("#modal-view").modal('show');
  }

  // Seta o produto no modal-view-update
  getProdutoSelecionado(produto: any) {
    this.produto = produto;
    this.precoVendaUpdate = this.produto.precoVenda;
    this.qtdVendaUpdate = this.produto.qtdVenda;
    $("#modal-view-update").modal('show');
  }

  // Registar o produto no array this.produtosSelecionados
  setProduto() {

    if (this.qtdVenda > this.produto.und) {
      alert('Não há essa quantia em estoque');
      $("#qtdVenda")[0].value = '';
      return
    }

    if (this.qtdVenda == null || this.precoVenda == null) {
      alert('Algum campo não está preenchido');
      return
    }

    if (this.produtosSelecionados.length < 0) {
      alert('Teste');
      return
    }

    this.produto.lucroVenda = (this.precoVenda - this.produto.valor_total) * this.qtdVenda;
    this.produto.precoVenda = this.precoVenda;

    if (this.qtdVenda > 1) {

      this.produto.qtdVenda = this.qtdVenda;
      this.produto.precoVenda = this.qtdVenda * this.precoVenda;

    } else {

      this.produto.qtdVenda = this.qtdVenda;

    }

    this.produtosSelecionados.push(this.produto);

    if (this.produtosSelecionados.length >= 0) {

      let lucro = null;
      let total = null;

      this.produtosSelecionados.forEach((valor, chave) => {
        if (valor.qtdVenda > 1) {
          lucro = valor.lucroVenda + lucro;
          total = valor.precoVenda + total;
        } else {
          lucro = valor.lucroVenda + lucro;
          total = valor.precoVenda + total;
        }
      })

      this.f.lucro.setValue(lucro);
      this.f.total_final.setValue(total);

    }

    $("#precoVenda")[0].value = '';
    $("#qtdVenda")[0].value = '';

    $("#modal-view").modal('hide');
    $("#modal-add").modal('hide');

    this.precoVenda = '';
    this.qtdVenda = '';
  }

  // setar o cliente.id no form
  selectEventCliente(cliente) {
    this.f.cliente.setValue(cliente.id_cliente);
  }

  onSubmitCliente() {
    this.loadingCliente = true;

    if (this.clienteForm.invalid) {
      this.loadingCliente = false;
      alert('Preencha todos os campos com " * "');
      return;
    }

    const store = {
      name: this.c.nameCliente.value,
      telefone: this.c.telefoneCliente.value,
    }

    this.clienteService.store(store).subscribe(
      (res: any) => {
        this.loading = true;
        this.getAllCliente();
        $("#modal-cliente").modal('hide');

      },
      error => {
        console.log(error)
        this.loading = false;
        this.loadingCliente = false;

      },
      () => {
        this.messageService.add({ key: 'bc', severity: 'success', summary: 'Sucesso', detail: 'Cadastrado com Sucesso!' });
        this.loadingCliente = false;
        this.clienteForm.reset()
      }
    )

  }

  // Post para o servidor
  onSubmit() {

    this.loadingCreate = true;

    if (this.createForm.invalid) {
      this.loadingCreate = false;
      alert('Preencha todos os campos com " * "');
      return;
    }

    if (this.produtosSelecionados.length < 0) {
      alert('Selecione um produto');
      return;
    }

    let request = {
      cliente_id: this.f.cliente.value,
      produtos: this.produtosSelecionados,
      total_final: this.f.total_final.value,
      pago: this.f.pago.value,
      lucro: this.f.lucro.value,
      pagamento: this.f.pagamento.value,
      status: this.f.status.value,
      status_movition: this.f.status_movition.value,
      prazo: this.f.prazo.value
    }

    this.vendaService.store(request).subscribe(
      (res: any) => {
        this.loadingCreate = false;
        this.loading = true;
        this.getAll();
        this.getEmEstoque();
        this.createForm.reset();
        this.zeraArray();
      },
      error => {
        console.log(error)
        this.loadingCreate = false;
      },
      () => {
        this.createClose = true;
        this.messageService.add({ key: 'bc', severity: 'success', summary: 'Sucesso', detail: 'Venda feita com Sucesso!' });
      })
  }

  upProduto(id: number) {
    $("#modal-update").modal('show');
    this.loadingUp = true;
    this.vendaService.getById(id).subscribe(
      (res: any) => {
        this.vendaDetalhe = res.venda;
        this.vendaProdutoDetalhe = res.produtos;
        if (this.vendaDetalhe.status == 'pendente') {
          this.u.pagoUpdate.enable();
        } else {
          this.u.pagoUpdate.disable();
        }
        this.setarUp(this.vendaDetalhe);
      },
      error => {
        console.log(error)
        this.loadingUp = false;
      },
      () => {
        this.loadingUp = false;
        this.messageService.add({ key: 'bc', severity: 'success', summary: 'Sucesso', detail: 'Atualizado com Sucesso!' });
      })
  }

  // Put para o servidor
  onUpdate() {
    this.loadingUpdate = true;
    this.vendaService.update(this.vendaDetalhe.id_venda, this.updateForm.value).subscribe(
      (res: any) => {
        this.loading = true;
        this.getAll();
      },
      error => {
        console.log(error)
        this.loadingUpdate = false;
      },
      () => {
        this.loadingUpdate = false;
        this.messageService.add({ key: 'bc', severity: 'success', summary: 'Sucesso', detail: 'Atualizado com Sucesso!' });
      })
  }

  // Put no modal de cadastrar venda
  putProduto() {

    if (this.qtdVendaUpdate > this.produto.und) {
      alert('Não há essa quantia em estoque');
      $("#qtdVendaUpdate")[0].value = '';
      return
    }

    this.produtosSelecionados.forEach((valor, chave) => {
      if (this.produto.cod_rastreio == valor.cod_rastreio) {
        valor.lucroVenda = this.precoVendaUpdate - valor.valor_total;
        valor.precoVenda = this.precoVendaUpdate;
        valor.qtdVenda = this.qtdVendaUpdate;
        $("#modal-view-update").modal('hide');
      }
    })
  }

  // Delete para o servidor
  delete(id: number) {

    this.loadingDelete = true;

    this.vendaService.delete(id).subscribe(
      (res: any) => {
        this.loadingDelete = false;
        this.loading = true;
        this.getAll();
      },
      error => {
        console.log(error)
      },
      () => {
        this.messageService.add({ key: 'bc', severity: 'success', summary: 'Sucesso', detail: 'Excluido com Sucesso!' });
        this.loadingDelete = false;
      }
    );
  }

  // Delete no modal de cadastrar venda
  deleteProduto(produto) {
    for (var i = 0; i < this.produtosSelecionados.length; i++) {
      if (this.produtosSelecionados[i].cod_rastreio === produto.cod_rastreio) {
        this.produtosSelecionados.splice(i, 1);
      }
    }
  }

  zeraArray() {
    while (this.produtosSelecionados.length) {
      this.produtosSelecionados.pop();
    }
  }

  setarUp(venda) {
    this.u.clienteUpdate.setValue(venda.name);
    this.u.totalFinalUpdate.setValue(venda.total_final);
    this.u.lucroUpdate.setValue(venda.lucro);
    this.u.pagoUpdate.setValue(venda.pago);
    this.u.pagamentoUpdate.setValue(venda.pagamento);
    this.u.statusUpdate.setValue(venda.status);
  }

  getExtensionFileName(img): string {
    const parts: string[] = img.split(/[\.]/g);
    return parts[parts.length - 1];
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }

}