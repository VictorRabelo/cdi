<?php

namespace App\Repositories\Contracts\Movition;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface MovitionRepositoryInterface extends CrudRepositoryInterface
{
    public function index($queryParams);
    public function create($dados);
}