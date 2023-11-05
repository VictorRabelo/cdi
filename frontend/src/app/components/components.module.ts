import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

// Modules
import { UtilModule } from '@app/util/util.module';

// Components
import { ClienteFormComponent } from './cliente-form/cliente-form.component';
import { EstoqueFormComponent } from './estoque-form/estoque-form.component';
import { ModalPessoalComponent } from './modal-pessoal/modal-pessoal.component';
import { ModalProductsComponent } from './modal-products/modal-products.component';
import { ModalProductDadosComponent } from './modal-product-dados/modal-product-dados.component';
import { ModalDebitarComponent } from './modal-debitar/modal-debitar.component';
import { ModalDolarFormComponent } from './modal-dolar-form/modal-dolar-form.component';
import { ModalAlterPasswordComponent } from './modal-alter-password/modal-alter-password.component';
import { FilterFormComponent } from './filter-form/filter-form.component';
import { TimerComponent } from './timer/timer.component';
import { ModalMovitionComponent } from './modal-movition/modal-movition.component';
import { FormaPagamentoFormComponent } from './forma-pagamento-form/forma-pagamento-form.component';
import { TableComponent } from './table/table.component';

// PrimeNG
import { ToastModule } from 'primeng/toast';
import { FileUploadModule } from 'primeng/fileupload';
import { RippleModule } from 'primeng/ripple';
import { ProgressSpinnerModule } from 'primeng/progressspinner';
import { ProgressBarModule } from 'primeng/progressbar';
import { TableModule } from 'primeng/table';
import { CardModule } from 'primeng/card';
import { ToolbarModule } from 'primeng/toolbar';
import { TooltipModule } from 'primeng/tooltip';
import { AccordionModule } from 'primeng/accordion';
import { AutoCompleteModule } from 'primeng-lts/autocomplete';

// Material/Angular
import { MatStepperModule } from '@angular/material/stepper';
import { MatTabsModule } from '@angular/material/tabs';

// Plugs
import { NgxMaskModule } from 'ngx-mask';
import { CurrencyMaskInputMode, NgxCurrencyModule } from 'ngx-currency';
import { Ng2SearchPipeModule } from 'ng2-search-filter';
import { TiposFormComponent } from './tipos-form/tipos-form.component';

export const customCurrencyMaskConfig = {
  align: "right",
  allowNegative: true,
  allowZero: true,
  decimal: ",",
  precision: 2,
  prefix: "R$ ",
  suffix: "",
  thousands: ".",
  nullable: false,
  min: null,
  max: null,
  inputMode: CurrencyMaskInputMode.FINANCIAL
};

@NgModule({
  declarations: [
    ClienteFormComponent,
    EstoqueFormComponent,
    ModalPessoalComponent,
    ModalProductsComponent,
    ModalProductDadosComponent,
    ModalDebitarComponent,
    ModalMovitionComponent,
    ModalDolarFormComponent,
    ModalAlterPasswordComponent,
    FilterFormComponent,
    TimerComponent,
    FormaPagamentoFormComponent,
    TableComponent,
    TiposFormComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    UtilModule,

    ToastModule,
    FileUploadModule,
    RippleModule,
    ProgressSpinnerModule,
    ProgressBarModule,
    TableModule,
    CardModule,
    ToolbarModule,
    TooltipModule,
    AccordionModule,
    AutoCompleteModule,

    MatTabsModule,
    Ng2SearchPipeModule,
    MatStepperModule,
    NgxMaskModule,
    NgxCurrencyModule.forRoot(customCurrencyMaskConfig),
  ],
  exports:[
    ClienteFormComponent,
    EstoqueFormComponent,
    ModalPessoalComponent,
    ModalProductsComponent,
    ModalProductDadosComponent,
    ModalDebitarComponent,
    ModalMovitionComponent,
    ModalDolarFormComponent,
    ModalAlterPasswordComponent,
    FilterFormComponent,
    TimerComponent,
    FormaPagamentoFormComponent,
    TableComponent,
    TiposFormComponent
  ],
  entryComponents: [
    ClienteFormComponent,
    EstoqueFormComponent,
    ModalPessoalComponent,
    ModalProductsComponent,
    ModalProductDadosComponent,
    ModalDebitarComponent,
    ModalMovitionComponent,
    ModalDolarFormComponent,
    ModalAlterPasswordComponent,
    FilterFormComponent,
    TimerComponent,
    FormaPagamentoFormComponent,
    TableComponent,
    TiposFormComponent
  ],
})
export class ComponentsModule { }
