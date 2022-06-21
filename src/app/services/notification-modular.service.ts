import { Injectable } from '@angular/core';
import { NotificationsService } from 'angular2-notifications';

@Injectable({
  providedIn: 'root'
})
export class NotificationModularService {

  constructor(private _notifications: NotificationsService) { }

  onSuccess(message) {
    this._notifications.success('Exitosa', message,{
      position: ['bottom','right'],
      timeOut: 5000,
      animte: 'fade',
      showProgressBar: true
    })
  }

  onInfo(message) {
    this._notifications.info('Mensaje', message,{
      position: ['bottom','right'],
      timeOut: 5000,
      animte: 'fade',
      showProgressBar: true
    })
  }

  onError(message) {
    this._notifications.error('Error', message,{
      position: ['bottom','right'],
      timeOut: 5000,
      animte: 'fade',
      showProgressBar: true
    })
  }
}
