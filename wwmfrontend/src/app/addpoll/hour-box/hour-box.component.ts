import { Observable, BehaviorSubject } from 'rxjs';
import { Component, OnInit, ViewContainerRef, Output, EventEmitter, ElementRef, Input } from '@angular/core';
import { isNgTemplate } from '@angular/compiler';

@Component({
  selector: 'app-hour-box',
  templateUrl: './hour-box.component.html',
  styleUrls: ['./hour-box.component.css']
})
export class HourBoxComponent implements OnInit {

  @Output() delete = new EventEmitter();
  @Input() meeting: Meeting;

  constructor() { }

  ngOnInit() {
  }

  onClose() {
    this.delete.emit();
  }


}
