<?php

namespace App\Repositories\Contracts\Dashboard;

interface DashboardRepositoryInterface
{
    public function getVendasDia();
    public function getVendasMes();
    public function getVendasTotal();
    public function getTotalClientes();
    public function getProdutosEnviados();
    public function getProdutosPagos();
    public function getProdutosCadastrados();
    public function getProdutosEstoque();
    public function getProdutosVendidos();
    public function getContasReceber();
}