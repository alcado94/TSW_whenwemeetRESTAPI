
import { ActivatedRouteSnapshot, Router, NavigationEnd } from '@angular/router';
import { Directive, HostBinding, Input, ElementRef, OnInit } from '@angular/core';

@Directive({
  selector: '[appLoginShowCurrent]'
})
export class LoginShowCurrentDirective implements OnInit {

  protected _elementClass: string[] = [];

  private route: string;

  @Input('appLoginShowCurrent') appUserLogged: string;

  @HostBinding('class')
  get elementClass(): string {
      return this._elementClass.join(' ');
  }

  constructor(private router: Router) {
    this.router.events.subscribe((val) => {
      if (val instanceof NavigationEnd) {
        this.route = val.url;
        this.setClass();
      }
    });
  }

  ngOnInit() {
    this.setClass();
  }

  setClass() {
    if ( this.route.includes(this.appUserLogged) ) {
      this._elementClass.push('current');
    } else {
      this._elementClass.shift();
    }
  }

}
