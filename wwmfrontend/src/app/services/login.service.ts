import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class LoginService {
  // static userLoged(): any {
  //   throw new Error("Method not implemented.");
  // }

  private url: String = 'http://localhost:80/rest/';

  constructor(private http: HttpClient) { }

  login(loginForm) {

    localStorage.setItem('currentUser', `${loginForm.login}:${loginForm.password}`);

    return this.http.get(this.url + 'user', {
      responseType: 'text',
    });

  }

  unsetLocalStorage() {
    localStorage.removeItem('currentUser');
  }

  userLoged(){
    return localStorage.getItem('currentUser');
  }
}
