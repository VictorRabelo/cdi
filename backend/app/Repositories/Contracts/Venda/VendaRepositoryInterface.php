<?php

namespace App\Repositories\Contracts\Venda;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface VendaRepositoryInterface extends CrudRepositoryInterface
{
    public function index($queryParams);

    public function create($dados);

    public function show($id);

    //itens
    public function getItemById($id);

    public function createItem($dados);

    public function updateItem($dados, $id);
    
    public function deleteItem($id);
}