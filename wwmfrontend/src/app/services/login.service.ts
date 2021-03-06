import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class LoginService {

  private url: String = 'http://localhost:80/rest/';

  constructor(private http: HttpClient) { }

  login(loginForm) {

    localStorage.setItem('currentUser', `${loginForm.login}:${loginForm.password}`);

    return this.http.get(this.url + 'user', {
      responseType: 'text',
    });

  }

  getUser() {
    return this.http.get(this.url + 'userinfo');
  }

  userLoged() {
    return localStorage.getItem('currentUser');
  }

  unsetLocalStorage() {
    localStorage.removeItem('currentUser');
  }

  singUp(form) {
    return this.http.post(this.url + 'user', form);
  }
}
