import { Component, OnInit, Input, Output, EventEmitter, OnChanges, SimpleChanges } from '@angular/core';

@Component({
  selector: 'app-left-navbar',
  templateUrl: './left-navbar.component.html',
  styleUrls: ['./left-navbar.component.css']
})
export class LeftNavbarComponent implements OnInit, OnChanges {

  constructor() { }

  @Input() data: any;
 
  ngOnChanges(changes: SimpleChanges): void {
    
  }

  ngOnInit(): void {
  }

  @Output() loggingOut: EventEmitter<any> = new EventEmitter<any>();

  buttonWasClicked() {
       this.loggingOut.emit('I AM LOGGING OUT');
  }

}
