<?php

namespace App\Repositories\Contracts\Fornecedor;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface FornecedorRepositoryInterface extends CrudRepositoryInterface
{
    public function index();
}