import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class TicketService {
  
  management(){
    return this.http.get(`${environment.endpoint}/backoffice/management`);
  }


  constructor(private http: HttpClient) { }

  getTickets(token){
    let data ={
      token:token
    }
    return this.http
    .post<any>(`${environment.endpoint}/getTicketsTabla`, data)
  }

  getTicketDetail(ticketId: string) {
    return this.http
    .get<any>(`${environment.endpoint}/getTicketDetalle/`+ticketId)
  }

  addTicket(userId, titulo, equipo, anydesk, detalle){
    let data = {
      userId: userId,
      titulo: titulo,
      equipo: equipo,
      anydesk: anydesk,
      detalle: detalle
    }
    return this.http
    .post<any>(`${environment.endpoint}/addTicket`, data)
  }

  changeTicketStatus(ticketId, estadoId, user_id) {
    let data = {
      ticketId:ticketId,
      estadoId: estadoId,
      user_id: user_id,
    }
    return this.http
    .post<any>(`${environment.endpoint}/changeTicketStatus`, data)  }

  sendMensaje(ticket_id, user_id, detalle){
    let data = {
      ticket_id: ticket_id,
      user_id: user_id,
      detalle: detalle,
    }
    return this.http
    .post<any>(`${environment.endpoint}/sendMensaje`, data)
  }
}
