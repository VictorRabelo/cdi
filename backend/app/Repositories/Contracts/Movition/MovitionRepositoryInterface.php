<?php

namespace App\Repositories\Contracts\Movition;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface MovitionRepositoryInterface extends CrudRepositoryInterface
{
    public function index();
    public function geral();
    public function filtrarMes($dados);
    public function create($dados);
}