<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Produto;
use App\Models\Entrega;

class EntregaItem extends Model
{
    protected $table = 'entrega_itens';
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'id',
        'entrega_id',
        'produto_id',
        'qtd_produto',
        'qtd_disponivel',
        'lucro_entrega',
        'preco_entrega',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id', 'id_produto');
    }

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'entrega_id', 'id_entrega');
    }

}
