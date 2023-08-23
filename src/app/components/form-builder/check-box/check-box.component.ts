import { Component, OnInit, Input } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Component({
  selector: 'app-check-box',
  templateUrl: './check-box.component.html',
  styleUrls: ['./check-box.component.scss']
})
export class CheckBoxComponent implements OnInit {

  @Input() prop:any = {};
  @Input() model:any;
  @Input() contactForm: FormGroup;

  constructor() {}

  ngOnInit(): void {
    if(this.prop.value){
      if (this.prop.value == '1') {
        this.contactForm.controls[this.prop.name].setValue(false);
      }else{
        this.contactForm.controls[this.prop.name].setValue(true);
      }
    }else{
      this.contactForm.controls[this.prop.name].setValue(false);
    }
  }

}
