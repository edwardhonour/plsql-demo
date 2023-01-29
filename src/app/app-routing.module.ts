import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { DataGuard } from './data.guard';
import { DataResolver } from './data.resolver';
import { DeptApiComponent } from './pages/dept-api/dept-api.component';
import { DeptEmpApiComponent } from './pages/dept-emp-api/dept-emp-api.component';
import { DeptEmpPlsqlComponent } from './pages/dept-emp-plsql/dept-emp-plsql.component';
import { DeptEmpSqlComponent } from './pages/dept-emp-sql/dept-emp-sql.component';
import { DeptPlsqlComponent } from './pages/dept-plsql/dept-plsql.component';
import { DeptSqlComponent } from './pages/dept-sql/dept-sql.component';

const routes: Routes = [
  { path: '', component: DeptApiComponent },
  { path: 'dept-sql', component: DeptSqlComponent, resolve: { data: DataResolver }, canActivate: [DataGuard]  },
  { path: 'dept-plsql', component: DeptPlsqlComponent, resolve: { data: DataResolver }, canActivate: [DataGuard]  },
  { path: 'dept-emp-sql', component: DeptEmpSqlComponent, resolve: { data: DataResolver }, canActivate: [DataGuard]  },
  { path: 'dept-emp-plsql', component: DeptEmpPlsqlComponent, resolve: { data: DataResolver }, canActivate: [DataGuard]  },
  { path: 'dept-emp-api', component: DeptEmpApiComponent, resolve: { data: DataResolver }, canActivate: [DataGuard]  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
