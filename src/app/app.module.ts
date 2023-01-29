import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { LocationStrategy, HashLocationStrategy } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';


import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { DeptApiComponent } from './pages/dept-api/dept-api.component';
import { DeptSqlComponent } from './pages/dept-sql/dept-sql.component';
import { DeptPlsqlComponent } from './pages/dept-plsql/dept-plsql.component';
import { DeptEmpSqlComponent } from './pages/dept-emp-sql/dept-emp-sql.component';
import { DeptEmpPlsqlComponent } from './pages/dept-emp-plsql/dept-emp-plsql.component';
import { DeptEmpApiComponent } from './pages/dept-emp-api/dept-emp-api.component';
import { LeftNavbarComponent } from './widgets/left-navbar/left-navbar.component';
import { PageHeaderComponent } from './widgets/page-header/page-header.component';
import { PageFooterComponent } from './widgets/page-footer/page-footer.component';

@NgModule({
  declarations: [
    AppComponent,
    DeptApiComponent,
    DeptSqlComponent,
    DeptPlsqlComponent,
    DeptEmpSqlComponent,
    DeptEmpPlsqlComponent,
    DeptEmpApiComponent,
    LeftNavbarComponent,
    PageHeaderComponent,
    PageFooterComponent,
  ],
  imports: [
    BrowserModule,
    FormsModule,
    AppRoutingModule,
    HttpClientModule
  ],
  providers: [{ provide: LocationStrategy, useClass: HashLocationStrategy }],
  bootstrap: [AppComponent]
})
export class AppModule { }
