import { Component, OnInit, Input } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Component({
  selector: 'app-control-builder',
  templateUrl: './control-builder.component.html',
  styleUrls: ['./control-builder.component.scss']
})
export class ControlBuilderComponent implements OnInit {

  @Input() prop:any = {};
  @Input() model:any;
  @Input() contactForm: FormGroup;

  constructor() { 
        
  }

  ngOnInit(): void {
  }

}
