import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'app-day-box',
  templateUrl: './day-box.component.html',
  styleUrls: ['./day-box.component.css']
})
export class DayBoxComponent implements OnInit {

  @Input() data: Day;
  @Output() delete = new EventEmitter();

  constructor() { }

  ngOnInit() {
    if (!this.data.id) {
      this.onAddHourClick();
    }
  }

  onAddHourClick() {
    this.data.hours.push({
      id: null,
      hourInit: '',
      hourEnd: ''
     });
  }

  remove(id) {
    this.data.hours.splice(id, 1);
  }

  onClose() {
    this.delete.emit();
  }

}
