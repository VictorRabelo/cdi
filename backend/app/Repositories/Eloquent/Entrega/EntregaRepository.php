<?php

namespace App\Repositories\Eloquent\Entrega;

use App\Enums\CodeStatusVendaEnum;
use App\Models\Entrega;
use App\Models\EntregaItem;
use App\Models\Estoque;
use App\Repositories\Contracts\Entrega\EntregaRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Utils\Messages;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntregaRepository extends AbstractRepository implements EntregaRepositoryInterface
{
    /**
     * @var Entrega
     */
    protected $model = Entrega::class;

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
        if(isset($queryParams['date'])) {
            if($queryParams['date'] == 0){
                $dados = $this->model->with('entregador')->orderBy('id_entrega', 'desc')->get();
            } else {
                $date = $this->dateFilter($queryParams['date']);
                $dados = $this->model->with('entregador')->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('id_entrega', 'desc')->get();
            }

            if (!$dados) {
                return $this->messages->error;
            }

        } else {
            $date = $this->dateMonth();
            $dados = $this->model->with('entregador')->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('id_entrega', 'desc')->get();
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

            array_push($dataSource, $item);
        }

        return [
            'entregas'     => $dataSource,
            'totalMensal'  => $totalMensal,
            'lucro'        => $lucro,
            'data'         => isset($date['inicio'])? $date['inicio']:date('Y-m-d'),
            'mounth'       => isset($queryParams['date'])? $queryParams['date']:date('m'),
        ];
    }
    
    public function show($id){
        $dadosEntrega = $this->model->where('id_entrega', '=', $id)->leftJoin('users','users.id', '=', 'entregas.entregador_id')->select('users.name as entregador', 'entregas.*')->first();
        if (!$dadosEntrega) {
            return false;
        }
        
        $dadosProdutos = EntregaItem::with('produto')->where('entrega_id', '=', $id)->orderBy('created_at', 'desc')->get();
        if (!$dadosProdutos) {
            return false;
        }

        $dadosEntrega->qtd_disponiveis = 0;

        foreach ($dadosProdutos as $item) {
            $item->id_estoque = $item->produto->estoque()->first()->id_estoque;
            $item->preco_entrega *= $item->qtd_produto;
            $item->lucro_entrega *= $item->qtd_produto;
            $dadosEntrega->qtd_disponiveis += $item->qtd_produto;
        }

        return ['dadosEntrega' => $dadosEntrega, 'dadosProdutos' => $dadosProdutos];
    }

    public function create($dados)
    {
        return $this->store($dados);
    }

    public function update($dados, $id)
    {
        $dadosEntrega = $this->where('id_entrega', '=', $id)->first();
        if (!$dadosEntrega) {
            return ['message' => 'Venda não encontrada!', 'code' => 404];
        }

        $dadosEntrega->fill($dados);
        if (!$dadosEntrega->save()) {
            return ['message' => 'Falha ao debitar!', 'code' => 500];
        }

        return ['message' => 'Venda atualizada com sucesso!', 'code' => 200];
    }

    public function deleteEntrega($id)
    {
        $dados = $this->model->findOrFail($id);
        
        if (empty($dados)) {
            return ['message' => 'Falha na movimentação do estoque', 'code' => 500];

        }
        
        foreach ($dados->entregasItens()->get() as $item) {
            $dadoProduto = $item->produto()->first();
            $dadoEstoque = $dadoProduto->estoque()->first();

            if (!$dadoEstoque) {
                return ['message' => 'Falha na movimentação do estoque', 'code' => 500];
            }
            
            if ($dadoEstoque->und == 0) {
                $dadoProduto->update(['status' => 'ok']);
            } else {
                if($dados->status == 'pendente'){
                    $dadoEstoque->increment('und', $item->qtd_produto);
                }
            }

            $item->delete();
        }

        $dados->delete();

        return ['message' => 'Deletado com sucesso!', 'code' => 200];

    }

    public function finishEntrega($dados)
    {
        if (count($dados['itens']) == 0) {
            return response()->json(['message' => 'Venda não contem itens!'], 500);
        }

        $dadosEntrega = Entrega::where('id_entrega', '=', $dados['id_entrega'])->first();
        if (!$dadosEntrega) {
            return ['message' => 'Falha ao procurar venda ', 'code' => 500];
        }
        
        $dados['status'] = 'pendente'; 
        
        $dadosEntrega->fill($dados);
        
        if(!$dadosEntrega->save()){
            return ['message' => 'Falha ao cadastrar venda', 'code' => 500];
        }
        
        if (!$this->movimentacaoEstoque($dados['itens'])) {
            return ['message' => 'Falha na movimentação do estoque', 'code' => 500];
        }

        return ['message' => 'Entrega realizada com sucesso!', 'code' => 200];
    }

    // Item 
    public function getItemById($id){
        $dados = EntregaItem::where('id', '=', $id)->first();
        if (!$dados) {
            return false;
        }
        
        $produto = $dados->produto()->first();
        $dados->produto = $produto;
        $dados->produto->estoque = $produto->estoque()->first();
        
        return $dados;
    }

    public function createItem($dados){
        $dados['qtd_produto']    = $dados['qtd_venda'];
        $dados['qtd_disponivel'] = $dados['qtd_produto'];
        $dados['lucro_entrega']  = $dados['lucro_venda'];
        $dados['preco_entrega']  = $dados['preco_venda'];

        $result = EntregaItem::create($dados);
        if(!$result){
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        $dadosVenda = Entrega::where('id_entrega', '=', $dados['entrega_id'])->first();
        if(!$dadosVenda){
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        $total = $result->preco_entrega * $result->qtd_produto;

        $resultFinal = $dadosVenda->total_final? $dadosVenda->total_final + $total : 0 + $total;
        $resultLucro = $dadosVenda->lucro + ($result->lucro_entrega * $result->qtd_produto);
        $resultQtd   = $dadosVenda->qtd_produtos + $result->qtd_produto;

        $dadosVenda->update(['total_final' => $resultFinal, 'lucro' => $resultLucro, 'qtd_produtos' =>  $resultQtd]);
        return ['message' => 'Item cadastrado com sucesso!'];  
    }

    public function updateItem($dados, $id){
        $dados['qtd_produto']    = $dados['qtd_venda'];
        $dados['qtd_disponivel'] = $dados['qtd_produto'];
        $dados['lucro_entrega']  = $dados['lucro_venda'];
        $dados['preco_entrega']  = $dados['preco_venda'];

        $dadosItem = EntregaItem::where('id', '=', $id)->first();
        if (!$dadosItem) {
            return false;
        }
        
        $dadosVenda = Entrega::where('id_entrega', '=', $dados['entrega_id'])->first();
        if (!$dadosVenda) {
            return false;
        }

        $configResult             = $dadosItem->preco_entrega * $dadosItem->qtd_produto;
        $dadosVenda->lucro        = $dadosVenda->lucro - ($dadosItem->lucro_entrega * $dadosItem->qtd_produto);
        $dadosVenda->total_final  = $dadosVenda->total_final - $configResult;
        $dadosVenda->qtd_produtos = $dadosVenda->qtd_produtos - $dadosItem->qtd_produto;

        $dadosItem->update([
            'preco_entrega'    => $dados['preco_entrega'], 
            'lucro_entrega'    => $dados['lucro_venda'], 
            'qtd_produto'      => $dados['qtd_produto'],
            'qtd_disponivel'   => $dados['qtd_produto'],
        ]);
        if(!$dadosItem){
            return false;
        }
        
        $resultFinal = $dadosVenda->total_final + ($dadosItem->preco_entrega * $dadosItem->qtd_produto);
        $resultLucro = $dadosVenda->lucro + ($dadosItem->lucro_entrega * $dadosItem->qtd_produto);
        $resultQtd   = $dadosVenda->qtd_produtos + $dadosItem->qtd_produto;

        $dadosVenda->update(['total_final' => $resultFinal, 'lucro' => $resultLucro, 'qtd_produtos' =>  $resultQtd]);
        if(!$dadosVenda){
            return false;
        }

        return ['message' => 'Atualizado com sucesso!'];
    }
    
    public function deleteItem($id){
        $dados = EntregaItem::where('id', '=', $id)->first();
        if (!$dados) {
            return false;
        }
        
        $dadosVenda = Entrega::where('id_entrega', '=', $dados['entrega_id'])->first();
        if (!$dadosVenda) {
            return false;
        }

        $resultFinal = $dadosVenda->total_final - ($dados->preco_entrega * $dados->qtd_produto);
        $resultLucro = $dadosVenda->lucro - ($dados->lucro_entrega * $dados->qtd_produto);
        $resultQtd   = $dadosVenda->qtd_produtos - $dados->qtd_produto;

        $dadosVenda->update(['total_final' => $resultFinal, 'lucro' => $resultLucro, 'qtd_produtos' =>  $resultQtd]);
        
        if(!$dados->delete()) {
            return false;
        }

        return ['message' => 'Item deletado com sucesso!'];
    }

    public function baixaEntrega($dados, $id)
    {
        $dadosEntrega = Entrega::where('id_entrega', '=', $id)->first();
        if(!$dadosEntrega) {
            return ['message' => 'Não encontrou a entrega!', 'code' => 500];
        }
        
        foreach ($dadosEntrega->entregasItens()->get() as $item) {
            
            $dadoProduto = $item->produto()->first();
            $dadoEstoque = $dadoProduto->estoque()->first();

            if ($dadoEstoque->und == 0) {
                $dadoProduto->update(['status' => 'ok']);
            }

            if($item->qtd_disponivel > 0){
                $dadoEstoque->increment('und', $item->qtd_disponivel);
                $item->decrement('qtd_disponivel', $item->qtd_disponivel);
            }
        }
        
        $dadosEntrega->update(['status' => 'ok']);

        return ['message' => 'Baixa com sucesso!', 'code' => 200];
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

            $dadosEstoque->decrement('und', $item['qtd_produto']);
            
            if(!$dadosEstoque->getIsHasUndAttribute()){
                $dadosProduto->update(['status' => 'vendido']);
            }
        }

        return true;
    }

}
