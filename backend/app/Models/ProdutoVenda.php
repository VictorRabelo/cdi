<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Venda;
use App\Models\Produto;

class ProdutoVenda extends Model
{
    protected $table = 'produto_venda';
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'id',
        'venda_id',
        'produto_id',
        'qtd_venda',
        'lucro_venda',
        'preco_venda'
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
