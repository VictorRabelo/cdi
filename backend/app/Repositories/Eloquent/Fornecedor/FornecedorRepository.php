<?php

namespace App\Repositories\Eloquent\Fornecedor;

use App\Models\Fornecedor;
use App\Repositories\Contracts\Fornecedor\FornecedorRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;

class FornecedorRepository extends AbstractRepository implements FornecedorRepositoryInterface
{
    protected $model = Fornecedor::class;
}