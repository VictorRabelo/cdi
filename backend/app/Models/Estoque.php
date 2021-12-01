<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produto;

class Estoque extends Model
{
    protected $table = 'estoques';
    protected $primaryKey = 'id_estoque';

    public $timestamps = false;
    
    protected $fillable = [
        'produto_id', 'und', 'pct',
    ];

    protected $hidden = [];

    protected $casts = [];

    public function produto() {
        return $this->hasOne(Produto::class, 'id_produto', 'produto_id');
    }
}
