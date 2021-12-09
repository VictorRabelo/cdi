<?php

namespace App\Repositories\Eloquent\Dolar;

use App\Models\Dolar;
use App\Repositories\Contracts\Dolar\DolarRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;

class DolarRepository extends AbstractRepository implements DolarRepositoryInterface
{
    /**
     * @var Dolar
     */
    protected $model = Dolar::class;

    public function index()
    {
        $dados = $this->model->orderBy('created_at', 'desc')->get();

        $count = 0;
        $entrada = 0;
        $saida = 0;
        $calcMedia = 0;
        $media = 0;
        
        foreach ($dados as $item) {
            if ($item->status == 'entrada') {
                $entrada += $item->montante;
            }
            
            if ($item->status == 'saida') {
                $saida += $item->montante;
            }

            if ($item->valor_dolar && $item->status == 'entrada') {
                $calcMedia += $item->valor_dolar;
                $count++;
            }
        }
        
        if($calcMedia !== 0){
            $media = $calcMedia / $count;
        }
        
        $saldo = $entrada - $saida;
        
        return ['dados' => $dados, 'media' => $media, 'saldo' => $saldo];
    }
}
