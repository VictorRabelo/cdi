<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Produto;

class Entrega extends Model
{
    protected $table = 'entregas';
    protected $primaryKey = 'id_entrega';
    
    public $timestamps = true;

    protected $fillable = [
        'id_entrega',
        'entregador_id',
        'total_final', 
        'lucro',
        'qtd_produtos',
        'status',
        'caixa',
    ];

    protected $hidden = [];

    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    public function entregador() {

        return $this->hasOne(User::class, 'id', 'entregador_id');
    }

    public function entregasItens()
    {
        return $this->hasMany(EntregaItem::class, 'entrega_id');
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'entrega_itens', 'entrega_id', 'produto_id')->orderBy('created_at', 'desc');
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
