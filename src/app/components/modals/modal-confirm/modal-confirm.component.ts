import { isPlatformBrowser } from '@angular/common';
import { Component, EventEmitter, Inject, Input, OnInit, Output, TemplateRef, ViewChild } from '@angular/core';
import { ModalDismissReasons, NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-modal-confirm',
  templateUrl: './modal-confirm.component.html',
  styleUrls: ['./modal-confirm.component.scss']
})
export class ModalConfirmComponent implements OnInit {

  @ViewChild("modalConfirm", { static: false }) modalConfirm: TemplateRef<any>;
  @Input() modal: {titulo: any, texto: any};
  @Output() confirmEvent = new EventEmitter<string>();

  closeResult: string;
  modalOpen: boolean = false;
  confirmStatus: boolean = false;
  titulo: string;
  texto: string;
  constructor( private modalService: NgbModal) {
    
   }

  ngOnInit(): void {
    this.titulo = this.modal.titulo;
    this.texto = this.modal.texto;
  }
  
  openModal() {
      this.modalService.open(this.modalConfirm, { 
        size: 'lg',
        ariaLabelledBy: 'Confirmacion-Modal',
        centered: true,
        windowClass: 'theme-modal confirmacion modalConfirm'
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

  confirmData(){
    this.confirmEvent.emit();
  }
  
  closeModal(){
    this.modalService.dismissAll();
  }
}
