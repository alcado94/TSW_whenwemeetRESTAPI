import { FormBuilder, FormGroup, FormControl } from '@angular/forms';
import { PollService } from './../services/poll.service';
import { Component, OnInit } from '@angular/core';
import { LoginService } from '../services/login.service';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {

  arrayCard: Array<Card>;
  user: any = '';

  notifyCheck: FormGroup;

  constructor(private pollService: PollService, private loginService: LoginService,
    private fb: FormBuilder, private userService: LoginService) { }

  ngOnInit() {

    this.notifyCheck = new FormGroup({
      notifycation: new FormControl(false)
    });

    this.pollService.getAll().subscribe(res => {
      this.arrayCard = res as Card[];
    }, err => {
      console.log(err);
    });

    this.loginService.getUser().subscribe(res => {
      this.user = res;

      this.notifyCheck.setValue({
        // Recoge el valor para las notificaciones
        notifycation: this.user.notify
      });

    }, err => {
      console.log(err);
    });

  }

  notify() {

    const value = this.notifyCheck.value['notifycation'] ? 1 : 0;

    this.pollService.notify({'notification': value}).subscribe(res => {
      this.notifyCheck.setValue({
        // Recoge el valor para las notificaciones
        notifycation: this.notifyCheck.value['notifycation']
      });
    }, err => {
      console.log(err);
    });
  }

}
