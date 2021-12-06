<?php

namespace App\Repositories\Eloquent\Categoria;

use App\Models\Categoria;
use App\Repositories\Contracts\Categoria\CategoriaRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;

class CategoriaRepository extends AbstractRepository implements CategoriaRepositoryInterface
{
    /**
     * @var Categoria
     */
    protected $model = Categoria::class;
    
    public function index(){
        $dados = $this->model->orderBy('categoria', 'asc')->orderBy('subcategoria', 'asc')->get();
        
        if(!$dados){
            return false;
        }

        return $dados;
    }
}