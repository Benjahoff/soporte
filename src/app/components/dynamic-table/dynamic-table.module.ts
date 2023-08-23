import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MyTestRoutingModule } from './dynamic-table-routing.module';

import { UploadComponent } from '../form/upload/upload.component';
import { UploadDirective } from '../form/upload/upload.directive';


@NgModule({
  imports: [
    CommonModule,
    MyTestRoutingModule,
  ],
  declarations: [
    UploadComponent,
    UploadDirective,
  ],
})
export class MyTestModule { }
