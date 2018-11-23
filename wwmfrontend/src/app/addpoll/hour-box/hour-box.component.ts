import { ActivatedRoute } from '@angular/router';
import { Component, OnInit, Output, EventEmitter, Input } from '@angular/core';


@Component({
  selector: 'app-hour-box',
  templateUrl: './hour-box.component.html',
  styleUrls: ['./hour-box.component.css']
})
export class HourBoxComponent implements OnInit {

  id: number;

  @Output() delete = new EventEmitter();
  @Input() meeting: Meeting;

  constructor(private route: ActivatedRoute) {
    this.route.params.subscribe( params => this.id = params['id'] );
  }

  ngOnInit() {
  }

  onClose() {
    this.delete.emit();
  }


}
