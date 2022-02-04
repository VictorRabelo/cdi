<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespesaEntrega extends Model
{
    protected $table = 'despesa_entregas';
    protected $primaryKey = 'id_despesaEntrega';

    public $timestamps = true;
    
    protected $fillable = [
        'data', 'valor', 'descricao','entregador_id'
    ];

    protected $hidden = [];

    protected $casts = [
        'data' => 'date:d-m-Y',
    ];

}
