<?php

namespace App\Repositories\Eloquent\Despesa;

use App\Models\Despesa;
use App\Models\ProdutoVenda;
use App\Repositories\Contracts\Despesa\DespesaRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Utils\Tools;
use Illuminate\Http\Request;

class DespesaRepository extends AbstractRepository implements DespesaRepositoryInterface
{
    /**
     * @var Despesa
     */
    protected $model = Despesa::class;

    private $tools;

    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }

    public function index()
    {
        $model = Despesa::orderBy('created_at', 'desc')->get();
        $saldo = $this->tools->calcularSaldo($model);

        return ['response' => $model, 'saldo' => $saldo];
    }

    public function movimentacao()
    {
        $despesas = Despesa::where('created_at', 'LIKE', '%' . $this->dateNow() . '%')->get();
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
            $dados['data'] = $this->data_now;
        }

        $res = Despesa::create($dados);

        if (!$res->save()) {
            return ['message' => 'Falha ao cadastrar despesa!', 'code' => 500];
        }

        return $res;
    }
}
