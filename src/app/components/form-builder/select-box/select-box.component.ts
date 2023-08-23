import { Component, OnInit, Input } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Component({
  selector: 'app-select-box',
  templateUrl: './select-box.component.html',
  styleUrls: ['./select-box.component.scss']
})
export class SelectBoxComponent implements OnInit {

  @Input() prop:any = {};
  @Input() model:any;
  @Input() contactForm: FormGroup;

  constructor() { }
  
  ngOnInit(): void {
  }

}
