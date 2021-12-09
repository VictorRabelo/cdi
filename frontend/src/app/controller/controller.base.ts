import { OnInit, AfterContentInit, AfterViewInit, OnDestroy, OnChanges, Injectable } from '@angular/core';

import { Role } from '@app/models/role';

declare let setEvents: any;
declare let $: any;

import 'bootstrap';
import { select, Store } from '@ngrx/store';
import { currentUser } from '@app/core/selectors/auth.selector';

@Injectable({providedIn: "root"})
export class ControllerBase implements OnInit, AfterContentInit, AfterViewInit, OnDestroy, OnChanges {
    public currentUser: any = {};
    public store: Store<any>;

    constructor() {}

    ngOnInit() {
        this.store.pipe(select(currentUser)).subscribe(res => {
            if (res) {
              this.currentUser = res;
            }
        });
    }

    ngOnChanges(){

    }
    
    ngAfterContentInit() {
    }

    ngAfterViewInit() {
        
    }

    ngOnDestroy() {
    }

    isAdmin() {
        return this.currentUser && this.currentUser.role === Role.admin;  
    }
    
    isDiretor() {
        return this.currentUser && this.currentUser.role === Role.diretor;  
    }
    
    isFinanceiro() {
        return this.currentUser && this.currentUser.role === Role.financeiro;  
    }
    
    isConsultor() {
        return this.currentUser && this.currentUser.role === Role.consultor;  
    }
    
    isClient() {
        return this.currentUser && this.currentUser.role === Role.client;  
    }

    getExtensionFileName(img: any): string {
        const parts: string[] = img.split(/[\.]/g);
        return parts[parts.length -1];
    }

    getMessage(): string{
        
        let d = new Date();
        let hour = d.getHours();
        
        if(hour < 5) {
            return "Uma Ótima Madrugada";
        }
        
        if(hour < 8) {
            return "Uma Ótimo Dia";
        }
    
        
        if(hour < 12) {
            return "Uma Ótimo Dia";
        }
    
        if(hour < 18) {
            return "Uma Ótima Tarde";
        } else {
            return "Uma Ótima Noite";
        }
    }
}