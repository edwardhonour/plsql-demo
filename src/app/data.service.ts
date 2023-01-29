import { Injectable } from '@angular/core';
import { HttpClient, HttpClientModule, HttpHeaders, HttpParams } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class DataService {

  t: any;

  constructor(private http: HttpClient) { }

  getDepartmentData(uri: any, empno: any) {
    const data = {
      user: 'ed',
      empno: empno
   }
  return this.http.post(uri,data);
  }

  getData(path: any) {
    const data = {
       user: 'ed',
       query: path
    }
    this.t = this.http.post('assets/data/index.php',data);
    return this.t;
  }

  getRESTData(uri: any, params: any) {
    const data = {
       user: 'ed',
       params: params
    }
    this.t = this.http.post(uri,data);
    return this.t;
  }

}
