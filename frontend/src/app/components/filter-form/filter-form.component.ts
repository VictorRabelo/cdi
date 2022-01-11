import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-filter-form',
  templateUrl: './filter-form.component.html',
  styleUrls: ['./filter-form.component.css']
})
export class FilterFormComponent implements OnInit {

  dados: any = {};
  loading: boolean = false;

  constructor(
    private activeModal: NgbActiveModal,
  ) { }

  ngOnInit() {
  }

  close(params = undefined) {
    this.activeModal.close(params);
  }

  submit(form: NgForm) {

    this.loading = true;

    this.dados.date = `${this.dados.ano}-${this.dados.mes}`;
    this.close(this.dados);
  }

  today() {
    
    let data = new Date();
    
    let mes = this.monthCurrent(data.getMonth());
    let ano = data.getFullYear();

    this.loading = true;

    this.dados.date = `${ano}-${mes}`;
    this.close(this.dados);
  }

  monthCurrent(mes) {
    let monthCurrent;
    switch (mes) {
      case 0:
        monthCurrent = '01';
        break;
      case 1:
        monthCurrent = '02';
        break;
      case 2:
        monthCurrent = '03';
        break;
      case 3:
        monthCurrent = '04';
        break;
      case 4:
        monthCurrent = '05';
        break;
      case 5:
        monthCurrent = '06';
        break;
      case 6:
        monthCurrent = '07';
        break;
      case 7:
        monthCurrent = '08';
        break;
      case 8:
        monthCurrent = '09';
        break;
      case 9:
        monthCurrent = '10';
        break;
        case 10:
        monthCurrent = '11';
        break;
      case 11:
        monthCurrent = '12';
        break;
    }

    return monthCurrent;
  }
}
