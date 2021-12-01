<?php

namespace App\Repositories\Contracts\Venda;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface VendaRepositoryInterface extends CrudRepositoryInterface
{
    public function index();

}