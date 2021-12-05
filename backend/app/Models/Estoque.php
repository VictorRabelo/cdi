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
        'produto_id', 'und'
    ];

    protected $hidden = [];

    protected $casts = [];

    public function produto() {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function getIsHasUndAttribute()
    {
        if($this->attributes['und'] == 0){
            return false;
        }

        return true;
    }
}
