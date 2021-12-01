<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Venda;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    public $timestamps = false;

    protected $fillable = [
        'name', 'cpf','telefone', 'telefone_segundo'
    ];

    protected $hidden = [];

    protected $casts = [];

    public function venda() {
        return $this->hasMany(Venda::class, 'cliente_id', 'id_cliente');
    }
    
    public function vendas() {
        return $this->hasMany(Venda::class, 'cliente_id', 'id_cliente')->orderBy('pago', 'desc');
    }
}
