import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PollParticipateComponent } from './poll-participate.component';

describe('PollParticipateComponent', () => {
  let component: PollParticipateComponent;
  let fixture: ComponentFixture<PollParticipateComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PollParticipateComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PollParticipateComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
