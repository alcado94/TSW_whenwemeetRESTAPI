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

  mobileParticipantes (estados) {
    let checkarray = 0;

    for (let index = 0; index < estados.length; index++) {
      const ele = estados[index];
      if (ele === 1) {
        checkarray++;
      }
    }

    return checkarray;
  }

  getMonth(date: string) {
    const id = date.split('-')[1];
    switch (id) {
      case '01':
        return 'Jan';
        break;
      case '02':
        return 'Feb';
        break;
      case '03':
        return 'Mar';
        break;
      case '04':
        return 'Apr';
        break;
      case '05':
        return 'May';
        break;
      case '06':
        return 'Jun';
        break;
      case '07':
        return 'Jul';
        break;
      case '08':
        return 'Agu';
        break;
      case '09':
        return 'Sep';
        break;
      case '10':
        return 'Oct';
        break;
      case '11':
        return 'Nov';
        break;
      case '12':
        return 'Dec';
        break;
      default:
        break;
    }
  }


}
