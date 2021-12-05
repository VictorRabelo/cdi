<?php

namespace App\Repositories\Contracts\Venda;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface VendaRepositoryInterface extends CrudRepositoryInterface
{
    public function index($queryParams);

    public function show($id);
    
    public function create($dados);

    public function update($dados, $id);

    public function deleteVenda($id);

    public function finishVenda($dados);

    //itens
    public function getItemById($id);

    public function createItem($dados);

    public function updateItem($dados, $id);
    
    public function deleteItem($id);
}