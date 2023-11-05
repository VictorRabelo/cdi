<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCaixa extends Model
{
    protected $table = 'tipos_caixas';
    protected $primaryKey = 'id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id','tipo'
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];
}
