<?php

namespace App\Repositories\Contracts\DespesaEntrega;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface DespesaEntregaRepositoryInterface extends CrudRepositoryInterface
{
    public function index();
    
    public function movimentacao();
    
    public function create($dados);

}