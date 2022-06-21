import { ModalCrearTicketComponent } from './../modals/modal-crear-ticket/modal-crear-ticket.component';
import { Component, OnInit, ViewChild } from '@angular/core';
import { faSearchPlus } from '@fortawesome/free-solid-svg-icons';
import { Router } from '@angular/router';
import { TicketService } from 'src/app/services/ticket.service';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {

  @ViewChild(ModalCrearTicketComponent) public crearTicketModal: ModalCrearTicketComponent;

  tickets:any;
  faDetail = faSearchPlus;
  token:any;
  settings = {
    actions: {
      edit: null,
      add: null,
      delete:null,
      custom: [
        {
          name: 'Ver Detalle',
          title: 'Ver Detalle',
        },
      ],
    },
    columns: {
      id: {
        title: 'id'
      },
      titulo: {
        title: 'Titulo'
      },
      dispositivo: {
        title: 'Dispositivo'
      },
      fecha: {
        title: 'Fecha'
      },
      username: {
        title: 'Usuario'
      },
      estado: {
        title: 'Estado'
      },
    }
  };

  loadingTickets: boolean = false
  constructor(private TicketService:TicketService, private router: Router, private AuthService: AuthService) { 
    this.AuthService.currentUserSubject.subscribe((val)=>{
      if (val && val['token']) {
        this.token = val['token']
      }
    })
  }

  ngOnInit(): void {
    this.getTickets()
  }

  getTickets(){
    this.loadingTickets = true;
    this.TicketService.getTickets(this.token).subscribe( (res)=> {
      this.tickets = res['tickets'];
      this.loadingTickets = false;
    })
  }

  navigateDetail(id){
    this.router.navigate(['ticketDetail/'+id.data.id])
  }

  openModal(){
    this.crearTicketModal.openModal();
  }
}
