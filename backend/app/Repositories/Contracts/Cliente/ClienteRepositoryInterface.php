<?php

namespace App\Repositories\Contracts\Cliente;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface ClienteRepositoryInterface extends CrudRepositoryInterface
{
    public function index();

    public function create($dados);
}