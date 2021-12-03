import { OnInit, AfterContentInit, AfterViewInit, OnDestroy, OnChanges } from '@angular/core';

import { User } from '@app/models/user';
import { Role } from '@app/models/role';

declare let setEvents: any;
declare let $: any;

import 'bootstrap';
import { AuthenticationService } from '@app/services/authentication.service';

export class ControllerBase implements OnInit, AfterContentInit, AfterViewInit, OnDestroy, OnChanges {
    
    public authenticationService: AuthenticationService;
    
    public currentUser: User;
    
    constructor() {}

    ngOnInit() {
        this.authenticationService.currentUser.subscribe(x => this.currentUser = x);
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

    getMessage(){
        
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