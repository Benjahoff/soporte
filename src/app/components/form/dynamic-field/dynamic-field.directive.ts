import { ComponentFactoryResolver, Directive, Input, OnInit, ViewContainerRef } from '@angular/core';
import { FormGroup } from '@angular/forms';
import { ButtonComponent } from '../elements/button/button.component';
import { CheckboxComponent } from '../elements/checkbox/checkbox.component';
import { InputComponent } from '../elements/input/input.component';
import { SelectComponent } from '../elements/select/select.component';
import { FieldConfig } from '../../interface/field.interface';



const componentMapper = {
  input: InputComponent,
  button: ButtonComponent,
  select: SelectComponent,
  checkbox: CheckboxComponent
};

@Directive({
  selector: '[dynamicField]'
})

export class DynamicFieldDirective implements OnInit {
  

  @Input() field: FieldConfig;
  @Input() group: FormGroup;

  componentRef: any;


  constructor(private resolver: ComponentFactoryResolver, private container: ViewContainerRef) { }

  ngOnInit(){
    const factory = this.resolver.resolveComponentFactory(
      componentMapper[this.field.type]
    );
    this.componentRef = this.container.createComponent(factory);
    this.componentRef.instance.field = this.field;
    this.componentRef.instance.group = this.group;
  }

}
