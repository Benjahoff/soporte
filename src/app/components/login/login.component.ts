import { Component, OnInit } from '@angular/core';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  usuario: string = '';
  contrasenia: string = '';
  constructor(private authService: AuthService) { }

  ngOnInit(): void {
  }

  login(){
    this.authService.login(this.usuario, this.contrasenia).subscribe(res=>console.log(res))
  }
}
