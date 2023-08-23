import { Component, OnInit, Input } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Component({
  selector: 'app-text-box',
  templateUrl: './text-box.component.html',
  styleUrls: ['./text-box.component.scss']
})
export class TextBoxComponent implements OnInit {

  @Input() prop:any = {};
  @Input() model:any;
  @Input() contactForm: FormGroup;

  constructor() {}

  ngOnInit(): void {
  }

}
