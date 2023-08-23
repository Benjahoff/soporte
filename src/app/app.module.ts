import { ErrorInterceptor } from './interceptor/error.interceptor';
import { JwtInterceptor } from './interceptor/jwt.interceptor';
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
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { ModalCrearTicketComponent } from './components/modals/modal-crear-ticket/modal-crear-ticket.component';
import { NgxLoadingModule } from 'ngx-loading';
import { Ng2SmartTableModule } from 'ng2-smart-table';
import { CanDeactivateGuard } from './services/can-deactivate-guard.service';
import { SimpleNotificationsModule } from 'angular2-notifications';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ModalGenerarCompraComponent } from './components/modals/modal-generar-compra/modal-generar-compra.component';
import { ManagementComponent } from './components/management/management.component';
import { CheckboxComponent } from './components/form/elements/checkbox/checkbox.component';
import { InputComponent } from './components/form/elements/input/input.component';
import { SelectComponent } from './components/form/elements/select/select.component';
import { DynamicFormComponent } from './components/form/dynamic-form/dynamic-form.component';
import { DynamicFieldDirective } from './components/form/dynamic-field/dynamic-field.directive';
import { ButtonComponent } from './components/form/elements/button/button.component';
import { NgSelectModule } from '@ng-select/ng-select';
import { ToastrModule } from 'ngx-toastr';
import { ModalCreateUpdateComponent } from './components/modals/modal-create-update/modal-create-update.component';
import { ModalConfirmComponent } from './components/modals/modal-confirm/modal-confirm.component';
import { CheckBoxComponent } from './components/form-builder/check-box/check-box.component';
import { ControlBuilderComponent } from './components/form-builder/control-builder/control-builder.component';
import { FormBuilderComponent } from './components/form-builder/form-builder/form-builder.component';
import { RadioButtonComponent } from './components/form-builder/radio-button/radio-button.component';
import { SelectBoxComponent } from './components/form-builder/select-box/select-box.component';
import { TextBoxComponent } from './components/form-builder/text-box/text-box.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    DynamicTableComponent,
    HeaderComponent,
    LoginComponent,
    RegisterComponent,
    TicketDetailComponent,
    ModalCrearTicketComponent,
    ModalGenerarCompraComponent,
    ManagementComponent,
    ButtonComponent,
    CheckboxComponent,
    InputComponent,
    SelectComponent,
    DynamicFormComponent,
    DynamicFieldDirective,
    ModalCreateUpdateComponent,
    ModalConfirmComponent,
    CheckBoxComponent,
    ControlBuilderComponent,
    FormBuilderComponent,
    RadioButtonComponent,
    SelectBoxComponent,
    TextBoxComponent
  ],
  imports: [
    FormsModule,
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    NgbModule,
    FontAwesomeModule,
    NgxLoadingModule.forRoot({}),
    BrowserAnimationsModule,
    SimpleNotificationsModule.forRoot({}),
    Ng2SmartTableModule,
    NgSelectModule,
    ReactiveFormsModule,
    FormsModule,
    ToastrModule.forRoot({
      timeOut: 3000,
      progressBar: false,
      enableHtml: true,
    }),
  ],
  providers: [CanDeactivateGuard,
    { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true },
    { provide: HTTP_INTERCEPTORS, useClass: ErrorInterceptor, multi: true },],
  bootstrap: [AppComponent]
})
export class AppModule { }
