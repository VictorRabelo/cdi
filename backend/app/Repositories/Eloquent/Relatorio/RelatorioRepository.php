<?php

namespace App\Repositories\Eloquent\Relatorio;

use PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Repositories\Contracts\Relatorio\RelatorioRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;

use App\Utils\Messages;
use App\Utils\Tools;

use App\Models\Cliente;
use App\Models\Entrega;
use App\Models\EntregaItem;
use App\Models\ProdutoVenda;
use App\Models\DespesaEntrega;
use App\Models\Movition;
use App\Models\Venda;

class RelatorioRepository extends AbstractRepository implements RelatorioRepositoryInterface
{
    /**
     * @var Tools
     */
    protected $tools = Tools::class;

    /**
     * @var Messages
     */
    protected $messages = Messages::class;

    public function vendas()
    {
        $data_now = $this->dateNow();
        
        $user = Auth::user()->id;
        $datas = Venda::with('produtos', 'cliente')->where('vendedor_id', $user)->orderBy('id_venda', 'desc')->get();
        
        if(empty($datas)){
            return $this->messages->notFound;
        }

        $pdf = PDF::loadView('pdf.vendas', compact('datas', 'data_now'));
        $result = $pdf->download($data_now.'.pdf');
        
        $base = base64_encode($result);

        return ['file' => $base,'data' => $data_now];
    }

    public function clientes()
    {
        $data_now = $this->dateNow();
        
        $datas = Cliente::with('vendas')->get();
        if(empty($datas)){
            return $this->messages->error;
        }

        foreach ($datas as $value) {
            $value->gastos = 0;
            $value->telefone = $this->tools->getPhoneFormattedAttribute($value->telefone);
            foreach ($value->vendas as $v) {
                $value->gastos = $v->pago + $value->gastos; 
            }
        }

        $resultado = $datas->sortByDesc('gastos');
        $result = $resultado->values()->all();
        $pdf = PDF::loadView('pdf.cliente', compact('result'));
        $file = $pdf->download($data_now.'.pdf');

        $base = base64_encode($file);

        return ['file' => $base,'data' => $data_now];
    }

    public function estoque()
    {
        $data_now = $this->dateNow();

        $datas = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->join('categorias', 'categorias.id_categoria', '=', 'produtos.categoria_id')->join('datas', 'datas.id_data', '=', 'produtos.data_id')->join('valores', 'valores.id_valor', '=', 'produtos.valor_id')->join('fretes', 'fretes.id_frete', '=', 'produtos.frete_id')->join('fornecedores', 'fornecedores.id_fornecedor', '=', 'produtos.fornecedor_id')->where('produtos.status', 'ok')->where('estoques.und', '>', '0')->orderBy('name', 'asc')->get();
        if(empty($datas)){
            return $this->messages->error;
        }
        
        $pdf = PDF::loadView('pdf.estoque', compact('datas'));
        $result = $pdf->download($data_now.'.pdf');
        
        $base = base64_encode($result);

        return ['file' => $base,'data' => $data_now];
    }

    public function vendidos()
    {
        $data_now = $this->dateNow();
    
        $sql = 'SELECT `produtos`.`name` as `nameProduto`, `produtos`.`path`, `produto_venda`.*, `vendas`.`entrega_id`, SUM(`produto_venda`.`qtd_venda`) as qtdTotal from `produto_venda` inner join `produtos` on `produtos`.`id_produto` = `produto_venda`.`produto_id` inner join `vendas` on `vendas`.`id_venda` = `produto_venda`.`venda_id` GROUP BY `produto_venda`.`produto_id`';
        $products = DB::select($sql);

        $pdf = PDF::loadView('pdf.vendidos', compact('products','data_now'));
        $result = $pdf->download($data_now.'.pdf');
    
        $base = base64_encode($result);
    
        return ['file' => $base,'data' => $data_now];
    }

    public function catalogo()
    {
        $data_now = $this->dateNow();
    
        $sql = 'SELECT * from `produtos` WHERE `produtos`.`status` = "ok" GROUP BY `produtos`.`categoria_id`, `produtos`.`name` ORDER BY `produtos`.`name` ASC';
        $products = DB::select($sql);
        
        $pdf = PDF::loadView('pdf.catalogo', compact('products', 'data_now'));
        $result = $pdf->download($data_now.'.pdf');
    
        $base = base64_encode($result);
    
        return ['file' => $base,'data' => $data_now];
            
        
    }

