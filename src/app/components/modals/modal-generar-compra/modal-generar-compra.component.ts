import { Component, EventEmitter, OnInit, Output, TemplateRef, ViewChild } from '@angular/core';
import { ModalDismissReasons, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { TicketService } from 'src/app/services/ticket.service';

@Component({
  selector: 'app-modal-generar-compra',
  templateUrl: './modal-generar-compra.component.html',
  styleUrls: ['./modal-generar-compra.component.css']
})
export class ModalGenerarCompraComponent implements OnInit {
  

  @Output()
  creadoExitoso = new EventEmitter()
  @ViewChild("modalGenerarCompra", { static: false }) modalGenerarCompra: TemplateRef<any>;
  modalOpen: boolean = false;
  closeResult: string;
  userId;
  titulo:string = '';
  detalle:string = '';
  equipo:string = '';
  anydesk:string = '';
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
    this.modalService.open(this.modalGenerarCompra, { 
      size: 'lg',
      ariaLabelledBy: 'modalGenerarCompra',
      centered: true,
      windowClass: 'theme-modal modalGenerarCompra ModalGenerarCompraComponent',
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
      this.ticketService.addTicket(this.userId, this.titulo, this.equipo,this.anydesk, this.detalle)
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
