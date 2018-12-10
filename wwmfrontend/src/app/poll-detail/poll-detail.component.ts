import { LoginService } from './../services/login.service';
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
  isAutor = false;
  numMembers = 0;

  constructor(private pollService: PollService, private loginService: LoginService, private route: ActivatedRoute) {
    this.route.params.subscribe( params => this.id = params['id'] );
  }

  ngOnInit() {
    this.pollService.getPoll(this.id).subscribe(res => {
      console.log(res);
      this.poll = res;
      this.numMembers = Object.keys(this.poll.participantes).length;

      this.loginService.getUser().subscribe(resAutor => {
        const user = resAutor as User;

        if (this.poll.idAutor === user.id) {
          this.isAutor = true;
        }
      });

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
      case '02':
        return 'Feb';
      case '03':
        return 'Mar';
      case '04':
        return 'Apr';
      case '05':
        return 'May';
      case '06':
        return 'Jun';
      case '07':
        return 'Jul';
      case '08':
        return 'Agu';
      case '09':
        return 'Sep';
      case '10':
        return 'Oct';
      case '11':
        return 'Nov';
      case '12':
        return 'Dec';
      default:
        break;
    }
  }


}
