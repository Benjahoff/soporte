import { Component, OnInit, Input } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Component({
  selector: 'app-radio-button',
  templateUrl: './radio-button.component.html',
  styleUrls: ['./radio-button.component.scss']
})
export class RadioButtonComponent implements OnInit {

  @Input() prop:any = {};
  @Input() model:any;
  @Input() contactForm: FormGroup;

  constructor() { }
  
  ngOnInit(): void {
  }

}
