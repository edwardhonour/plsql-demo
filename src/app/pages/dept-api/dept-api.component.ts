import { Component, OnInit, Input, Output, OnChanges, EventEmitter, SimpleChanges } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { DataService } from 'src/app/data.service';

@Component({
  selector: 'app-dept-api',
  templateUrl: './dept-api.component.html',
  styleUrls: ['./dept-api.component.css']
})
export class DeptApiComponent implements OnInit, OnChanges {

  formData = {
    deptno: '',
    dname: '',
    loc: '',
  }

  data: any;


  constructor(private dataService: DataService, private _activatedRoute: ActivatedRoute) { }

  ngOnChanges(): void {

  }

  ngOnInit(): void {

  }

  getDepartmentData() {
      this.dataService.getRESTData('assets/data/get_department_data.php',this.formData['deptno']).subscribe((data: any)=>{
           this.formData['dname'] = data['DNAME'];
           this.formData['loc'] = data['LOC'];
      });
  }

}
