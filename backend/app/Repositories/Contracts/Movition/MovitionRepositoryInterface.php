<?php

namespace App\Repositories\Contracts\Movition;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface MovitionRepositoryInterface extends CrudRepositoryInterface
{
    public function index($queryParams);
    public function create($dados);

    //Tipos de caixa
    public function getAllItem();

    public function getItemById($id);

    public function createItem($dados);

    public function updateItem($dados, $id);
    
    public function deleteItem($id);
}