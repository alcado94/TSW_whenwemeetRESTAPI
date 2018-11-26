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

  constructor(private pollService: PollService, private loginService: LoginService) { }

  ngOnInit() {
    this.pollService.getAll().subscribe(res => {
      console.log(res);
      this.arrayCard = res as Card[];
    }, err => {
      console.log(err);
    });

    this.loginService.getUser().subscribe(res => {
      this.user = res;
    }, err => {
      console.log(err);
    });

  }

}
