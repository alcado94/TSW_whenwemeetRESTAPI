import { Directive, Input, OnInit, ElementRef, ViewContainerRef, TemplateRef, Renderer2, Inject } from '@angular/core';
import { forEach } from '@angular/router/src/utils/collection';
import { DOCUMENT } from '@angular/common';

@Directive({
  selector: '[appCheckHourState]'
})
export class CheckHourStateDirective implements OnInit {

  // tslint:disable-next-line:no-input-rename
  @Input('arrayDias') dias: Array<any>;
  // tslint:disable-next-line:no-input-rename
  @Input('key') key: number;

  private html: string;

  constructor(private elementRef: ElementRef,
    private viewContainer: ViewContainerRef,
    private renderer: Renderer2, @Inject(DOCUMENT) private document) { }

  ngOnInit() {
     this.check();
  }


  check() {
    const checkarray = [];
    // tslint:disable-next-line:forin
    for ( const estados in this.dias.values) {
      // console.log(this.dias.value[estados]);
      checkarray.push(this.dias.values[estados][this.key]);
    }

    if (checkarray.includes(1) && !checkarray.includes(0)) {
      this.elementRef.nativeElement.style.fill = 'green';
    } else if ( checkarray.includes(1) ) {
      this.elementRef.nativeElement.style.fill = 'grey';
    } else {
      this.elementRef.nativeElement.style.fill = 'red';
    }
  }
}
