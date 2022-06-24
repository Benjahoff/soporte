import { Component, EventEmitter, OnInit, Output, TemplateRef, ViewChild } from '@angular/core';
import { ModalDismissReasons, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { TicketService } from 'src/app/services/ticket.service';

@Component({
  selector: 'app-modal-crear-ticket',
  templateUrl: './modal-crear-ticket.component.html',
  styleUrls: ['./modal-crear-ticket.component.css']
})
export class ModalCrearTicketComponent implements OnInit {
  

  @Output()
  creadoExitoso = new EventEmitter()
  @ViewChild("modalCrearTicket", { static: false }) modalCrearTicket: TemplateRef<any>;
  modalOpen: boolean = false;
  closeResult: string;
  userId;
  titulo:string = '';
  detalle:string = '';
  equipo:string = '';
  camposCompletos:boolean = true;
  constructor(private modalService: NgbModal, private ticketService:TicketService) {
    let sessionUser = JSON.parse(localStorage.getItem('currentUser'));
    if (sessionUser) {
      this.userId = sessionUser['id'];
    }
   }

  ngOnInit(): void {
  }


  openModal() {
    this.modalService.open(this.modalCrearTicket, { 
      size: 'lg',
      ariaLabelledBy: 'modalCrearTicket',
      centered: true,
      windowClass: 'theme-modal modalCrearTicket modalCrearTicketComponent',
      backdrop : 'static',
    }).result.then((result) => {
      this.modalOpen = true;
      `Result ${result}`
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }


  private getDismissReason(reason: any): string {
    if (reason === ModalDismissReasons.ESC) {
      return 'by pressing ESC';
    } else if (reason === ModalDismissReasons.BACKDROP_CLICK) {
      return 'by clicking on a backdrop';
    } else {
      return `with: ${reason}`;
    }
  }

  ngOnDestroy() {
    if(this.modalOpen){
      this.modalService.dismissAll();
    }
  }

  closeModal(){
    this.modalService.dismissAll();
  }

  crearTicket(){
    if (this.titulo.length > 4 && this.detalle.length > 4 && this.equipo.length > 4) {
      this.camposCompletos = true;
      this.ticketService.addTicket(this.userId, this.titulo, this.equipo, this.detalle)
      .subscribe(() =>{
        this.detalle = '';
        this.titulo = '';
        this.equipo = '';
        this.creadoExitoso.emit();
        this.closeModal()});
      }else{
        this.camposCompletos = false;
      }
    }

}
