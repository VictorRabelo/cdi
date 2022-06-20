<?php

namespace App\Repositories\Eloquent\FormaPagamento;

use App\Models\FormaPagamento;
use App\Repositories\Contracts\FormaPagamento\FormaPagamentoRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;

class FormaPagamentoRepository extends AbstractRepository implements FormaPagamentoRepositoryInterface
{
    /**
     * @var FormaPagamento
    */
    protected $model = FormaPagamento::class;

    public function index()
    {
        return $this->model->orderBy('name', 'asc')->get();
    }
    
    public function create($dados)
    {
        $dados = $this->model->where('telefone', $dados['telefone'])->first();

        if($dados){
            return false;
        }

        return $this->store($dados);
    }
}