import { DOCUMENT } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { PollService } from './../services/poll.service';
import { Component, OnInit, Inject } from '@angular/core';

@Component({
  selector: 'app-poll-participate',
  templateUrl: './poll-participate.component.html',
  styleUrls: ['./poll-participate.component.css']
})
export class PollParticipateComponent implements OnInit {

  id: number;
  poll: any = [];
  showDetail: string = undefined;

  constructor(private pollService: PollService, private route: ActivatedRoute, 
    @Inject(DOCUMENT) document) {
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

  setMobile (id, toMobile) {
    console.log(id);
    console.log(document.getElementById(id));
    console.log(document.getElementById(id + 'm'));
    if (toMobile) {
      if (document.getElementById(id + 'm').checked) {
        document.getElementById(id + 'm').checked = false;
      } else {
        document.getElementById(id + 'm').checked = true;
      }
    } else {
      if (document.getElementById(id).checked) {
        document.getElementById(id).checked = false;
      } else {
        document.getElementById(id).checked = true;
      }
      document.getElementById(id).checked = true;
    }
    
  }



}
