<?php

namespace App\Repositories\Eloquent\Dashboard;

use App\Models\Produto;
use App\Models\ProdutoRepository;
use App\Models\Venda;
use App\Models\Estoque;
use App\Models\Cliente;
use App\Models\ProdutoVenda;
use App\Repositories\Eloquent\AbstractRepository;
use App\Repositories\Contracts\Dashboard\DashboardRepositoryInterface;

class DashboardRepository extends AbstractRepository implements DashboardRepositoryInterface
{
    /**
     * @var Venda
    */
    protected $modelVenda = Venda::class;
    
    /**
     * @var Produto
    */
    protected $modelProduto = Produto::class;
    
    /**
     * @var Cliente
    */
    protected $modelCliente = Cliente::class;
    
    public function getVendasDia()
    {
        $dados = Venda::where('created_at', 'LIKE', '%'.$this->dateNow().'%')->get();
        
        $this->verifica($dados);
        
        $count = $dados->count();
        
        return $count;


    }

    public function getVendasMes()
    {
        $date = $this->dateMonth();
        $dados = Venda::whereBetween('created_at', [$date['inicio'], $date['fim']])->get();
        
        $this->verifica($dados);
        
        $count = $dados->count();
        
        return $count;
    }

    public function getVendasTotal()
    {
        $dados = Venda::all();
        
        $this->verifica($dados);
        
        $count = $dados->count();

        return $count;
    }

    public function getTotalClientes()
    {
        $dados = Cliente::all();
        
        $this->verifica($dados);

        $count = $dados->count();

        return $count;
    }

    public function getProdutosEnviados()
    {
        $dados = Produto::where('status', 'pendente')->get();
        
        $this->verifica($dados);

        $count = $dados->count();

        return $count;
    }

    public function getProdutosPagos()
    {
        $dados = Produto::where('status', 'pago')->get();
        
        $this->verifica($dados);

        $count = $dados->count();

        return $count;
    }

    public function getProdutosEstoque()
    {
        $dados = Produto::where('status', 'ok')->get();
        
        $this->verifica($dados);

        $count = $dados->count();

        return $count;
    }

    public function getProdutosVendidos()
    {
        $dados = Produto::where('status', 'vendido')->get();
        
        $this->verifica($dados);

        $count = $dados->count();

        return $count;
    }

    public function getContasReceber()
    {
        $dados = Venda::where('status', 'pendente')->get();
        
        $this->verifica($dados);

        $count = $dados->count();

        return $count;
    }
}