<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movition extends Model
{
    protected $table = 'movitions';
    protected $primaryKey = 'id_movition';
    
    public $timestamps = false;
    
    protected $fillable = [
        'venda_id', 'data', 'valor', 'descricao', 'tipo', 'status',
    ];

    protected $hidden = [];

    protected $casts = [
        'data' => 'date:d-m-Y',
    ];

    public function venda()
    {
        return $this->belongsTo(Venda::class, 'id_venda', 'venda_id');
    }
}
