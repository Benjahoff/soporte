import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class TicketService {

  constructor(private http: HttpClient) { }

  getTickets(){
    return this.http
    .get<any>(`${environment.endpoint}/getTicketsTabla`)
  }

  getTicketDetail(ticketId: string) {
    return this.http
    .get<any>(`${environment.endpoint}/getTicketDetalle/`+ticketId)  }
}
