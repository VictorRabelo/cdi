<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frete extends Model
{
    protected $table = 'fretes';
    protected $primaryKey = 'id_frete';

    public $timestamps = false;
    
    protected $fillable = [
        'frete_mia_pjc', 'dolar_frete','frete_pjc_gyn', 'total_frete', 'total_frete_mia_pjc'
    ];

    protected $hidden = [];

    protected $casts = [];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
