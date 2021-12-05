<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Produto;
use App\Models\Movition;
use App\Models\ProdutoVenda;

class Venda extends Model
{
    protected $table = 'vendas';
    protected $primaryKey = 'id_venda';
    
    public $timestamps = true;

    protected $fillable = [
        'vendedor_id',
        'cliente_id',
        'total_final', 
        'lucro',
        'pago',
        'pagamento',
        'qtd_produto',
        'restante',
        'status',
        'caixa'
    ];

    protected $hidden = [];

    protected $casts = [
        'updated_at' => 'datetime:d-m-Y',
        'created_at' => 'datetime:d-m-Y',
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id_cliente', 'cliente_id');
    }

    public function vendedor() {

        return $this->hasOne(User::class, 'id', 'vendedor_id');
    }

    public function produto()
    {
        return $this->belongsTo(ProdutoVenda::class, 'id_venda', 'venda_id')->orderBy('created_at', 'desc');
    }

    public function vendaItens()
    {
        return $this->hasMany(ProdutoVenda::class, 'venda_id');
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'produto_venda', 'venda_id', 'produto_id')->orderBy('created_at', 'desc');
    }

    public function movition()
    {
        return $this->hasMany(Movition::class, 'id_movition', 'venda_id');
    }
}
