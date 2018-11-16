import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';


@Injectable({
  providedIn: 'root'
})
export class PollService {

  private url: String = 'http://localhost:80/rest/';

  constructor(private http: HttpClient) { }

  getAll() {
    return this.http.get(this.url + 'poll');
  }

  getPoll(id) {
    return this.http.get(this.url + 'poll/' + id);
  }

}
