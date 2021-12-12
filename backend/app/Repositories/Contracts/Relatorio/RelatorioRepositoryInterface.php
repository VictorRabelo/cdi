<?php

namespace App\Repositories\Contracts\Relatorio;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

interface RelatorioRepositoryInterface extends CrudRepositoryInterface
{
    public function vendas();
    public function clientes();
    public function estoque();
    public function vendidos();
    public function entregas();
    public function entregaDetalhes($id);
}