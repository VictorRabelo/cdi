import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FormasPagamentosComponent } from './formas-pagamentos.component';

describe('FormasPagamentosComponent', () => {
  let component: FormasPagamentosComponent;
  let fixture: ComponentFixture<FormasPagamentosComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FormasPagamentosComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FormasPagamentosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
