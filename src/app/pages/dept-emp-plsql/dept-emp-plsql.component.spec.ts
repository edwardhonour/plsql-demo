import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DeptEmpPlsqlComponent } from './dept-emp-plsql.component';

describe('DeptEmpPlsqlComponent', () => {
  let component: DeptEmpPlsqlComponent;
  let fixture: ComponentFixture<DeptEmpPlsqlComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DeptEmpPlsqlComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DeptEmpPlsqlComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
