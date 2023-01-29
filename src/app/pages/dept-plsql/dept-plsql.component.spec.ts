import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DeptPlsqlComponent } from './dept-plsql.component';

describe('DeptPlsqlComponent', () => {
  let component: DeptPlsqlComponent;
  let fixture: ComponentFixture<DeptPlsqlComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DeptPlsqlComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DeptPlsqlComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
