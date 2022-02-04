<?php

namespace App\Repositories\Eloquent\Dashboard;

use App\Models\Produto;
use App\Models\Venda;
use App\Models\Cliente;
use App\Models\Entrega;
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
        if (isset($request['app'])) { 
            $id = auth()->user()->id;
            $dados = Venda::where('created_at', 'LIKE', '%'.$this->dateNow().'%')->where('vendedor_id', $id)->get();
            $count = $dados->count();
            
            return $count;
            
        }
        $dados = Venda::where('created_at', 'LIKE', '%'.$this->dateNow().'%')->get();
        
        $count = $dados->count();
        
        return $count;


    }

    public function getVendasMes()
    {
        if (isset($request['app'])) { 
            $id = auth()->user()->id;
            $date = $this->dateMonth();
            $dados = Venda::whereBetween('created_at', [$date['inicio'], $date['fim']])->where('vendedor_id', $id)->get();
            $count = $dados->count();
            
            return $count;
        }

        $date = $this->dateMonth();
        $dados = Venda::whereBetween('created_at', [$date['inicio'], $date['fim']])->get();
        
        $count = $dados->count();
        
        return $count;
    }

    public function getVendasTotal()
    {
        if (isset($request['app'])) { 
            $id = auth()->user()->id;
            
            $dados = Venda::where('vendedor_id', $id)->get();
            
            $count = $dados->count();
    
            return $count;
        }

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
        if (isset($request['app'])) { 
            $userId =  auth()->user()->id;
            $date = $this->dateToday();
            $entregas = Entrega::where('entregador_id', $userId)->whereBetween('created_at', [$date['inicio'], $date['fim']])->where('status', 'pendente')->get();
            
            $produtosDisponiveis = 0;
            
            foreach ($entregas as $item) {
                $produtosDisponiveis += $item->entregasItens()->get()->count();
            }
            
            return $produtosDisponiveis;
        }
        
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