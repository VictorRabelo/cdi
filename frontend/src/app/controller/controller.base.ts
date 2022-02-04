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
    
    isEntregador() {
        return this.currentUser && this.currentUser.role === Role.entregador;  
    }
    
    isVendedor() {
        return this.currentUser && this.currentUser.role === Role.vendedor;  
    }

    getExtensionFileName(img: any): string {
        const parts: string[] = img.split(/[\.]/g);
        return parts[parts.length -1];
    }

    getMessage(): string{
        
        let d = new Date();
        let hour = d.getHours();
        
        if(hour < 5) {
            return "Uma Ótima Madrugada!";
        }
        
        if(hour < 8) {
            return "Uma Ótimo Dia!";
        }
    
        
        if(hour < 12) {
            return "Uma Ótimo Dia!";
        }
    
        if(hour < 18) {
            return "Uma Ótima Tarde!";
        } else {
            return "Uma Ótima Noite!";
        }
    }

    downloadPDF(file, data, type) {
    
        let fileName = data +'_'+ type + '.pdf';
        const bytes: Uint8Array = this.base64ToArrayBuffer(file);
        const filePdf: Blob = new Blob([bytes], {type: 'application/pdf'});
        let fileURL = URL.createObjectURL(filePdf);
    
        let link = document.createElement("a");
        link.href = fileURL;
        link.download = fileName;
        document.body.append(link);
    
        link.click();
        link.remove();
        // in case the Blob uses a lot of memory
        window.addEventListener('focus', e=>URL.revokeObjectURL(fileURL), {once:true});
    }
    
    base64ToArrayBuffer(base64: string): Uint8Array {
        var binaryString = window.atob(base64);
        var binaryLen = binaryString.length;
        var bytes = new Uint8Array(binaryLen);
        for (var i = 0; i < binaryLen; i++) {
          var ascii = binaryString.charCodeAt(i);
          bytes[i] = ascii;
        }
        return bytes;
    }
}