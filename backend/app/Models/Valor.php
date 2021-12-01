<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valor extends Model
{
    protected $table = 'valores';
    protected $primaryKey = 'id_valor';

    public $timestamps = false;
    
    protected $fillable = [
        'valor_site', 'dolar', 'total_site',
    ];

    protected $hidden = [];

    protected $casts = [];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
