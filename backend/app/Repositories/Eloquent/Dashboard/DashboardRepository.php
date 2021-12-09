<?php

namespace App\Repositories\Eloquent\Dashboard;

use App\Models\Produto;
use App\Models\Venda;
use App\Models\Cliente;
use App\Repositories\Eloquent\AbstractRepository;
use App\Repositories\Contracts\Dashboard\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;

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
        
        $count = $dados->count();
        
        return $count;


    }

    public function getVendasMes()
    {
        $date = $this->dateMonth();
        $dados = Venda::whereBetween('created_at', [$date['inicio'], $date['fim']])->get();
        
        $count = $dados->count();
        
        return $count;
    }

    public function getVendasTotal()
    {
        $dados = Venda::all();
        
        $count = $dados->count();

        return $count;
    }

    public function getTotalClientes()
    {
        $dados = Cliente::all();
        
        $count = $dados->count();

        return $count;
    }

    public function getProdutosEnviados()
    {
        $dados = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->where('status', 'pendente')->get();
        
        $count = $dados->count();

        return $count;
    }

    public function getProdutosPagos()
    {
        $dados = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->where('status', 'pago')->get();
        
        $count = $dados->count();

        return $count;
    }

    public function getProdutosEstoque()
    {
        $dados = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->where('status', 'ok')->get();
        
        $count = $dados->count();

        return $count;
    }
    
    public function getProdutosCadastrados()
    {
        $dados = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->get();
        
        $count = $dados->count();

        return $count;
    }

    public function getProdutosVendidos()
    {
        $dados = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->where('status', 'vendido')->get();
        
        $count = $dados->count();

        return $count;
    }

    public function getContasReceber()
    {
        $dados = Venda::where('status', 'pendente')->get();
        
        $count = $dados->count();

        return $count;
    }
}