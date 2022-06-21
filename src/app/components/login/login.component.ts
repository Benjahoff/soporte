import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/services/auth.service';
import { NotificationModularService } from 'src/app/services/notification-modular.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  usuario: string = '';
  contrasenia: string = '';
  loading:boolean = false;
  dataIncorrect:boolean = false;
  constructor(private authService: AuthService, private router: Router, private _notificationService: NotificationModularService) {
    this.authService.currentUserSubject.subscribe((val)=>{
      if (val && val['token']) {
        this.router.navigate(['/home'])
      }
    })
   }

  ngOnInit(): void {
  }

  login(){
    this.loading = true;
    this.authService.login(this.usuario, this.contrasenia).subscribe(()=>{
      this.loading = false
      this.dataIncorrect = false;
      this._notificationService.onSuccess('Inicio correcto')
      this.router.navigate(['/home'])
    }, () => {
      this.loading = false
      this.dataIncorrect = true;
      this._notificationService.onError('Verifique usuario y contraseña')
    })
  }
}
