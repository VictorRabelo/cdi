import { Component, Input, OnInit } from '@angular/core';
import { animate, style, transition, trigger } from '@angular/animations';

@Component({
  selector: 'async-image',
  template: `
    <img [src]="img" class="produtos" alt="Foto do produto" *ngIf="!loading" [@enterAnimation]>
    <div class="card-loader" *ngIf="loading" [@enterAnimation]><i class="fas fa-sync-alt anim-rotate"></i></div>
  `,
  styles: [
    '.produtos { height: 80px; width: 80px;}',
  ],
  animations: [
    trigger(
      'enterAnimation', [
        transition(':enter', [
          style({opacity: 0}),
          animate('1000ms', style({opacity: 1}))
        ]),
        transition(':leave', [
          style({opacity: 1}),
          animate('500ms', style({transform: 'translateY(100%)', opacity: 0}))
        ])
      ]
    )
  ],
})
export class AsyncImageComponent implements OnInit {

  @Input() url: string;
  img: any;

  loading: boolean = false;

  constructor() { }

  ngOnInit(): void {
    this.loading = true;
    this.img = this.url;
    this.loading = false;
  }
}