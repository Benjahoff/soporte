import { Component, OnInit } from '@angular/core';
import { FormGroup } from '@angular/forms';
import { Router } from '@angular/router';
import { FieldConfig } from 'src/app/components/interface/field.interface';

@Component({
  selector: 'app-select',
  templateUrl: './select.component.html',
  styleUrls: ['./select.component.css']
})
export class SelectComponent implements OnInit {

  field: FieldConfig;
  group: FormGroup;
  options: any[]
  
  itemsBuffer = [];
  bufferSize = 50;
  numberOfItemsFromEndBeforeFetchingMore = 10;
  loading = false;


  constructor(private route: Router) {
  }
  
  ngOnInit() {
    this.itemsBuffer = this.field['options'].slice(0, this.bufferSize);
  }

  onScrollToEnd() {
    this.fetchMore();
  }

  onScroll({ end }) {
      if (this.loading || this.field['options'].length <= this.itemsBuffer.length) {
          return;
      }
  
      if (end + this.numberOfItemsFromEndBeforeFetchingMore >= this.itemsBuffer.length) {
          this.fetchMore();
      }
  }

  private fetchMore() {
      const len = this.itemsBuffer.length;
      const more = this.field['options'].slice(len, this.bufferSize + len);
      this.loading = true;
      // using timeout here to simulate backend API delay
      setTimeout(() => {
          this.loading = false;
          this.itemsBuffer = this.itemsBuffer.concat(more);
      }, 200)
  }

  newRegister(){
    this.route.navigate([`backoffice/table/${this.field.label.toLocaleLowerCase()}/add/clientes`])
  }

  multiselectSearch(event){
    let inputText = event.target.value;
    let filteredArray = this.field['options'].filter( x => x['name'].toLowerCase().includes(inputText.toLowerCase()));  
    this.itemsBuffer = filteredArray.slice(0, this.bufferSize);
  }






}
