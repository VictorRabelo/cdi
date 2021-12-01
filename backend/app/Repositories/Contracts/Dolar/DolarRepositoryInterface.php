<?php

namespace App\Repositories\Contracts\Dolar;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface DolarRepositoryInterface extends CrudRepositoryInterface
{
    public function index();
}