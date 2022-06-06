import { TicketService } from './../../services/ticket.service';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-ticket-detail',
  templateUrl: './ticket-detail.component.html',
  styleUrls: ['./ticket-detail.component.css']
})
export class TicketDetailComponent implements OnInit {

  ticketId: string

  ticket:any;

  renglones:any;

  userId: any;

  constructor(private route: ActivatedRoute, private TicketService: TicketService) { 
    let sessionUser = JSON.parse(localStorage.getItem('currentUser'));
    if (sessionUser) {
      this.userId = sessionUser['id'];
    }
  }

  ngOnInit(): void {
    this.ticketId = this.route.snapshot.paramMap.get('id');
    this.getTicketDetail();
  }

  getTicketDetail(){
    this.TicketService.getTicketDetail(this.ticketId).subscribe((res)=>{
      this.ticket = res['ticket'];
      this.renglones = res['renglones'];
      console.log(res);
    })
  }
}
