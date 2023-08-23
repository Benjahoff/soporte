import { Component, OnInit, ViewChild } from '@angular/core';
import { Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { ToastrService } from 'ngx-toastr';
import { FieldConfig } from 'src/app/interface/field.interface';
import { CrudService } from 'src/app/shared/service/crud.service';
import { DynamicFormComponent } from '../dynamic-form/dynamic-form.component';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.css'],
  preserveWhitespaces: true
})
export class FormComponent implements OnInit {

  @ViewChild(DynamicFormComponent) form: any;

  regConfig: FieldConfig[] = [];

  loadingForm: boolean;
  table: string; 
  id: number;
  serverId = null;
  returnTable: string;

  constructor(private _crudService: CrudService, private acRoute: ActivatedRoute, private route: Router, public translate: TranslateService, private toastrService: ToastrService) {
     
   }

  ngOnInit(): void {
    this.acRoute.params.subscribe(routeParams => {
      this.table = this.acRoute.snapshot.params.table;
      this.id = null;
      if( this.acRoute.snapshot.params.id ){
        this.id = this.acRoute.snapshot.params.id;
      }
      if( this.acRoute.snapshot.params.table_secondary ){
        this.returnTable = this.acRoute.snapshot.params.table_secondary;
      }  
      this.loadingForm = true
      this.loadForm();
    });
    
    
  }

  loadForm(){
    this._crudService.getForm(this.table, this.id).subscribe( (resp:FieldConfig[]) => {
      const form = resp['form'];
      this.serverId = resp['data']['id'];
      form.forEach(element => {
        if(element['validations'].length > 0){
          element['validations'].forEach(elem => {
            switch (elem['name']) {
              case 'required':
                elem['validator'] = Validators.required;
                break;
              case 'mail':
                elem['validator'] = Validators.pattern("^[a-z0-9._%+-]+@[a-z0-9.-]+.[a-z]{2,4}$");
                break;            
              default:
                break;
            }
          });          
        }
        if((element['type'] === 'select')&& (this.returnTable)){
          element['addButton'] = false;
        }
      });
      this.regConfig = form;
      this.form = DynamicFormComponent;
      this.loadingForm = false;
    }, err => {
      console.warn('Error inesperado', err);
      this.route.navigate([`backoffice/table/${this.table}/data`]);
    });
  }

  submit(event: any){
    this.form.buttonLoading();
    if(this.serverId != -1){
      this.updateElement(event);
    }
    else {
      this.createElement(event);
    }

    
  }

  createElement(event){
    this._crudService.addRegister(this.table, event).subscribe( resp => {
      this.toastrService.success(this.translate.instant('createSucces'))  
      this.form.buttonLoading();
      if(this.returnTable){
        this.route.navigate([`backoffice/table/${this.returnTable}/add`]);
      }
      else {
        this.route.navigate([`backoffice/table/${this.table}/data`]);
      }
    }, err => {
      this.toastrService.error(this.translate.instant('createError'))
      this.form.buttonLoading();
      console.warn(err);
    })
  }

  updateElement(event){
    this._crudService.updateRegister(this.table, event, this.id).subscribe( resp => {
      this.toastrService.error(this.translate.instant('updateSucces'))
      this.form.buttonLoading();
      this.route.navigate([`backoffice/table/${this.table}/data`]);
      
    }, err => {
      this.toastrService.error(this.translate.instant('updateError'))
      this.form.buttonLoading();
      console.warn(err);
    })
  }

}
