import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DeptApiComponent } from './dept-api.component';

describe('DeptApiComponent', () => {
  let component: DeptApiComponent;
  let fixture: ComponentFixture<DeptApiComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DeptApiComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DeptApiComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
