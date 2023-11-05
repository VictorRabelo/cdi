import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { FinanceiroRoutingModule } from './financeiro-routing.module';

import { APagarComponent } from './a-pagar/a-pagar.component';
import { AReceberComponent } from './a-receber/a-receber.component';
import { CaixaDolarComponent } from './caixa-dolar/caixa-dolar.component';

import { ToastModule } from 'primeng/toast';

import { AutocompleteLibModule } from 'angular-ng-autocomplete';
import { Ng2SearchPipeModule } from 'ng2-search-filter';
import { NgxMaskModule, IConfig } from 'ngx-mask';
import { CurrencyMaskInputMode, NgxCurrencyModule } from "ngx-currency";
import { DespesasComponent } from './despesas/despesas.component';

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
    APagarComponent,
    AReceberComponent,
    CaixaDolarComponent,
    DespesasComponent
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
    ToastModule,
    FinanceiroRoutingModule,
    AutocompleteLibModule,
    Ng2SearchPipeModule,
    NgxCurrencyModule.forRoot(customCurrencyMaskConfig),
    NgxMaskModule.forRoot()
  ]
})
export class FinanceiroModule { }
