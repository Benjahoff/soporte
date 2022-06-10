import { TicketService } from './../../services/ticket.service';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-ticket-detail',
  templateUrl: './ticket-detail.component.html',
  styleUrls: ['./ticket-detail.component.css']
})
export class TicketDetailComponent implements OnInit {

  ticketId: string

  ticket:any;

  renglones:any;

  estados:any;

  userId: any;
  levelUser:any;

  mensaje: string;

  loadingTicket:boolean = false;


  constructor(private route: ActivatedRoute, private TicketService: TicketService, private AuthService:AuthService) { 
    this.AuthService.currentUserSubject.subscribe((val)=>{
      if (val && val['id']) {
        this.userId = val['id']
        this.levelUser = val['level']
      }
    })
  }

  ngOnInit(): void {
    this.ticketId = this.route.snapshot.paramMap.get('id');
    this.getTicketDetail();
  }

  getTicketDetail(){
    this.loadingTicket = true;
    this.TicketService.getTicketDetail(this.ticketId).subscribe((res)=>{
      this.ticket = res['ticket'][0];
      this.renglones = res['renglones'];
      this.loadingTicket = false;
      console.log(res);
      this.estados = res['estados']
    })
  }

  sendMensaje(){
    if (this.mensaje.trim() !== '') {
      this.loadingTicket = true;
      this.TicketService.sendMensaje(this.ticketId, this.userId, this.mensaje)
      .subscribe(() =>{
        this.mensaje= ''
        this.getTicketDetail()
        });
    }
  }

  changeEstado(){
    this.TicketService.changeTicketStatus(this.ticketId, this.ticket.estadoId, this.userId).subscribe((res)=>{
      this.getTicketDetail();
    })
  }
}
