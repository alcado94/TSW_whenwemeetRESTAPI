import { LoginService } from './../services/login.service';
import { FormBuilder, FormGroup, FormControl } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { PollService } from './../services/poll.service';
import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-poll-participate',
  templateUrl: './poll-participate.component.html',
  styleUrls: ['./poll-participate.component.css']
})
export class PollParticipateComponent implements OnInit {

  id: number;
  code: number;
  poll: any = [];
  showDetail: string = undefined;
  numMembers = 0;

  index = 0;
  diaSave = 0;
  isAutor = false;

  myForm: FormGroup;

  constructor(private pollService: PollService, private loginService: LoginService, private route: ActivatedRoute,
    private fb: FormBuilder, private router: Router) {

    this.route.params.subscribe(params => {
      this.id = params['id'];
      this.code = params['code'];
    });
  }

  ngOnInit() {

    this.myForm = this.fb.group({});

    if (this.id == null && this.code != null) {

      this.pollService.confirmPoll(this.code).subscribe(res => {
        const value = Number.parseInt(res);
        this.id = value;


        this.pollService.getPollParticipate(this.id).subscribe(res2 => {

          this.poll = res2;
          this.numMembers = Object.keys(this.poll.participantes).length;
          const statusMeeting = [];

          // tslint:disable-next-line:forin
          for (const key in this.poll.dias) {
            const valueT = this.poll.dias[key];
            // tslint:disable-next-line:forin
            for (const key2 in valueT) {
              if (valueT[key2][0] === 1) {
                statusMeeting.push(true);
              } else {
                statusMeeting.push(null);
              }
            }
          }
          this.loginService.getUser().subscribe(resAutor => {
            const user = resAutor as User;

            if (this.poll.idAutor === user.id) {
              this.isAutor = true;
            }
          });

          let indice = 0;

            this.poll.diasId.forEach(control => {
              this.myForm.addControl(control, new FormControl(statusMeeting[indice]));
              indice++;
            });
            console.log(this.myForm);
        });
      });
    } else {
      this.pollService.getPoll(this.id).subscribe(res => {
        this.poll = res;

        const statusMeeting = [];

        // tslint:disable-next-line:forin
        for (const key in this.poll.dias) {
          const value = this.poll.dias[key];
          // tslint:disable-next-line:forin
          for (const key2 in value) {
            if (value[key2][0] === 1) {
              statusMeeting.push(true);
            } else {
              statusMeeting.push(null);
            }
          }
        }
        this.loginService.getUser().subscribe(resAutor => {
          const user = resAutor as User;

          if (this.poll.idAutor === user.id) {
            this.isAutor = true;
          }
        });

        let indice = 0;

        this.poll.diasId.forEach(control => {
          this.myForm.addControl(control, new FormControl(statusMeeting[indice]));
          indice++;
        });

      });
    }
  }

  expand(diaId) {
    if (this.showDetail === diaId) {
      this.showDetail = undefined;

      document.getElementById(diaId).classList.toggle('divTableColumnScheduleShow');
      document.getElementById(diaId).classList.toggle('divTableColumnSchedule');
    } else if (this.showDetail || this.showDetail === '0') {
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

  setMobile(id, toMobile) {
    if (toMobile) {
      const elem = document.getElementById(id.toString() + 'm') as HTMLInputElement;
      if (elem.checked) {
        elem.checked = false;
      } else {
        elem.checked = true;
      }
    } else {
      const elem = document.getElementById(id.toString()) as HTMLInputElement;
      if (elem.checked) {
        elem.checked = false;
      } else {
        elem.checked = true;
      }
    }
  }

  getId() {

    let toret = this.index;

    this.index++;

    if (this.poll.diasId.length === this.index) {
      this.index = 0;
    }

    return toret;
  }

  submitHandler() {
    const formValue = this.myForm.value;
    const toret = {};

    // tslint:disable-next-line:forin
    for (const elem in this.myForm.controls) {
      if (this.myForm.controls[elem].value === true) {
        toret[elem] = '1';
      } else {
        toret[elem] = '0';
      }
    }



    const form = {
      'participateDate': toret
    };


    this.pollService.participatePoll(form, this.poll.id).subscribe(res => {
      this.router.navigate(['/dashboard']);
    }, err => {
      console.log(err);
    });
  }

}
