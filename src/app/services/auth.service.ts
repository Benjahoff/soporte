import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { User } from '../model/user';
import { map } from 'rxjs/operators';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  public currentUserSubject: BehaviorSubject<User>
  public currentUser: Observable<User>
  private tokenRefreshLoad: boolean

  constructor(private http: HttpClient) {
    this.currentUserSubject = new BehaviorSubject<User>(
      JSON.parse(localStorage.getItem('currentUser'))
    )
    this.currentUser = this.currentUserSubject.asObservable()
    this.tokenRefreshLoad = false
  }

  public get currentUserValue(): User {
    return this.currentUserSubject.value
  }

  validateTokenExpiration() {
    if (this.currentUserValue) {
      const expire = parseInt(localStorage.getItem('expire'))
      if (expire > Date.now()) {
        if (new Date(expire - Date.now()).getMinutes() < 10) {
          if (this.tokenRefreshLoad === false) {
            this.refreshToken()
          }
        }
      } else {
        this.logout()
      }
    }
  }

  login(username: string, password: string) {
    let data = {
      username: username,
      password: password
    }
    return this.http
      .post<any>(`${environment.endpoint}/auth`, data )
      .pipe(
        map((resp) => {
          // store user details and jwt token in local storage to keep user logged in between page refreshes
          localStorage.setItem('currentUser', JSON.stringify(resp['user']))
          this.currentUserSubject.next(resp['user'])
          this.startRefreshTokenTimer()
          return resp['user']
        })
      )
  }

  updateUser(user: any) {
    return this.http.put(`${environment.endpoint}/web/updateUser`, user)
  }

  logout() {
    // remove user from local storage to log user out
    localStorage.removeItem('currentUser')
    localStorage.removeItem('expire')
    localStorage.removeItem('extranetSelectedClient')
    this.currentUserSubject.next(null)
  }


  refreshToken() {
    this.tokenRefreshLoad = true
    this.http.post<any>(`${environment.endpoint}/refresh-token`, {}).subscribe(
      (resp) => {
        localStorage.setItem('currentUser', JSON.stringify(resp['user']))
        this.currentUserSubject.next(resp['user'])
        this.startRefreshTokenTimer()
        this.tokenRefreshLoad = false
      },
      (err) => {
        this.tokenRefreshLoad = false
      }
    )
  }

  private startRefreshTokenTimer() {
    // parse json object from base64 encoded jwt token
    const jwtToken = JSON.parse(atob(this.currentUserValue.token.split('.')[1]))
    const expires = new Date(jwtToken.exp * 1000)
    localStorage.setItem('expire', expires.getTime().toString())
  }
}
