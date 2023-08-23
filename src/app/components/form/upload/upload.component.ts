import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { ModalConfirmComponent } from '../../modals/modal-confirm/modal-confirm.component';
import { CrudService } from 'src/app/services/crud.service';
import { FileService } from 'src/app/services/file.service';

@Component({
  selector: 'app-upload',
  templateUrl: './upload.component.html',
  styleUrls: ['./upload.component.css']
})



export class UploadComponent {

  @ViewChild(ModalConfirmComponent) modalDelete: ModalConfirmComponent;

  table: string; 
  id: number;
  loadingButton: boolean
  files: any[] = [];
  currentFiles: any[] = null;
  hasPrimary : boolean;
  modal: {} = {titulo: null, texto: null, accion: null};
  selectedItem: number;

  constructor(private acRoute: ActivatedRoute, private _crudService: CrudService, private _fileService: FileService, private toastrService: ToastrService) { }

  ngOnInit(): void {
    this.loadingButton = false;
    this.acRoute.params.subscribe(routeParams => {
      this.table = this.acRoute.snapshot.params.table;
      this.id = this.acRoute.snapshot.params.id;
      this.getCurrentFiles();
    });
    this.modal["titulo"] = 'Eliminar Registro';
    this.modal["texto"] = "Para eliminar el registro de " + this.table + ", debes confirmar la accion"
  }

  getCurrentFiles(){
    this.currentFiles = null;
    this._fileService.currentFiles(this.table,this.id).subscribe( resp => {
      this.currentFiles = resp['files'];
      this.hasPrimary = resp['hasPrimary'];
    });
  }

  downloadFile(filename: string){
    this._fileService.download(this.table,this.id,filename)
    .subscribe(blob => {
      const a = document.createElement('a')
      const objectUrl = URL.createObjectURL(blob)
      a.href = objectUrl
      a.download = filename;
      a.click();
      URL.revokeObjectURL(objectUrl);
    })
  }

  modalDeleteData(index: number){
    this.selectedItem = index;
    this.modal["titulo"] = 'Eliminar Registro';
    this.modal["texto"] = "Para eliminar el registro de " + this.table + ", debes confirmar la accion"
    this.modalDelete.openModal();
  }

  deleteData(){
    this.modalDelete.openModal();
    const tempName = this.currentFiles[this.selectedItem].file;
    this.currentFiles = null;
    this._fileService.deleteFile(this.table,this.id,tempName)
    .subscribe( resp => {
      this.modalDelete.closeModal();
      this.getCurrentFiles();
      this.toastrService.success('deleteSucces');
    }, err => {
      this.modalDelete.closeModal();
      this.toastrService.error('deleteError')
      this.getCurrentFiles();
    });
  }

  /**
   * on file drop handler
   */
  onFileDropped($event) {
   /*  event.stopPropagation();
    event.preventDefault(); */
    this.prepareFilesList($event);
  }

  /**
   * handle file from browsing
   */
  fileBrowseHandler(files) {
    this.prepareFilesList(files);
  }

  /**
   * Delete file from files list
   * @param index (File index)
   */
  deleteFile(index: number) {
    this.files.splice(index, 1);
  }

 

  /**
   * Convert Files list to normal array list
   * @param files (Files List)
   */
  prepareFilesList(files: Array<any>) {
    for (const item of files) {
      item.progress = 0;
      this.files.push(item);
    }
  }

  /**
   * format bytes
   * @param bytes (File size in bytes)
   * @param decimals (Decimals point)
   */
  formatBytes(bytes, decimals) {
    if (bytes === 0) {
      return '0 Bytes';
    }
    const k = 1024;
    const dm = decimals <= 0 ? 0 : decimals || 2;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
  }

  uploadFiles(){
    this.loadingButton = true;
    const formData: FormData = new FormData();
    this.files.forEach(file => {
      formData.append(file.name, file, file.name);
    });
    this._fileService.uploadFiles(formData,this.table,this.id).subscribe( resp => {
      this.files = [];
      this.toastrService.success('uploadSucces')
      this.loadingButton = false;
      this.getCurrentFiles();

    }, err => {
      console.warn(err);
      this.toastrService.error('uploadError')
      this.loadingButton = false;
    });
  }

  makePrimary(index: number){
    if(this.currentFiles[index].primary){
      return;
    }
    const tempName = this.currentFiles[index].file;
    this.currentFiles = null;
    this._fileService.makePrimary(this.table,this.id,tempName).subscribe( resp => {
      this.currentFiles = resp['files'];
      this.hasPrimary = resp['hasPrimary'];
      this.toastrService.success('imageSucces')
    });
  }

 

}
