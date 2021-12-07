import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AsyncImageComponent } from './async-image.component';

describe('AsyncImageComponent', () => {
  let component: AsyncImageComponent;
  let fixture: ComponentFixture<AsyncImageComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AsyncImageComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AsyncImageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
