<?php

namespace App\Repositories\Eloquent\DespesaEntrega;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\DespesaEntrega;
use App\Models\ProdutoVenda;
use App\Repositories\Contracts\DespesaEntrega\DespesaEntregaRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Utils\Tools;

class DespesaEntregaRepository extends AbstractRepository implements DespesaEntregaRepositoryInterface
{
    /**
     * @var DespesaEntrega
     */
    protected $model = DespesaEntrega::class;

    /**
     * @var Tools
     */
    protected $tools = Tools::class;

    public function index()
    {
        $id = auth()->user()->id;
        $date = $this->dateToday();
        
        $model = DespesaEntrega::where('entregador_id', $id)->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('created_at', 'desc')->get();
        if (!$model) {
            return ['message' => 'Falha ao processar as despesas!', 'code' => 500];
        }

        $saldo = $this->tools->calcularSaldo($model);

        return ['response' => $model, 'saldo' => $saldo];
    }

    public function movimentacao()
    {
        $despesas = DespesaEntrega::where('created_at', 'LIKE', '%' . $this->dateNow() . '%')->get();
        if (!$despesas) {
            return ['message' => 'Falha ao processar as despesas!', 'code' => 500];
        }

        $vendas = ProdutoVenda::where('created_at', 'LIKE', '%' . $this->dateNow() . '%')->orderBy('created_at', 'desc')->get()->groupBy('venda_id');
        if (!$vendas) {
            return ['message' => 'Falha ao processar as vendas!', 'code' => 500];
        }

        return ['despesas' => $despesas, 'vendas' => $vendas];
    }

    public function create($dados)
    {
        if (!isset($dados['data'])) {
            $dados['data'] = $this->dateNow();
        }
        $dados['entregador_id'] = auth()->user()->id;

        $res = DespesaEntrega::create($dados);

        if (!$res->save()) {
            return ['message' => 'Falha ao cadastrar despesa!', 'code' => 500];
        }

        return $res;
    }
}
