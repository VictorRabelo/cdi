<?php

namespace App\Repositories\Contracts\Historico;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface HistoricoRepositoryInterface extends CrudRepositoryInterface
{
    public function create($dados);
}