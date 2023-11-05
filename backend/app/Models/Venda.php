<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Entrega;
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
        'entrega_id',
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
    
    protected $dates = ['created_at', 'updated_at'];
    
    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id_cliente', 'cliente_id');
    }

    public function vendedor() 
    {
        return $this->hasOne(User::class, 'id', 'vendedor_id');
    }

    public function entrega() 
    {
        return $this->hasOne(Entrega::class, 'id_entrega', 'entrega_id');
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

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s');
    }
    
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s');
    }
}
