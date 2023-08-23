import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment';


@Injectable({
  providedIn: 'root'
})
export class CrudService {


  public ENDPOINT = environment.endpoint;
  constructor(private http: HttpClient) { }

  getForm(table: string, id: number) {
    const data = {
      table: table,
      id: id
    }
    return this.http.post(`${this.ENDPOINT}/getform`, data);
  }

  getData(table: string) {
    const data = {
      table: table
    }
    return this.http.post(`${this.ENDPOINT}/backoffice/getDataTable`, data);
  }

  getDataTable(table: string) {
    const data = {
      table: table
    }
    return this.http.post(`${this.ENDPOINT}/getDataTable`, data);
  }

  getDataForm(table: any, id: number) {
    let data = {
      id: id,
      table: table
    }
    return this.http.post(`${this.ENDPOINT}/getform`, data);
  }


  updateRegister(table: string, datos: any, id: any) {
    const data = {
      table: table,
      data: datos,
      id: id
    }
    return this.http.post(`${this.ENDPOINT}/updateRegister`, data);
  }

  dataTableServerSide(table: string, dataTablesParameters: any) {
    const data = {
      table: table,
      dataTable: dataTablesParameters
    }
    return this.http.post<any>(`${this.ENDPOINT}/dataTable`, data, {})
  }

  deleteRegister(table: string, id: number) {
    return this.http.delete<any>(`${this.ENDPOINT}/deleteRegister/${table}/${id}`);
  }

  addRegister(table: string, formData: {}) {
    const data = {
      table: table,
      form: formData,
    };
    return this.http.post(`${environment.endpoint}/addRegister`, data);
  }


}
