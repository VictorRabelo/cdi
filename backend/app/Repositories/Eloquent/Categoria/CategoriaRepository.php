<?php

namespace App\Repositories\Eloquent\Categoria;

use Illuminate\Support\Facades\DB;

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
    
    public function categoria(){
        $sql = 'SELECT * FROM `categorias` GROUP BY categoria ORDER BY categoria ASC';
        $categoria = DB::select($sql);

        return $categoria;
    }
    
    public function subcategoria(){
        $sql = 'SELECT * FROM `categorias` GROUP BY subcategoria ORDER BY subcategoria ASC';
        $subcategoria = DB::select($sql);

        return $subcategoria;
    }
}