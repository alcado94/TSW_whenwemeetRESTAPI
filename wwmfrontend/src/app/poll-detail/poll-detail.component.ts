import { PollService } from './../services/poll.service';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-poll-detail',
  templateUrl: './poll-detail.component.html',
  styleUrls: ['./poll-detail.component.css']
})
export class PollDetailComponent implements OnInit {

  id: number;
  poll: any = [];
  showDetail: string = undefined;

  constructor(private pollService: PollService, private route: ActivatedRoute) {
    this.route.params.subscribe( params => this.id = params['id'] );
  }

  ngOnInit() {
    this.pollService.getPoll(this.id).subscribe(res => {
      console.log(res);
      this.poll = res;
    }, err => {
      console.log(err);
    });
  }

  expand (diaId) {
    if (this.showDetail === diaId) {
      this.showDetail = undefined;

      document.getElementById(diaId).classList.toggle('divTableColumnScheduleShow');
      document.getElementById(diaId).classList.toggle('divTableColumnSchedule');
    } else if ( this.showDetail || this.showDetail === '0' ) {
      document.getElementById(this.showDetail).classList.toggle('divTableColumnScheduleShow');
      document.getElementById(this.showDetail).classList.toggle('divTableColumnSchedule');

      this.showDetail = diaId;

      document.getElementById(diaId).classList.toggle('divTableColumnScheduleShow');
      document.getElementById(diaId).classList.toggle('divTableColumnSchedule');
    } else {
      this.showDetail = diaId;

      document.getElementById(diaId).classList.toggle('divTableColumnScheduleShow');
      document.getElementById(diaId).classList.toggle('divTableColumnSchedule');
    }
  }

}
