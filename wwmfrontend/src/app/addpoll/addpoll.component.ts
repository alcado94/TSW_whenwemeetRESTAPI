import { Router } from '@angular/router';
import { PollService } from './../services/poll.service';
import { FormGroup, FormBuilder, FormControl, Validators } from '@angular/forms';
import { DayBoxComponent } from './day-box/day-box.component';
import { Component, OnInit, ViewContainerRef, ComponentFactoryResolver, ViewChild, ComponentRef } from '@angular/core';

@Component({
  selector: 'app-addpoll',
  templateUrl: './addpoll.component.html',
  styleUrls: ['./addpoll.component.css']
})
export class AddpollComponent implements OnInit {

  myForm: FormGroup;
  titleCtrl: FormControl;

  listDays: Day[] = [];

  constructor(private fb: FormBuilder, private pollService: PollService, private router: Router) { }

  ngOnInit() {
    this.onAddDayClick();

    this.titleCtrl = new FormControl('', Validators.required);

    this.myForm = this.fb.group({
      title: this.titleCtrl
    });
  }

  onAddDayClick() {
    this.listDays.push({
      day: '',
      hours: []
    });

  }

  remove(id) {
    this.listDays.splice(id, 1);
  }

  submit() {

    const data = this.myForm.value;
    data['day'] = this.listDays;

    const toret = JSON.parse(JSON.stringify(data));

    toret['day'].forEach((element, index) => {

      toret['day'][index][0] = element['day'];
      delete toret['day'][index]['day'];

      element['hours'].forEach((elem2, index2) => {
        toret['day'][index][index2 + 1] = elem2;
      });

      delete toret['day'][index]['hours'];
    });

    this.pollService.postPoll(data).subscribe(res => {
      this.router.navigate(['/dashboard']);
    }, err => {
      console.log(err);
    });

  }
}
