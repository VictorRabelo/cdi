<?php

namespace App\Repositories\Eloquent\Venda;

use App\Enums\CodeStatusVendaEnum;
use App\Models\Estoque;
use App\Models\Movition;
use App\Models\ProdutoVenda;
use App\Models\Venda;
use App\Repositories\Contracts\Venda\VendaRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Utils\Messages;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendaRepository extends AbstractRepository implements VendaRepositoryInterface
{
    /**
     * @var Venda
     */
    protected $model = Venda::class;

    /**
     * @var Tools
     */
    protected $tools = Tools::class;

    /**
     * @var Messages
     */
    protected $messages = Messages::class;

    public function index($queryParams)
    {
        if (isset($queryParams['aReceber'])) {
            return $this->aReceber();
        }

        $totalVendas = $this->model->select(DB::raw('sum(total_final) as total'))->get();

        if(isset($queryParams['date'])) {
            if($queryParams['date'] == 0){
                $dados = $this->model->with('produto', 'cliente', 'vendedor')->orderBy('id_venda', 'desc')->get();
            } else {
                $date = $this->dateFilter($queryParams['date']);
                $dados = $this->model->with('produto', 'cliente', 'vendedor')->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('id_venda', 'desc')->get();
            }

            if (!$dados) {
                return $this->messages->error;
            }

        } else {
            $date = $this->dateMonth();
            $dados = $this->model->with('produto', 'cliente', 'vendedor')->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('id_venda', 'desc')->get();
            if (!$dados) {
                return $this->messages->error;
            }
        }
        
        $lucro = 0;
        $totalMensal = 0;
        $pago = 0;

        $dataSource = [];
        foreach ($dados as $item) {
            
            $lucro += $item->lucro;
            $totalMensal += $item->total_final;
            $pago += $item->pago;

            array_push($dataSource, $item);
        }

        return [
            'vendas'       => $dataSource,
            'totalMensal'  => $totalMensal,
            'totalVendas'  => $totalVendas[0]['total'],
            'lucro'        => $lucro,
            'pago'         => $pago,
            'data'         => isset($date['inicio'])? $date['inicio']:date('Y-m-d'),
            'mounth'       => isset($queryParams['date'])? $queryParams['date']:date('m'),
        ];
    }
    
    public function show($id){
        $dadosVenda = Venda::where('id_venda', '=', $id)->leftJoin('clientes','clientes.id_cliente', '=', 'vendas.cliente_id')->select('clientes.name as cliente', 'vendas.*')->first();
        if (!$dadosVenda) {
            return false;
        }
        
        $dadosProdutos = ProdutoVenda::with('produto')->where('venda_id', '=', $id)->orderBy('created_at', 'desc')->get();
        if (!$dadosProdutos) {
            return false;
        }

        foreach ($dadosProdutos as $item) {
            $item->id_estoque = $item->produto->estoque()->first()->id_estoque;
            $item->preco_venda *= $item->qtd_venda;
            $item->lucro_venda *= $item->qtd_venda;
        }
        return ['dadosVenda' => $dadosVenda, 'dadosProdutos' => $dadosProdutos];
    }

    public function create($dados)
    {
        if (!$dados) {
            $dados['vendedor_id'] = $this->userLogado()->id;
            return $this->store($dados);
        }
    }

    public function update($dados, $id)
    {
        $dadosVenda = Venda::where('id_venda', '=', $id)->leftJoin('clientes','clientes.id_cliente', '=', 'vendas.cliente_id')->select('clientes.name as cliente', 'vendas.*')->first();
        if (!$dadosVenda) {
            return ['message' => 'Venda não encontrada!', 'code' => 404];
        }

        if(($dados['restante'] == 0 || $dados['restante'] < 0) && $dados['restante'] !== null) {
            $dados['status'] = 'pago';
        }

        $dadosVenda->fill($dados);
        if (!$dadosVenda->save()) {
            return ['message' => 'Falha ao debitar!', 'code' => 500];
        }

        if(isset($dados['debitar'])){
            return $this->debitar($dados, $id);
        }

        return ['message' => 'Venda atualizada com sucesso!', 'code' => 200];
    }

    public function deleteVenda($id)
    {
        $dados = $this->model->findOrFail($id);
        
        if (empty($dados)) {
            return ['message' => 'Falha na movimentação do estoque', 'code' => 500];

        }
        
        foreach ($dados->vendaItens()->get() as $item) {
            $dadoProduto = $item->produto()->first();
            $dadoEstoque = $dadoProduto->estoque()->first();

            if (!$dadoEstoque) {
                return ['message' => 'Falha na movimentação do estoque', 'code' => 500];
            }
            
            if ($dadoEstoque->und == 0) {
                $dadoProduto->update(['status' => 'ok']);
            } else {
                if($dados->status == 'pendente'){
                    $dadoEstoque->increment('und', $item->qtd_venda);
                }
            }

            $item->delete();
        }

        $dados->delete();

        return ['message' => 'Deletado com sucesso!', 'code' => 200];

    }

    public function finishVenda($dados)
    {

        if (count($dados['itens']) == 0) {
            return response()->json(['message' => 'Venda não contem itens!'], 500);
        }

        $dadosVenda = Venda::where('id_venda', '=', $dados['id_venda'])->first();
        if (!$dadosVenda) {
            return ['message' => 'Falha ao procurar venda ', 'code' => 500];
        }
        
        $dadosVenda->fill($dados);
        
        if(!$dadosVenda->save()){
            return ['message' => 'Falha ao cadastrar venda', 'code' => 500];
        }
        
        if (!$this->movimentacaoEstoque($dados['itens'])) {
            return ['message' => 'Falha na movimentação do estoque', 'code' => 500];
        }

        if (!$this->aPrazoVenda($dados)) {
            return ['message' => 'Falha ao cadastrar movimentação', 'code' => 500];
        }

        return ['message' => 'Venda realizada com sucesso!', 'code' => 200];
    }

    // Item 
    public function getItemById($id){
        $dados = ProdutoVenda::where('id', '=', $id)->first();
        if (!$dados) {
            return false;
        }
        
        $produto = $dados->produto()->first();
        $dados->produto = $produto;
        $dados->produto->estoque = $produto->estoque()->first();
        
        return $dados;
    }

    public function createItem($dados){
        $result = ProdutoVenda::create($dados);
        if(!$result){
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        $dadosVenda = Venda::where('id_venda', '=', $dados['venda_id'])->first();
        if(!$dadosVenda){
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        $total = $result->preco_venda * $result->qtd_venda;

        $resultFinal = $dadosVenda->total_final? $dadosVenda->total_final + $total : 0 + $total;
        $resultLucro = $dadosVenda->lucro + ($result->lucro_venda * $result->qtd_venda);
        $resultQtd   = $dadosVenda->qtd_produto + $result->qtd_venda;

        $dadosVenda->update(['total_final' => $resultFinal, 'lucro' => $resultLucro, 'qtd_produto' =>  $resultQtd]);
        return ['message' => 'Item cadastrado com sucesso!'];  
    }

    public function updateItem($dados, $id){
        $dadosItem = ProdutoVenda::where('id', '=', $id)->first();
        if (!$dadosItem) {
            return false;
        }
        
        $dadosVenda = Venda::where('id_venda', '=', $dados['venda_id'])->first();
        if (!$dadosVenda) {
            return false;
        }

        $configResult            = $dadosItem->preco_venda * $dadosItem->qtd_venda;
        $dadosVenda->lucro       = $dadosVenda->lucro - ($dadosItem->lucro_venda*$dadosItem->qtd_venda);
        $dadosVenda->total_final = $dadosVenda->total_final - $configResult;
        $dadosVenda->qtd_produto = $dadosVenda->qtd_produto - $dadosItem->qtd_venda;


        $dadosItem->update(['preco_venda' => $dados['preco_venda'], 'qtd_venda' => $dados['qtd_venda']]);
        if(!$dadosItem){
            return false;
        }
        
        $resultFinal = $dadosVenda->total_final + ($dadosItem->preco_venda * $dadosItem->qtd_venda);
        $resultLucro = $dadosVenda->lucro + ($dadosItem->lucro_venda * $dadosItem->qtd_venda);
        $resultQtd   = $dadosVenda->qtd_produto + $dadosItem->qtd_venda;

        $dadosVenda->update(['total_final' => $resultFinal, 'lucro' => $resultLucro, 'qtd_produto' =>  $resultQtd]);
        if(!$dadosVenda){
            return false;
        }

        return ['message' => 'Atualizado com sucesso!'];
    }
    
    public function deleteItem($id){
        $dados = ProdutoVenda::where('id', '=', $id)->first();
        if (!$dados) {
            return false;
        }
        
        $dadosVenda = Venda::where('id_venda', '=', $dados['venda_id'])->first();
        if (!$dadosVenda) {
            return false;
        }

        $resultFinal = $dadosVenda->total_final - ($dados->preco_venda * $dados->qtd_venda);
        $resultLucro = $dadosVenda->lucro - ($dados->lucro_venda * $dados->qtd_venda);
        $resultQtd   = $dadosVenda->qtd_produto - $dados->qtd_venda;

        $dadosVenda->update(['total_final' => $resultFinal, 'lucro' => $resultLucro, 'qtd_produto' =>  $resultQtd]);
        
        if(!$dados->delete()) {
            return false;
        }

        return ['message' => 'Item deletado com sucesso!'];
    }

    private function aReceber() {

        $dados = $this->model->with('produto', 'cliente', 'vendedor')->where('status', 'pendente')->orderBy('id_venda', 'desc')->get();
        if (!$dados) {
            return $this->messages->error;
        }

        $restante = 0;
        $pago = 0;
        $totalFinal = 0;

        $dataSource = [];
        foreach ($dados as $item) {
            $item->nameCliente = $item->cliente->name;
            $item->telefoneCliente = $item->cliente->telefone;
            $restante += $item->restante;
            $pago += $item->pago;
            $totalFinal += $item->total_final;

            array_push($dataSource, $item);
        }
        
        return [
            'dadosReceber'  => $dataSource,
            'saldoReceber'  => $totalFinal,
            'saldoPago'     => $pago,
            'totalRestante' => $restante,
        ];
    }

    private function debitar($dados, $id)
    {
        $dateNow = $this->dateNow();

        $movition = Movition::create([
            'venda_id' => $id,
            'data' => $dateNow,
            'valor' => $dados['creditar'],
            'descricao' => $dados['cliente'],
            'tipo' => 'entrada',
            'status' => $dados['caixa']
        ]);

        if(!$movition) {
            return ['message' => 'Falha criar movimentação!', 'code' => 500];
        }

        return ['message' => 'Debitado com sucesso!', 'code' => 200];
    }

    private function aPrazoVenda($dados)
    {
        if(!isset($dados['prazo'])) {
            
            $dateNow = $this->dateNow();

            $movition = Movition::create([
                'venda_id' => $dados['id_venda'],
                'data' => $dateNow,
                'valor' => $dados['debitar'],
                'descricao' => $dados['cliente'],
                'tipo' => 'entrada',
                'status' => $dados['caixa']
            ]);

            if(!$movition) {
                return false;
            }

            return true;
        }

        return true;
    }

    private function movimentacaoEstoque($dados)
    {
        foreach ($dados as $item) {
            $dadosEstoque = Estoque::where('id_estoque', $item['id_estoque'])->where('produto_id', $item['produto_id'])->first();
            if (!$dadosEstoque) {
                return false;
            }
            
            $dadosProduto = $dadosEstoque->produto;
            if (!$dadosProduto) {
                return false;
            }

            if(!$dadosEstoque->getIsHasUndAttribute()){
                $dadosProduto->update(['status' => 'vendido']);
                return false;
            }

            $dadosEstoque->decrement('und', $item['qtd_venda']);
            
            if(!$dadosEstoque->getIsHasUndAttribute()){
                $dadosProduto->update(['status' => 'vendido']);
            }
        }

        return true;
    }

}
