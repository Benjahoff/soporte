import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormGroup } from '@angular/forms';
import { FieldConfig } from 'src/app/components/interface/field.interface';


@Component({
  selector: 'app-button',
  templateUrl: './button.component.html',
  styleUrls: ['./button.component.css']
})
export class ButtonComponent implements OnInit {


  field: FieldConfig;
  group: FormGroup;
  

  constructor() {
   
  }
  
  ngOnInit() {
  
  }
  

}