    public function entregas()
    {
        $data_now = $this->dateNow();
    
        $datas = Entrega::with('entregador')->orderBy('id_entrega', 'desc')->get();
    
        $pdf = PDF::loadView('pdf.entregas', compact('datas', 'data_now'));
        $result = $pdf->download($data_now.'.pdf');
    
        $base = base64_encode($result);
    
        return ['file' => $base,'data' => $data_now];
            
        
    }

    public function entregaDetalhes($id)
    {
        $data_now = $this->dateNow();
        $today = $this->dateToday();
        
        $dadosEntrega = Entrega::where('id_entrega', '=', $id)->leftJoin('users','users.id', '=', 'entregas.entregador_id')->select('users.name as entregador', 'entregas.*')->first();
        if (!$dadosEntrega) {
            return false;
        }
        
        $idEntregador = $dadosEntrega->entregador_id;
        
        $dadosProdutos = EntregaItem::with('produto')->where('entrega_id', '=', $id)->orderBy('created_at', 'desc')->get();
        if (!$dadosProdutos) {
            return false;
        } 
        
        $despesaEntrega = DespesaEntrega::with('entregador')->whereBetween('created_at', [$today['inicio'], $today['fim']])->where('entregador_id', '=', $idEntregador)->get();
        if (!$despesaEntrega) {
            return false;
        } 
        
        $dadosVendas = Venda::with('produtos', 'cliente')->where('vendedor_id', $idEntregador)->where('entrega_id', '=', $id)->orderBy('id_venda', 'desc')->get();
        if (!$dadosVendas) {
            return false;
        } 
        
        $sql = 'SELECT `produtos`.`name` as `nameProduto`, `produtos`.`path`, `produto_venda`.*, `vendas`.`entrega_id`, SUM(`produto_venda`.`qtd_venda`) as qtdTotal from `produto_venda` inner join `produtos` on `produtos`.`id_produto` = `produto_venda`.`produto_id` inner join `vendas` on `vendas`.`id_venda` = `produto_venda`.`venda_id` where `vendas`.`entrega_id` = '.$id.' GROUP BY `produto_venda`.`produto_id`';
        $products = DB::select($sql); 
        
        $dadosEntrega->qtd_disponiveis = 0;

        foreach ($dadosProdutos as $item) {
            $item->id_estoque = $item->produto->estoque()->first()->id_estoque;
            $item->preco_entrega *= $item->qtd_produto;
            $item->lucro_entrega *= $item->qtd_produto;
            $dadosEntrega->qtd_disponiveis += $item->qtd_produto;
        }
        
        $totalDespesa = 0;
        
        foreach ($despesaEntrega as $item) {
            $totalDespesa += $item->valor;
        }
        
        $pdf = PDF::loadView('pdf.entrega-detalhes', compact('dadosEntrega', 'dadosProdutos', 'dadosVendas', 'despesaEntrega', 'data_now', 'totalDespesa', 'products'));
        $result = $pdf->download($data_now.'.pdf');

        $base = base64_encode($result);
    
        return ['file' => $base,'data' => $data_now];
    }

    public function detalheAReceber($id)
    {
        $data_now = $this->dateNow();
        
        $dadosVenda = Venda::where('id_venda', '=', $id)->leftJoin('clientes','clientes.id_cliente', '=', 'vendas.cliente_id')->select('clientes.name as cliente', 'vendas.*')->first();
        if (!$dadosVenda) {
            return false;
        }
        
        $dadosProdutos = ProdutoVenda::with('produto')->where('venda_id', '=', $id)->orderBy('created_at', 'desc')->get();
        if (!$dadosProdutos) {
            return false;
        }
        
        $dadosMovition = Movition::where('venda_id', '=', $id)->orderBy('data', 'desc')->get();
        if ($dadosMovition == null) {
            return false;
        }
        
        $pdf = PDF::loadView('pdf.detalhe-areceber', compact('dadosVenda', 'dadosProdutos', 'dadosMovition'));
        $result = $pdf->download($data_now.'.pdf');

        $base = base64_encode($result);
    
        return ['file' => $base,'data' => $data_now];   
    }
}
