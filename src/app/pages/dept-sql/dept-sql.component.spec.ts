import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DeptSqlComponent } from './dept-sql.component';

describe('DeptSqlComponent', () => {
  let component: DeptSqlComponent;
  let fixture: ComponentFixture<DeptSqlComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DeptSqlComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DeptSqlComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
