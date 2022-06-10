import { AuthService } from './../../services/auth.service';
import { AfterViewInit, Component, OnInit } from '@angular/core';
import { faArrowAltCircleRight } from '@fortawesome/free-solid-svg-icons';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent {
  faLogout = faArrowAltCircleRight;

  userId: any = -1;
  constructor(private AuthService:AuthService) { 
    this.AuthService.currentUserSubject.subscribe((val)=>{
      if (val && val['id']) {
        this.userId = val['id']
      }
    })
  }

  logout(){
    this.AuthService.logout();
    window.location.reload();
  }
}
