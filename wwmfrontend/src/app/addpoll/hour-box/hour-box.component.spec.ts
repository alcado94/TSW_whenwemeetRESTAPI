import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { HourBoxComponent } from './hour-box.component';

describe('HourBoxComponent', () => {
  let component: HourBoxComponent;
  let fixture: ComponentFixture<HourBoxComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ HourBoxComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(HourBoxComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
