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

  getPollParticipate(id) {
    return this.http.get(this.url + 'poll/code/' + id);
  }

  participatePoll(form, id) {
    return this.http.put(this.url + `poll/${id}/participate`, form);
  }

  postPoll(form) {
    return this.http.post(this.url + 'poll', form, {
      responseType: 'text',
    });
  }

  modifyPoll(form, id) {
    return this.http.put(this.url + `poll/${id}`, form, {
      responseType: 'text',
    });
  }

  confirmPoll(code) {
    return this.http.get(this.url + 'code/' + code,{
      responseType: 'text',
    });
  }

}
