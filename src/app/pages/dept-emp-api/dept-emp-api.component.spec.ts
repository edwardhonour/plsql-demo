import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DeptEmpApiComponent } from './dept-emp-api.component';

describe('DeptEmpApiComponent', () => {
  let component: DeptEmpApiComponent;
  let fixture: ComponentFixture<DeptEmpApiComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DeptEmpApiComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DeptEmpApiComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
