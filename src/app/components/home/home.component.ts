import { ModalCrearTicketComponent } from './../modals/modal-crear-ticket/modal-crear-ticket.component';
import { Component, OnInit, ViewChild } from '@angular/core';
import { faSearchPlus } from '@fortawesome/free-solid-svg-icons';
import { Router } from '@angular/router';
import { TicketService } from 'src/app/services/ticket.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {

  @ViewChild(ModalCrearTicketComponent) public crearTicketModal: ModalCrearTicketComponent;
  
  tickets:any;
  faDetail = faSearchPlus;
  settings = {
    columns: {
      id: {
        title: 'id'
      },
      Titulo: {
        title: 'Titulo'
      },
      Dispositivo: {
        title: 'Dispositivo'
      },
      Fecha: {
        title: 'Fecha'
      },
      Usuario: {
        title: 'Usuario'
      },
      Estado: {
        title: 'Estado'
      },
      VerDetalle: {
        title: 'Ver detalle'
      },
    }
  };

  constructor(private TicketService:TicketService, private router: Router) { }

  ngOnInit(): void {
    this.getTickets()
  }

  getTickets(){
    this.TicketService.getTickets().subscribe( (res)=> {
      this.tickets = res['tickets'];
    })
  }

  navigateDetail(id){
    this.router.navigate(['ticketDetail/'+id])
  }

  openModal(){
    this.crearTicketModal.openModal();
  }
}
