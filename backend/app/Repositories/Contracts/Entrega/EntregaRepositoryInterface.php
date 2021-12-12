<?php

namespace App\Repositories\Contracts\Entrega;

use App\Repositories\Contracts\CrudRepositoryInterface;

interface EntregaRepositoryInterface extends CrudRepositoryInterface
{
    public function index($queryParams);

    public function show($id);
    
    public function create($dados);

    public function update($dados, $id);

    public function deleteEntrega($id);

    public function finishEntrega($dados);
    
    public function baixaEntrega($dados, $id);

    //itens
    public function getItemById($id);

    public function createItem($dados);

    public function updateItem($dados, $id);
    
    public function deleteItem($id);
}