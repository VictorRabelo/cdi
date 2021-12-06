<?php

namespace App\Repositories\Contracts\Estoque;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface EstoqueRepositoryInterface extends CrudRepositoryInterface
{
    public function index($queryParams);
    public function getById($id);
    public function create(Request $request);
    public function updateEstoque($dados, $id);
    public function deleteEstoque($id);
}