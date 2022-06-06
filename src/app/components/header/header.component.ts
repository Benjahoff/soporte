import { Component, OnInit } from '@angular/core';
import { faLongArrowAltRight } from '@fortawesome/free-solid-svg-icons';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {
  faLogout = faLongArrowAltRight;

  constructor() { }

  ngOnInit(): void {
  }

}
