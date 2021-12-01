<?php

namespace App\Repositories\Eloquent\Estoque;

use App\Models\Estoque;
use App\Repositories\Contracts\Estoque\EstoqueRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;

class EstoqueRepository extends AbstractRepository implements EstoqueRepositoryInterface
{
    protected $model = Estoque::class;
}