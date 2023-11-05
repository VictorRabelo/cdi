export class Column {
    id: string;
    header: string;
    field: string; 
    mask?: string;
    roleStatus?: Status[];
    sortIcon: boolean;
    crud: boolean;
    withImagem?: boolean;
}

export class Status {
    field: any; 
    value: any;
    color: string;
}