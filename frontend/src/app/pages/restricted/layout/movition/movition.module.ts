import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { MovitionRoutingModule } from './movition-routing.module';
import { MoveGeralComponent } from './move-geral/move-geral.component';
import { MoveEletronicoComponent } from './move-eletronico/move-eletronico.component';
import { MoveAllComponent } from './move-all/move-all.component';

import { ToastModule } from 'primeng/toast';

import { Ng2SearchPipeModule } from 'ng2-search-filter';

import { NgxIziToastModule } from 'ngx-izitoast';
import { NgxMaskModule, IConfig } from 'ngx-mask';
import { CurrencyMaskInputMode, NgxCurrencyModule } from "ngx-currency";

export const customCurrencyMaskConfig = {
  align: "left",
  allowNegative: true,
  allowZero: true,
  decimal: ",",
  precision: 2,
  prefix: "R$ ",
  suffix: "",
  thousands: ".",
  nullable: true,
  min: null,
  max: null,
  inputMode: CurrencyMaskInputMode.FINANCIAL
};
export const options: Partial<IConfig> | (() => Partial<IConfig>) = null;


@NgModule({
  declarations: [
    MoveGeralComponent, 
    MoveEletronicoComponent, 
    MoveAllComponent
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
    MovitionRoutingModule,
    ToastModule,
    NgxIziToastModule,
    Ng2SearchPipeModule,
    NgxCurrencyModule.forRoot(customCurrencyMaskConfig),
    NgxMaskModule.forRoot()
  ]
})
export class MovitionModule { }
