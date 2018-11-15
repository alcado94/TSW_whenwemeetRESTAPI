import { PollService } from './../services/poll.service';
import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {

  arrayCard: Array<Card>;

  constructor(private pollService: PollService) { }

  ngOnInit() {
    this.pollService.getAll().subscribe(res => {
      console.log(res);
      this.arrayCard = res as Card[];
    }, err => {
      console.log(err);
    });

  }

}
