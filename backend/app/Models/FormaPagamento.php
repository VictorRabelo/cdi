<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Venda;

class FormaPagamento extends Model
{
    protected $table = 'formas_pagamentos';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'descricao', 'taxa', 'parcelamento', 'observacao', 'bandeira', 'status',
    ];

    protected $hidden = [];

    protected $casts = [
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'created_at' => 'datetime:d-m-Y H:i:s',
    ];

    public function produto()
    {
        return $this->belongsTo(Venda::class, 'id_venda');
    }
}
