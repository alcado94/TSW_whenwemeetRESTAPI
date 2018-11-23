import { FormBuilder, FormGroup, FormControl } from '@angular/forms';
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

  index = 0;
  diaSave = 0;

  myForm: FormGroup;

  constructor(private pollService: PollService, private route: ActivatedRoute,
    @Inject(DOCUMENT) document, private fb: FormBuilder) {
    this.route.params.subscribe( params => this.id = params['id'] );
  }

  ngOnInit() {

    this.myForm = this.fb.group({});

    this.pollService.getPoll(this.id).subscribe(res => {
      console.log(res);
      this.poll = res;
      console.log(this.poll.diasId);
      this.poll.diasId.forEach(control => this.myForm.addControl(control, new FormControl()));
        //

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
    /*
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
    }*/
  }

  getId() {

    const toret = this.index;

    this.index++;

    if ( this.poll.diasId.length === this.index ) {
      this.index = 0;
    }

    return toret;
  }

  submitHandler() {
    const formValue = this.myForm.value;
    const toret = {};

    console.log(formValue);
/*
    for (const elem in this.myForm.controls) {
      console.log(this.myForm.controls[elem]);
      if (elem.checked) {
        toret[elem.toString()] = '1';
      }
    }*/

    const form = {
      'participateDate' : toret
    };


    this.pollService.participatePoll(form, this.poll.id).subscribe( res => {
      console.log(res);
    }, err => {
      console.log(err);
    });

    console.log(form);
  }



}
