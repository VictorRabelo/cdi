import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MovitionComponent } from './movition.component';

describe('MovitionComponent', () => {
  let component: MovitionComponent;
  let fixture: ComponentFixture<MovitionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MovitionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MovitionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
