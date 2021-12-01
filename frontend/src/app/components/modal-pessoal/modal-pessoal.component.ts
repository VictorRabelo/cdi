import { Component, Input, OnInit } from '@angular/core';
import { CrudService } from '@app/services/crud.service';
import { NgbActiveModal, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ClienteFormComponent } from '../cliente-form/cliente-form.component';

@Component({
  selector: 'app-modal-pessoal',
  templateUrl: './modal-pessoal.component.html',
  styleUrls: ['./modal-pessoal.component.css'],
  providers: [
    CrudService
  ]
})
export class ModalPessoalComponent implements OnInit {

  dataSource: any = [];

  loading: boolean = false;

  @Input() data: any;
  @Input() type: string;

  term: string;

  constructor(
    private modalCtrl: NgbModal,
    private activeModal: NgbActiveModal,
    private service: CrudService
  ) { }

  ngOnInit(): void {
    if (this.type) {
      this.service.setEndPoint(this.type);    
      this.listing();
    }
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  listing() {
    this.loading = true;
    this.service.getAll().subscribe(res => {
      this.loading = false;
      this.dataSource = res;

    })
  }

  addPessoal() {
    const modalRef = this.modalCtrl.open(ClienteFormComponent, { size: 'sm', backdrop: 'static' });
    modalRef.componentInstance.module = this.type;
    modalRef.componentInstance.crud = 'cadastrar';
    modalRef.result.then(res => {
      if (res) {
        this.listing();
      }
    })
  }

}
