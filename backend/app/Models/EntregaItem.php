<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Venda;
use App\Models\Produto;

class EntregaItem extends Model
{
    protected $table = 'entrega_itens';
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'id',
        'entregador_id',
        'produto_id',
        'qtd_entrega',
        'lucro_entrega',
        'preco_entrega'
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'date:d-m-Y',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id', 'id_produto');
    }

}
