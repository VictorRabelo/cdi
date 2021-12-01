<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produto;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';

    public $timestamps = false;
    
    protected $fillable = [
        'categoria', 'subcategoria'
    ];

    protected $hidden = [];

    protected $casts = [];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
