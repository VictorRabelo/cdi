<?php

namespace App\Repositories\Contracts\User;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface UserRepositoryInterface extends CrudRepositoryInterface
{
    public function index();

    public function getById($id);

    public function create($dados);
    
    public function updateUser($dados, $id);
}