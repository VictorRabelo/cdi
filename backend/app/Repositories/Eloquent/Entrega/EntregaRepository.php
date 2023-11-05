<?php

namespace App\Repositories\Eloquent\Entrega;

use App\Enums\CodeStatusVendaEnum;
use App\Models\Entrega;
use App\Models\EntregaItem;
use App\Models\Estoque;
use App\Models\Produto;
use App\Repositories\Contracts\Entrega\EntregaRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Resolvers\AppResolverInterface;
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

    /**
     * @var AppResolverInterface
     */
    protected $baseApp = AppResolverInterface::class;

    public function index($queryParams)
    {
        if (isset($queryParams['app'])) {
            if ($queryParams['typeSearch'] == 'bau') {
                return $this->baseApp->getEntregasApp($queryParams);
            }

            if ($queryParams['typeSearch'] == 'sales') {
                return $this->baseApp->getEntregasDisponiveis();
            }
        }

        if (isset($queryParams['date'])) {
            if ($queryParams['date'] == 0) {
                $dados = $this->model->with('entregador')->orderBy('id_entrega', 'desc')->get();
            } else {
                $date = $this->filterDate($queryParams['date']);
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
            'data'         => isset($date['inicio']) ? $date['inicio'] : date('Y-m-d'),
            'mounth'       => isset($queryParams['date']) ? $queryParams['date'] : date('m'),
        ];
    }

    public function show($id)
    {
        $dadosEntrega = $this->model->where('id_entrega', '=', $id)->leftJoin('users', 'users.id', '=', 'entregas.entregador_id')->select('users.name as entregador', 'entregas.*')->first();
        if (!$dadosEntrega) {
            return false;
        }

        $dadosProdutos = EntregaItem::with('produto')->where('entrega_id', '=', $id)->orderBy('created_at', 'desc')->get();
        if (!$dadosProdutos) {
            return false;
        }

        $qtdDisponiveis = 0;
        $dadosEntrega->total_final = 0;
        $dadosEntrega->lucro = 0;
        $dadosEntrega->qtd_produtos = 0;

        foreach ($dadosProdutos as $item) {

            $item->id_estoque = $item->produto->estoque()->first()->id_estoque;
            $item->preco_entrega *= $item->qtd_produto;
            $item->lucro_entrega *= $item->qtd_produto;

            $qtdDisponiveis += $item->qtd_disponivel;

            $dadosEntrega->lucro += $item->lucro_entrega;
            $dadosEntrega->qtd_produtos += $item->qtd_produto;
            $dadosEntrega->total_final += $item->preco_entrega;
        }

        if ($dadosEntrega->status != 'ok') {
            $dadosEntrega->save();
        }

        $dadosEntrega->qtd_disponiveis = $qtdDisponiveis;

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

            if (!is_null($dados->status) && $dados->qtd_disponivel > 0) {
                $dadoEstoque->increment('und', $item->qtd_disponivel);
            }

            if ($dadoEstoque->und > 0) {
                $dadoProduto->update(['status' => 'ok']);
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

        if (!$dadosEntrega->save()) {
            return ['message' => 'Falha ao cadastrar venda', 'code' => 500];
        }

        if (!$this->movimentacaoEstoque($dados['itens'])) {
            return ['message' => 'Falha na movimentação do estoque', 'code' => 500];
        }

        return ['message' => 'Entrega realizada com sucesso!', 'code' => 200];
    }

    // Item 
    public function getAllItem($queryParams)
    {
        if (isset($queryParams['app'])) {
            return $this->baseApp->getAllItemAvailable($queryParams);
        }
    }

    public function getItemById($id)
    {
        $dados = EntregaItem::where('id', '=', $id)->first();
        if (!$dados) {
            return false;
        }

        $produto = $dados->produto()->first();
        $dados->produto = $produto;
        $dados->produto->estoque = $produto->estoque()->first();

        return $dados;
    }

    public function createItem($dados)
    {
        $itensEntrega = EntregaItem::where('entrega_id', $dados['entrega_id'])->where('produto_id', $dados['id_produto'])->first();
        if ($itensEntrega) {
            return ['message' => 'Produto já cadastrado!', 'code' => 500];
        }

        $dados['qtd_produto']    = $dados['qtd_venda'];
        $dados['qtd_disponivel'] = $dados['qtd_produto'];
        $dados['lucro_entrega']  = $dados['lucro_venda'];
        $dados['preco_entrega']  = $dados['preco_venda'];

        $result = EntregaItem::create($dados);
        if (!$result) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        $dadosEntrega = Entrega::where('id_entrega', '=', $dados['entrega_id'])->first();
        if (!$dadosEntrega) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        if ($dadosEntrega->status == 'pendente') {
            $dadoProduto = $result->produto()->first();
            $dadoEstoque = $dadoProduto->estoque()->first();

            if (!$dadoEstoque) {
                return ['code' => 500, 'message' => 'Falha no estoque'];
            }

            $dadoEstoque->decrement('und', $dados['qtd_disponivel']);
            $dadoEstoque->save();
        }

        return ['message' => 'Item cadastrado com sucesso!', 'code' => 200];
    }

    public function updateItem($dados, $id)
    {

        $dadosItem = EntregaItem::where('id', '=', $id)->first();
        if (!$dadosItem) {
            return ['code' => 500, 'message' => 'Falha ao processar a entrega'];
        }

        $dadosEntrega = Entrega::where('id_entrega', '=', $dados['entrega_id'])->first();
        if (!$dadosEntrega) {
            return ['code' => 500, 'message' => 'Falha ao processar a entrega'];
        }

        // Config input
        $dados['qtd_produto']    = $dados['qtd_venda'];
        $dados['qtd_disponivel'] = $dados['qtd_produto'];
        $dados['lucro_entrega']  = $dados['lucro_venda'];
        $dados['preco_entrega']  = $dados['preco_venda'];

        $estoque = Estoque::where('id_estoque', $dados['produto']['estoque']['id_estoque'])->where('produto_id', $dados['produto']['estoque']['produto_id'])->first();
        if (!$estoque) {
            return ['code' => 500, 'message' => 'Falha no estoque'];
        }

        if ($dadosEntrega->status == 'pendente') {

            if (!isset($dados['add'])) {
                $estoque->increment('und', $dadosItem->qtd_produto);
                $estoque->decrement('und', $dados['qtd_produto']);
                $estoque->save();

                $dadosItem->update([
                    'qtd_produto'      => $dados['qtd_produto'],
                    'qtd_disponivel'   => $dados['qtd_produto'],
                    'lucro_entrega'    => $dados['lucro_venda'],
                    'preco_entrega'    => $dados['preco_entrega'],
                ]);
            }

            if (isset($dados['add'])) {
                $estoque->decrement('und', $dados['qtd_produto']);
                $estoque->save();

                $dadosItem->increment('qtd_produto', $dados['qtd_produto']);
                $dadosItem->increment('qtd_disponivel', $dados['qtd_produto']);
                $dadosEntrega->increment('qtd_produtos', $dados['qtd_produto']);
            }
        }

        if ($dadosEntrega->status == null) {

            if (isset($dados['add'])) {
                $dadosItem->increment('qtd_produto', $dados['qtd_produto']);
                $dadosItem->increment('qtd_disponivel', $dados['qtd_produto']);
                $dadosEntrega->increment('qtd_produtos', $dados['qtd_produto']);
            }

            if (!isset($dados['add'])) {

                $dadosItem->update([
                    'qtd_produto'      => $dados['qtd_produto'],
                    'qtd_disponivel'   => $dados['qtd_produto'],
                    'lucro_entrega'    => $dados['lucro_venda'],
                    'preco_entrega'    => $dados['preco_entrega'],
                ]);
            }
        }

        return ['message' => 'Atualizado com sucesso!'];
    }

    public function deleteItem($id)
    {
        $dados = EntregaItem::where('id', '=', $id)->first();

        if (!$dados) {
            return false;
        }

        $dadosEntrega = Entrega::where('id_entrega', '=', $dados['entrega_id'])->first();
        if (!$dadosEntrega) {
            return false;
        }

        $dadoProduto = $dados->produto()->first();
        $dadoEstoque = $dadoProduto->estoque()->first();

        if ($dadosEntrega->status == 'pendente') {
            $dadoEstoque->increment('und', $dados->qtd_disponivel);
            $dadoEstoque->save();
        }

        $dados->delete();

        return ['message' => 'Item deletado com sucesso!'];
    }

    public function baixaEntrega($dados, $id)
    {
        $dadosEntrega = Entrega::where('id_entrega', '=', $id)->first();
        if (!$dadosEntrega) {
            return ['message' => 'Não encontrou a entrega!', 'code' => 500];
        }

        foreach ($dadosEntrega->entregasItens()->get() as $item) {

            $dadoProduto = $item->produto()->first();
            $dadoEstoque = $dadoProduto->estoque()->first();

            if ($item->qtd_disponivel > 0) {
                $dadoEstoque->increment('und', $item->qtd_disponivel);
                $item->decrement('qtd_disponivel', $item->qtd_disponivel);
            }

            if ($dadoEstoque->und > 0) {
                $dadoProduto->update(['status' => 'ok']);
            } else {
                $dadoProduto->update(['status' => 'vendido']);
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

            if (!$dadosEstoque->getIsHasUndAttribute()) {
                $dadosProduto->update(['status' => 'vendido']);
                return false;
            }

            $dadosEstoque->decrement('und', $item['qtd_produto']);

            if (!$dadosEstoque->getIsHasUndAttribute()) {
                $dadosProduto->update(['status' => 'vendido']);
            }
        }

        return true;
    }
}
