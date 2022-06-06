import { Component, OnInit, TemplateRef, ViewChild } from '@angular/core';
import { ModalDismissReasons, NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-modal-crear-ticket',
  templateUrl: './modal-crear-ticket.component.html',
  styleUrls: ['./modal-crear-ticket.component.css']
})
export class ModalCrearTicketComponent implements OnInit {
  
  @ViewChild("modalCrearTicket", { static: false }) modalCrearTicket: TemplateRef<any>;
  modalOpen: boolean = false;
  closeResult: string;

  constructor(private modalService: NgbModal) { }

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

}
