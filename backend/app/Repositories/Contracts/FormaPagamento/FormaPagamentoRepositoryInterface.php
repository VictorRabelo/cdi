<?php

namespace App\Repositories\Contracts\FormaPagamento;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface FormaPagamentoRepositoryInterface extends CrudRepositoryInterface
{
    public function index();

    public function create($dados);
}