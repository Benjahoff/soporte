import { AdminGuard } from './guards/admin.guard';
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from './components/home/home.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { TicketDetailComponent } from './components/ticket-detail/ticket-detail.component';
import { AuthGuard } from './guards/auth.guard';
import { ManagementComponent } from './components/management/management.component';
import { DynamicTableComponent } from './components/dynamic-table/dynamic-table.component';

const routes: Routes = [

  {
    path: '',
    component: HomeComponent,
    data: {
      title: 'Home',
    },
    canActivate: [AuthGuard],
    children: [
      {
        path: 'home',
        component: HomeComponent,
        data: {
          title: 'Home Page',
        },
      }
    ],
  },
  {
    path: 'login',
    component: LoginComponent,
    data: {
      title: 'Login Page',
    },
  },
  {
    path: 'ticketDetail/:id',
    component: TicketDetailComponent,
    data: {
      title: 'Home Page',
    },
    canActivate: [AuthGuard],
    },
    {
      path: 'management',
      component: ManagementComponent,
      data: {
        title: 'Management',
      },
      canActivate: [AuthGuard],
    },
    {
      path: 'management/table/:table',
      component: DynamicTableComponent,
      data: {
        title: "Tablas",
        breadcrumb: "Tablas"
      },
    },
    {
      path: 'register',
      component: RegisterComponent,
      data: {
        title: 'Register Page',
      },
      canActivate: [AdminGuard],
    },
];
@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
