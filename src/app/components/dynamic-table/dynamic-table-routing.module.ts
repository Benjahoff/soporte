import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DynamicTableComponent } from './dynamic-table.component';
import { UploadComponent } from '../form/upload/upload.component';
import { FormComponent } from '../form/form/form.component';


const routes: Routes = [{
  path: '',
    data: {
      title: 'Tabla'
    },
  children: [
    {
      path: '',
      redirectTo: 'data'
    },
     {
      path: 'data', 
      component: DynamicTableComponent,   
      data: {
        title: 'General'
      },
      /* canDeactivate: [CanDeactivateGuard], */
    },
    
    {
      path: 'add', 
      component: FormComponent,  
      data: {
        title: 'Agregar'
      }
    },
    {
      path: 'add/:table_secondary', 
      component: FormComponent,  
      data: {
        title: 'Agregar'
      }
    },
    
    {
      path: 'update/:id', 
      component: FormComponent,  
      data: {
        title: 'Editar'
      }
    },
    {
      path: 'upload/:id', 
      component: UploadComponent,  
      data: {
        title: 'Subir'
      }
    },
  ]  
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MyTestRoutingModule { }
