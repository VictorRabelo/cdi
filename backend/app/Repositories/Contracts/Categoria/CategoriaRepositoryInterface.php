<?php

namespace App\Repositories\Contracts\Categoria;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface CategoriaRepositoryInterface extends CrudRepositoryInterface
{
    public function index();
    public function categoria();
    public function subcategoria();
}