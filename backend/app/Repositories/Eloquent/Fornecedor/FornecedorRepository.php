<?php

namespace App\Repositories\Eloquent\Fornecedor;

use App\Models\Fornecedor;
use App\Repositories\Contracts\Fornecedor\FornecedorRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;

class FornecedorRepository extends AbstractRepository implements FornecedorRepositoryInterface
{
    /**
     * @var Fornecedor
     */
    protected $model = Fornecedor::class;

    public function index(){
        $dados = $this->model->orderBy('fornecedor', 'asc')->get();
        
        if(!$dados){
            return false;
        }

        return $dados;
    }
}