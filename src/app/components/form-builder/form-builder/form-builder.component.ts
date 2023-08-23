import { Component, OnInit, Input } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Component({
  selector: 'app-form-builder',
  templateUrl: './form-builder.component.html',
  styleUrls: ['./form-builder.component.scss']
})
export class FormBuilderComponent implements OnInit {

  @Input() formJson:any = {};
  @Input() model:any;
  @Input() contactForm: FormGroup;

  constructor() { }

  ngOnInit(): void {
  }

}
