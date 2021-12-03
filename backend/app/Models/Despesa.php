<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Despesa extends Model
{
    protected $table = 'despesas';
    protected $primaryKey = 'id_despesa';

    public $timestamps = true;
    
    protected $fillable = [
        'data', 'valor', 'descricao', 'tipo',
    ];

    protected $hidden = [];

    protected $casts = [
        'data' => 'date:d-m-Y',
    ];

}
