import { Component, OnInit } from '@angular/core';
import { FormGroup } from '@angular/forms';
import { FieldConfig } from 'src/app/components/interface/field.interface';

@Component({
  selector: 'app-checkbox',
  templateUrl: './checkbox.component.html',
  styleUrls: ['./checkbox.component.css']
})
export class CheckboxComponent implements OnInit {

  field: FieldConfig;
  group: FormGroup;

  constructor() { }

  ngOnInit(): void {
    
  }

  toogleCheck(event: any){
    if(event.target.checked){
      event.target.value = 1;
    }
    else {
      event.target.value = 0;
    }
  }

}
