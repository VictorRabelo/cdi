<?php

namespace App\Repositories\Contracts\User;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface UserRepositoryInterface extends CrudRepositoryInterface
{
    public function index();

    public function create($dados);
}