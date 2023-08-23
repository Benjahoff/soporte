import { Component, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { TicketService } from 'src/app/services/ticket.service';
import { DynamicFormComponent } from '../form/dynamic-form/dynamic-form.component';

@Component({
  selector: 'app-management',
  templateUrl: './management.component.html',
  styleUrls: ['./management.component.css']
})
export class ManagementComponent implements OnInit {

  //Form
  @ViewChild(DynamicFormComponent) form: any;
  loadingForm: boolean;

  tables: any[];
  isOptSelected: boolean = false;
  optSelected: string;
  constructor(private ticketservice: TicketService, private route: Router) { }

  ngOnInit(): void {
    this.loadingForm = true;
    this.ticketservice.management().subscribe(resp => {
      const form = resp['form'];
      this.tables = form[0]['options'];
      this.form = DynamicFormComponent;
      this.loadingForm = false;
    })
  }

  submit(event: any) {
    let tabla = this.tables.findIndex(r => r.value === event.tablas);
    if (tabla >= 0) {
      this.route.navigate(['management', 'table', this.tables[tabla]['name']]);
    }
  }

  goToSelect() {
    this.route.navigate(['management', 'table', this.optSelected]);
  }
  
  selectOption(option) {
    if (option.name && option.value) {
      this.optSelected = option.name;
      this.isOptSelected = true;
    }
  }
  
  goHome(){
    this.route.navigate(['home']);
  }
}
