import { Router, ActivatedRoute } from '@angular/router';
import { PollService } from './../services/poll.service';
import { FormGroup, FormBuilder, FormControl, Validators } from '@angular/forms';
import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-addpoll',
  templateUrl: './addpoll.component.html',
  styleUrls: ['./addpoll.component.css']
})
export class AddpollComponent implements OnInit {

  id: number;
  pollData;

  myForm: FormGroup;
  titleCtrl: FormControl;

  listDays: Day[] = [];

  constructor(private fb: FormBuilder, private pollService: PollService, private router: Router,
    private route: ActivatedRoute) {
    this.route.params.subscribe( params => this.id = params['id'] );
  }

  ngOnInit() {

    this.titleCtrl = new FormControl('', Validators.required);

    if (this.id) {

      this.myForm = this.fb.group({
        title: this.titleCtrl
      });

      this.pollService.getPoll(this.id).subscribe(res => {
        this.pollData = res;
        this.onDayEdit();
        this.myForm.controls['title'].setValue(this.pollData.titulo);

      }, err => {
        console.log(err);
      });
    } else {
      this.onAddDayClick();
      this.myForm = this.fb.group({
        title: this.titleCtrl
      });
    }
  }

  onAddDayClick() {
    this.listDays.push({
      id: null,
      day: '',
      hours: []
    });
  }

  onDayEdit() {

    let index = 0;
    let indexHour = 0;
    // tslint:disable-next-line:forin
    for (const key in this.pollData.dias) {
        const value = this.pollData.dias[key];

        const hourArray: Meeting[] = [];
        // tslint:disable-next-line:forin
        for (const hour in value) {
          hourArray.push({
            id: this.pollData.diasId[indexHour],
            hourInit: hour.split('-')[0],
            hourEnd: hour.split('-')[1]
          });
          indexHour++;
        }

        this.listDays.push({
          id: this.pollData.diasId[index],
          day: key,
          hours: hourArray
        });
        index++;
    }
  }

  remove(id) {
    this.listDays.splice(id, 1);
  }

  submit() {

    if (this.id) {
      const data = this.myForm.value;

      data['days'] = [];
      data['daysNew'] = {};
      this.listDays.forEach(element => {
        element.hours.forEach(elem => {
          if (elem.id) {
            data['days'].push(elem.id);
          } else {
            if (!data['daysNew'][element.day]) {
              data['daysNew'][element.day] = [];
            }
            data['daysNew'][element.day].push([elem.hourInit, elem.hourEnd]);
          }
        });
      });

      this.pollService.modifyPoll(data, this.id).subscribe(res => {
        this.router.navigate(['/dashboard']);
      }, err => {
        console.log(err);
      });

    } else {
      const data = this.myForm.value;
      data['day'] = this.listDays;

      const toret = JSON.parse(JSON.stringify(data));

      toret['day'].forEach((element, index) => {

        toret['day'][index][0] = element['day'];
        delete toret['day'][index]['day'];
        delete toret['day'][index]['id'];

        element['hours'].forEach((elem2, index2) => {
          toret['day'][index][index2 + 1] = elem2;
          delete toret['day'][index][index2 + 1]['id'];
        });

        delete toret['day'][index]['hours'];
      });

      this.pollService.postPoll(toret).subscribe(res => {
        this.router.navigate(['/dashboard']);
      }, err => {
        console.log(err);
      });
    }

  }
}
