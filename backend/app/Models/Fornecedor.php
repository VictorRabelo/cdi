<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    protected $table = 'fornecedores';
    protected $primaryKey = 'id_fornecedor';

    public $timestamps = false;
    
    protected $fillable = [
        'fornecedor', 'telefone'
    ];

    protected $hidden = [];

    protected $casts = [];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
