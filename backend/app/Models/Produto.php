<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Venda;
use App\Models\Estoque;
use App\Models\Categoria;
use App\Models\Data;
use App\Models\Valor;
use App\Models\Frete;
use App\Models\ProdutoProduto;
use App\Models\Fornecedor;
use Illuminate\Support\Facades\Storage;

class Produto extends Model
{
    protected $table = 'produtos';
    protected $primaryKey = 'id_produto';

    public $timestamps = false;
    
    protected $fillable = [
        'categoria_id',
        'data_id', 
        'valor_id', 
        'frete_id', 
        'fornecedor_id', 
        'img', 
        'path', 
        'name', 
        'descricao', 
        'preco', 
        'valor_total', 
        'tipo', 
        'status',
    ];

    protected $hidden = [];

    protected $casts = [];
    
    public function getPathAttribute()
    {
        if (!isset($this->attributes['path'])) {
            return null;
        }
        $path = $this->attributes['path'];
        if (Storage::disk('public')->exists($path) && isset($this->attributes['path'])) {
            return Storage::url($path);
        }
        return null;
    }

    public function vendas() {
        return $this->belongsToMany(Venda::class, 'produto_venda', 'venda_id', 'produto_id')->orderBy('created_at', 'desc');
    }

    public function estoque() {
        return $this->hasOne(Estoque::class, 'produto_id');
    }

    public function categoria() {
        return $this->hasOne(Categoria::class, 'id_categoria', 'categoria_id');
    }

    public function data() {
        return $this->hasOne(Data::class, 'id_data', 'data_id');
    }

    public function valor() {
        return $this->hasOne(Valor::class, 'id_valor', 'valor_id');
    }

    public function frete() {
        return $this->hasOne(Frete::class, 'id_frete', 'frete_id');
    }

    public function fornecedor() {
        return $this->hasOne(Fornecedor::class, 'id_fornecedor', 'fornecedor_id');
    }
}
