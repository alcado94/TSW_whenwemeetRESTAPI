import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'showHourStart'
})
export class ShowHourStartPipe implements PipeTransform {

  transform(value: any, args: boolean): any {
    const hours: string[] = value.split('-');
    const hour: string[] = args ? hours[0].split(':') : hours[1].split(':');
    return `${hour[0]}:${hour[1]}`;
  }

}
