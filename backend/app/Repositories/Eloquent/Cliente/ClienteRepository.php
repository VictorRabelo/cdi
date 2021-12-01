<?php

namespace App\Repositories\Eloquent\Cliente;

use App\Models\Cliente;
use App\Repositories\Contracts\Cliente\ClienteRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;

class ClienteRepository extends AbstractRepository implements ClienteRepositoryInterface
{
    /**
     * @var Cliente
    */
    protected $model = Cliente::class;

    public function index()
    {
        return $this->model->orderBy('name', 'asc')->get();
    }
    
    public function create($dados)
    {
        $cliente = $this->model->where('telefone', $dados['telefone'])->first();

        if($cliente){
            return false;
        }

        return $this->store($dados);
    }
}