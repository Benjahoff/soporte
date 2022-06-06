import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { HomeComponent } from './components/home/home.component';
import { DynamicTableComponent } from './components/dynamic-table/dynamic-table.component';
import { HeaderComponent } from './components/header/header.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { TicketDetailComponent } from './components/ticket-detail/ticket-detail.component';
import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { ModalCrearTicketComponent } from './components/modals/modal-crear-ticket/modal-crear-ticket.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    DynamicTableComponent,
    HeaderComponent,
    LoginComponent,
    RegisterComponent,
    TicketDetailComponent,
    ModalCrearTicketComponent
  ],
  imports: [
    FormsModule,
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    NgbModule,
    FontAwesomeModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
