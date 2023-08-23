import { Component, OnInit, EventEmitter, Input, OnChanges, Output } from '@angular/core';
import { FormGroup, FormBuilder, Validators, FormControl } from "@angular/forms";
import { FieldConfig } from '../../interface/field.interface';

@Component({
  selector: 'dynamic-form',
  templateUrl: './dynamic-form.component.html',
  styleUrls: ['./dynamic-form.component.css']
})
export class DynamicFormComponent implements OnInit {

  @Input() fields: FieldConfig[] = [];
  @Output() submit: EventEmitter<any> = new EventEmitter<any>();
  form: FormGroup;

  constructor(private fb: FormBuilder) { 
  }
  
  get value() {
    return this.form.value;
  }
  
  ngOnInit() {
    this.form = this.createControl();
  }
  
  createControl() {
    const group = this.fb.group({});
    this.fields.forEach(field => {
      if (field.type === "button") return;
        const control = this.fb.control(
        field.value,
        this.bindValidations(field.validations || [])
      );
      group.addControl(field.name, control);
    });
    return group;
  }

  bindValidations(validations: any) {
    if (validations.length > 0) {
      const validList = [];
      validations.forEach(valid => {
        validList.push(valid.validator);
      });
      return Validators.compose(validList);
    }
    return null;
  }

  

  onSubmit(event: Event) {
    event.preventDefault();
    event.stopPropagation();
    if (this.form.valid) {
      this.submit.emit(this.form.value);
    } else {
      this.validateAllFormFields(this.form);
      console.log("Verifique los datos ingresados")
    }
  }

  buttonLoading(){
    this.fields.filter(obj => {
      //if((obj.type === "button")&& (obj.label = "Confirmar")){
      if((obj.type === "button")){
        obj['loading'] = !obj['loading'];
      }
   })
  }

  validateAllFormFields(formGroup: FormGroup) {
    Object.keys(formGroup.controls).forEach(field => {
      const control = formGroup.get(field);
      control.markAsTouched({ onlySelf: true });
    });
  }

  

}
