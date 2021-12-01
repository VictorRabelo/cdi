<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    protected $table = 'datas';
    protected $primaryKey = 'id_data';

    public $timestamps = false;
    
    protected $fillable = [
        'data_pedido', 'data_gyn', 'data_pjc', 'data_miami', 'data_vendas'
    ];

    protected $hidden = [];

    protected $casts = [
        'data_pedido' => 'datetime:d-m-Y H:0i',
        'data_gyn' => 'datetime:d-m-Y H:0i',
        'data_pjc' => 'datetime:d-m-Y H:0i',
        'data_miami' => 'datetime:d-m-Y H:0i',
        'data_vendas' => 'datetime:d-m-Y H:0i',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

}
