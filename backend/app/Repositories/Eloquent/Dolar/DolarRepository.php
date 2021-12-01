<?php

namespace App\Repositories\Eloquent\Dolar;

use App\Models\Dolar;
use App\Repositories\Contracts\Dolar\DolarRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;

class DolarRepository extends AbstractRepository implements DolarRepositoryInterface
{
    /**
     * @var Dolar
     */
    protected $model = Dolar::class;

    public function index()
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }
}
