import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DeptEmpSqlComponent } from './dept-emp-sql.component';

describe('DeptEmpSqlComponent', () => {
  let component: DeptEmpSqlComponent;
  let fixture: ComponentFixture<DeptEmpSqlComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DeptEmpSqlComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DeptEmpSqlComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
