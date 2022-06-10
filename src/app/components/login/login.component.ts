import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  usuario: string = '';
  contrasenia: string = '';
  loading:boolean = false;
  constructor(private authService: AuthService, private router: Router) { }

  ngOnInit(): void {
  }

  login(){
    this.loading = true;
    this.authService.login(this.usuario, this.contrasenia).subscribe(()=>{
      this.loading = false
      this.router.navigate(['/home'])
    }, err => {
      this.loading = false
    })
  }
}
