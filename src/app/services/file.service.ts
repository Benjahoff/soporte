import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { switchMap, tap } from 'rxjs/operators';
import { environment } from 'src/environments/environment';


@Injectable({
  providedIn: 'root'
})
export class FileService {

  public ENDPOINT = environment.endpoint;
  constructor(private http: HttpClient) { }

  currentFiles(table: string, id: number){
    return this.http.get<any>(`${this.ENDPOINT}/files/${table}/${id}`);
  }

  uploadFiles(files: any, table: string, id: number){
    return this.http.post<any>(`${this.ENDPOINT}/uploadFile/${table}/${id}`,files, {});
  }

  deleteFile(table: string, id: number, file: string){
    return this.http.delete<any>(`${this.ENDPOINT}/files/${table}/${id}/${file}`);
  }

  download(table: string,id: number,filename: string): Observable<Blob> {
    return this.http.get(`${this.ENDPOINT}/downloadFile/${table}/${id}/${filename}`, {
      responseType: 'blob'
    })
  }

  downloadReservation(reservationId: any): Observable<Blob> {
    return this.http.get(`${this.ENDPOINT}/reservaPDF/${reservationId}`, {
      responseType: 'blob'
    })
  }

  makePrimary(table: string,id: number,filename: string){
    return this.http.get(`${this.ENDPOINT}/filePrimary/${table}/${id}/${filename}`);
  }

  getCartPdf(idCarrito:number){
    return this.http.get(`${this.ENDPOINT}/detalleCarritoPdf/${idCarrito}`);
  }

  getFaltantes(fecha:string){
    return this.http.get(`${this.ENDPOINT}/faltantesPdf/${fecha}`);
  }

  getFactura(idCarrito:number){
    return this.http.get(`${this.ENDPOINT}/faltantesPdf/${idCarrito}`);
  }
  getBultos(idCarrito:number){
    return this.http.get(`${this.ENDPOINT}/bultosPdf/${idCarrito}`);
  }

  public get getImages(): Observable<File[]> {
    return this.images;
  }

  private get images(): Observable<File[]> {
    return of(sessionStorage.getItem('images'))
        .pipe(
            switchMap(images => {
                if (!images) {
                    return this.http.get<File[]>(this.ENDPOINT+'ecommerce/getImages')
                        .pipe(tap(res => sessionStorage.setItem('images', JSON.stringify(res))));
                }
                return of(JSON.parse(images));
            })
        );
  } 
}
