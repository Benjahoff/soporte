import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Ng2SmartTableComponent } from 'ng2-smart-table';
import { ToastrService } from 'ngx-toastr';
import { Observable } from 'rxjs';
import { CrudService } from 'src/app/services/crud.service';
import { ModalConfirmComponent } from '../modals/modal-confirm/modal-confirm.component';
import { ModalCreateUpdateComponent } from '../modals/modal-create-update/modal-create-update.component';


@Component({
  selector: 'app-my-test',
  templateUrl: './dynamic-table.component.html',
  styleUrls: ['./dynamic-table.component.css']
})


export class DynamicTableComponent implements OnInit {

  @ViewChild(ModalConfirmComponent) public modalDelete: ModalConfirmComponent;
  @ViewChild(ModalCreateUpdateComponent) public modalCreateUpdate: ModalCreateUpdateComponent;
  @ViewChild(Ng2SmartTableComponent) public tableNg2: Ng2SmartTableComponent;

  //Tabal de la que se muestran los datos.
  table: string;
  categories = []

  //Datos para la pestña general, ejemplo totales, columnas de la tabla etc.
  dataTableData: any[];

  //Botones para las filas de la tabla
  dataTableButtons: any[];

  //Botones para la seccion
  dataButtons: any[];

  //Datos de la tabla.
  tableGeneralData: any;
  // dtOptions: DataTables.Settings = {};
  dtOptions: any = {};

  //Registro seleccionado 
  item: any;

  deleteStatus = false;

  //DeactivateGuard 
  routeEnable: boolean;

  //flag orden tabla
  flagOrdenTabla: boolean;

  settings: {};
  modalDeleteData = { titulo: '', texto: '', accion: '' };
  modalCreateUpdateData = { titulo: '', texto: '', accion: '' };
  formProps: any[];
  data: any[];
  idRegister: number;
  loadingDataForm: boolean = false;
  constructor(private _crudService: CrudService, private acRoute: ActivatedRoute, private route: Router, private toastrService: ToastrService) {
  }

  ngOnInit(): void {
    this.acRoute.params.subscribe(routeParams => {
      this.table = routeParams.table;
      this.modalDeleteData["titulo"] = 'Eliminar Registro';
      this.modalDeleteData["texto"] = "Para eliminar el registro de " + this.table + ", debes confirmar la accion"
      this.modalCreateUpdateData["titulo"] = 'Agregar Registro';
      this.modalCreateUpdateData["texto"] = "Para agregar un registro a " + this.table + ", debes confirmar la accion"
    });
    this.getData()
    this.getDataform(this.table);
  }

  getDataform(table) {
    this.loadingDataForm = false;
    this.idRegister = null;
    this.formProps = [];
    this._crudService.getForm(table, null).subscribe(resp => {
      this.formProps = resp["form"];
      this.loadingDataForm = true;      
    }, err => {
      this.toastrService.error('Error al traer los datos')
    })
  }

  getEditDataform(table, id) {
    this.loadingDataForm = false;
    this._crudService.getDataForm(table, id).subscribe(resp => {
      this.loadingDataForm = true;
      this.formProps = resp["form"];      
      setTimeout(() => {
        this.modalCreateUpdate.openModal(this.formProps);
      }, 20);
    }, err => {
      this.toastrService.error('Error al traer los datos')
    })
  }

  getData() {
    this._crudService.getData(this.table).subscribe(resp => {
      this.settings = resp["settings"];
      this.data = resp["data"];
      this.tableGeneralData = true;
    }, err => {
      this.toastrService.error('Error al traer los datos')
    });
  }

  openDelete(event) {
    this.modalDelete.openModal();
    debugger
    this.idRegister = event.data['id'];
  }

  openCreate() {
    if(this.formProps){
      this.clearValues();
    }
    else{
      this.getDataform(this.table);
    }       
  }
  
  clearValues(){
    this.formProps.map(prop => {
      prop.value = '';
    })
    this.modalCreateUpdate.openModal(this.formProps);       
  }

  openEdit(event) {    
    this.modalCreateUpdateData["titulo"] = 'Editar Registro';
    this.modalCreateUpdateData["texto"] = "Para editar este registro de la tabla " + this.table + ", debes confirmar la accion";
    this.idRegister = event.data['id' + this.table];
    this.getEditDataform(this.table, this.idRegister)
  }

  deleteRecord() {
    this.modalDelete.confirmStatus = true;
    debugger
    this._crudService.deleteRegister(this.table, this.idRegister).subscribe(resp => {
      this.modalDelete.closeModal();
      this.modalDelete.confirmStatus = false;
      this.getData()
      this.toastrService.success('Registro borrado correctamente')
    },
      err => {
        this.toastrService.error('Error al borrar el registro')
        this.modalDelete.confirmStatus = false;
      })
  }

  createElement(event) {
    if (this.idRegister) {
      this._crudService.updateRegister(this.table, event, this.idRegister).subscribe(resp => {
        this.getData()
        this.modalCreateUpdate.closeModal()
        this.toastrService.success('Registro actualizado correctamente')
      },
        err => {
          this.toastrService.error('Error al actualizar el registro')
        })
    }
    else {
      this._crudService.addRegister(this.table, event).subscribe(resp => {
        this.getData()
        this.modalCreateUpdate.closeModal()
        this.toastrService.success('Registro creado correctamente')
      },
        err => {
          this.toastrService.error('Error al crear el registro')
        })

    }
  }
}
