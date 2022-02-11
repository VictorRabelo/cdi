<?php

namespace App\Repositories\Contracts\Dashboard;

interface DashboardRepositoryInterface
{
    public function getVendasDia($request);
    public function getVendasMes($request);
    public function getVendasTotal($request);
    public function getTotalClientes();
    public function getProdutosEnviados();
    public function getProdutosPagos();
    public function getProdutosCadastrados();
    public function getProdutosEstoque($request);
    public function getProdutosVendidos();
    public function getContasReceber();
}