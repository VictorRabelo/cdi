<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Venda;

class Historico extends Model
{
    protected $table = 'historicos';
    protected $primaryKey = 'id_historico';
    
    public $timestamps = false;
    
    protected $fillable = [
        'produto_id', 'data', 'comentario',
    ];

    protected $hidden = [];

    protected $casts = [
        'data' => 'date:d-m-Y',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto', 'produto_id');
    }
}
