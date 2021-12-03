<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dolar extends Model
{
    protected $table = 'dolars';
    protected $primaryKey = 'id_dolar';

    public $timestamps = true;
    
    protected $fillable = [
        'montante', 'valor_pago', 'valor_dolar', 'descricao', 'status',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
    ];

}
