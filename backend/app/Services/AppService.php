<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Eloquent\AbstractRepository;
use App\Models\Venda;
use App\Resolvers\ApiCdiResolverInterface;
use App\Resolvers\AppResolverInterface;
use App\Utils\Messages;
use App\Utils\Tools;

class AppService extends AbstractRepository implements AppResolverInterface
{
    /**
     * @var Venda
     */
    protected $modelVenda = Venda::class;

    /**
     * @var Messages
     */
    protected $messages = Messages::class;

    /**
     * @var Tools
     */
    protected $tools = Tools::class;

    /**
     * @var ApiCdiResolverInterface
     */
    protected $baseApi = ApiCdiResolverInterface::class;

    public function getVendas($queryParams, $date){

       if(isset($queryParams['date'])) {
            if($queryParams['date'] == 0){
                $dados = Venda::with('produto', 'cliente', 'vendedor')->where('vendedor_id', $queryParams['userId'])->orderBy('id_venda', 'desc')->get();
            } else {
                $date = $this->dateFilter($queryParams['date']);
                $dados = Venda::with('produto', 'cliente', 'vendedor')->where('vendedor_id', $queryParams['userId'])->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('id_venda', 'desc')->get();
            }

        } else {
            $date = $this->dateMonth();
            $dados = $this->model->with('produto', 'cliente', 'vendedor')->where('vendedor_id', $queryParams['userId'])->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('id_venda', 'desc')->get();
        }

        return $this->tools->calculoVenda($dados);
    }

    public function getEntregas($queryParams){

    }
    
    public function getByIdVendas($queryParams){

    }

    public function getByIdEntregas($queryParams){

    }
    
    public function postVendas($request){

    }

    public function postEntregas($request){

    }
    
    public function putVendas($request){

    }

    public function putEntregas($request){

    }
    
    public function deleteVendas($request){

    }

    public function deleteEntregas($request) {

    }
}