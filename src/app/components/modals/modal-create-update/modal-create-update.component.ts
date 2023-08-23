import { Component, EventEmitter, Input, OnInit, Output, TemplateRef, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ModalDismissReasons, NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-modal-create-update',
  templateUrl: './modal-create-update.component.html',
  styleUrls: ['./modal-create-update.component.scss']
})
export class ModalCreateUpdateComponent implements OnInit {

  @ViewChild("modalCreateUpdate", { static: false }) modalCreateUpdate: TemplateRef<any>;
  @Input() modal: {titulo: any, texto: any};
  @Input() loading: boolean;
  @Output() submit: EventEmitter<any> = new EventEmitter<any>();

  //@Output() confirmEvent = new EventEmitter<string>();
  
  modalForm:  FormGroup;
  titulo: string;
  texto: string;
  modalOpen: boolean = false;
  closeResult: string;
  formJson: any [] = [];
  loadingDataValidation: boolean = false; 
  
  constructor(private modalService: NgbModal, private fb: FormBuilder) { }
  model: any = {};
  ngOnInit(): void {
    this.titulo = this.modal.titulo;
    this.texto = this.modal.texto;
  }
  


  get value() {
    return this.modalForm.value;
  }
  
  createControl() {
    const group = this.fb.group({});
    this.formJson.forEach(field => {
      if (field.type === "button") return;
        const control = this.fb.control(
        field.value,
        this.bindValidations(field.validations || [], field)
      );
      group.addControl(field.name, control);
    });
    return group;
  }
  
  bindValidations(validations: any, field: any) {

    if (validations.length > 0) {
      if(field['inputType'] == 'email'){
      }
      const validList = [];
        if(validations[0]['name'] == 'required'){
          validList.push(Validators.required);
        }
        if(field['inputType']=='email'){
          validList.push(Validators.email);
        }
        /* if(field['inputType']=='text'){
          validList.push(Validators.pattern('[a-zA-Z][a-zA-Z ]+[a-zA-Z]$'));
        } */
        if(field['inputType']=='number'){
          validList.push(Validators.pattern('[0-9]+'));
        }
        if(field['length']>0){
          validList.push(Validators.maxLength(field['length']));
        }
      return Validators.compose(validList);
    }
    return null;
  }

  openModal(form) {
    this.formJson = form;    
    this.modalForm = this.createControl();
    this.loadingDataValidation = true;
    this.modalService.open(this.modalCreateUpdate, { 
      size: 'lg',
      ariaLabelledBy: 'modalCreateUpdate',
      centered: true,
      windowClass: 'theme-modal confirmacion modalConfirm',
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
  
  confirmData(){
    //Send data
  }
  
  onSubmit(event: Event) {
    event.preventDefault();
    event.stopPropagation();
    if (this.modalForm.valid) {
      this.submit.emit(this.modalForm.value);
    } else {
      this.validateAllFormFields(this.modalForm);
    }
  }
  
  validateAllFormFields(formGroup: FormGroup) {
    Object.keys(formGroup.controls).forEach(field => {
      const control = formGroup.get(field);
      control.markAsTouched({ onlySelf: true });
    });
  }

  closeModal(){
    this.modalService.dismissAll();
  }
}
