import { Component, OnInit } from '@angular/core';
import { MessageService } from '@app/services/message.service';

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

import * as FileSaver from 'file-saver';
import { jsPDF } from "jspdf";
import autoTable from 'jspdf-autotable'

import { SubSink } from 'subsink';

@Component({
  selector: 'app-table',
  templateUrl: './table.component.html',
  styleUrls: ['./table.component.css']
})
export class TableComponent implements OnInit {
  private sub = new SubSink();

  loading: boolean = false;

  dataSource: any[] = [];
  filters: any = {};

  term: string;

  user: any = {};
  userCurrent: any = {};

  isFilter: boolean = false;

  constructor(
    private modalCtrl: NgbModal,
    private message: MessageService
  ) {

  }

  async ngOnInit() {
    this.getStart();
  }

  getStart() {
    this.loading = true;
  }

  
  openFilter(){
    this.isFilter = (this.isFilter)? false:true;
  }

  filterDate(res: any) {
    
    this.filters = res;
    this.loading = true;
    this.getView();
  }

  getView() {
    this.sub.sink = this.service.getView(this.filters).subscribe(res => {
      
      this.dataSource = res;
      this.loading = false;

    }, error => {

      this.loading = false;
      this.message.toastError(error.message);
      console.log(error);

    });
  }

  openForm(crud, id = undefined) {
    const modalRef = this.modalCtrl.open(ComprasFormComponent, { size: 'md', backdrop: 'static' });
    modalRef.componentInstance.id = id;
    modalRef.componentInstance.crud = crud;
    modalRef.result.then(res => {
      if (res.message) {
        this.message.toastSuccess();
      }
      this.getView();
    })
  }

  deleteConfirm(id) {
    this.message.swal.fire({
      title: 'Atenção!',
      icon: 'warning',
      html: `Deseja excluir esse usuário? `,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Voltar',
      showCancelButton: true
    }).then(res => {
      if (res.isConfirmed) {
        this.delete(id);
      }
    })
  }

  delete(id) {

    this.loading = true;

    this.service.delete(id).subscribe(
      (res: any) => {
        this.getStart();
      },
      error => console.log(error),
      () => {
        this.message.toastSuccess('Excluido com Sucesso!');
        this.loading = false;
      }
    );
  }

  exportPdf() {
    const doc = new jsPDF()

    autoTable(doc, {
      html: '#table-compras',
      columns: [
        { header: 'Rebanho' },
        { header: 'Contrato' },
        { header: 'Fornecedor' },
        { header: 'Dt. Compra' },
        { header: 'Dt. Investimento' },
      ],
    })

    doc.save('tabela_compras.pdf')
  }

  exportExcel() {
    import("xlsx").then(xlsx => {
      const worksheet = xlsx.utils.json_to_sheet(this.dataSource);
      const workbook = { Sheets: { 'data': worksheet }, SheetNames: ['data'] };
      const excelBuffer: any = xlsx.write(workbook, { bookType: 'xlsx', type: 'array' });
      this.saveAsExcelFile(excelBuffer, "compras");
    });
  }

  saveAsExcelFile(buffer: any, fileName: string): void {
    let EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
    let EXCEL_EXTENSION = '.xlsx';
    const data: Blob = new Blob([buffer], {
      type: EXCEL_TYPE
    });
    FileSaver.saveAs(data, fileName + '_export_' + new Date().getTime() + EXCEL_EXTENSION);
  }

  ngOnDestroy(): void {
    this.sub.unsubscribe();
  }
}
