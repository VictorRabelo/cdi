<?php

namespace App\Repositories\Contracts\Produto;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface ProdutoRepositoryInterface extends CrudRepositoryInterface
{
    public function estoque();
    public function perfumeMasculino();
    public function perfumeFeminino();
    public function pago();
    public function enviados();
    public function vendidos();
    public function storeDolarFeminino($dados);
    public function storeDolarMasculino($dados);
    public function create(Request $request);
}