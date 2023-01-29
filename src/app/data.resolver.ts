import { Injectable } from '@angular/core';
import { DataService } from './data.service';
import {
  Router, Resolve,
  RouterStateSnapshot,
  ActivatedRouteSnapshot, ActivatedRoute
} from '@angular/router';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class DataResolver implements Resolve<boolean> {
  path: any;
  r: any;

  constructor(private dataService: DataService) { }
   
  resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean> {

   this.path = '';

   if (state.url!==undefined) {
    this.path=state.url;
   }

   this.r = this.dataService.getData(this.path).pipe(catchError(err=>{
       console.log(err);
       return of(null);
   }));

   return (this.r);
  
  }
}
