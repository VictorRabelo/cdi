<?php

namespace App\Repositories\Eloquent\Categoria;

use App\Models\Categoria;
use App\Repositories\Contracts\Categoria\CategoriaRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;

class CategoriaRepository extends AbstractRepository implements CategoriaRepositoryInterface
{
    protected $model = Categoria::class;
}