<?php

namespace App\Repositories\Eloquent\Movition;

use App\Models\Movition;
use App\Models\TipoCaixa;
use App\Repositories\Contracts\Movition\MovitionRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Utils\Tools;

class MovitionRepository extends AbstractRepository implements MovitionRepositoryInterface
{
    /**
     * @var Movition
     */
    protected $model = Movition::class;

    /**
     * @var Tools
     */
    protected $tools = Tools::class;

    public function index($queryParams)
    {
        if (!isset($queryParams['type'])) {
            return ['message' => 'O tipo de movivementação não foi selecionado!', 'code' => 500];
        }

        if ($queryParams['type'] == 'historico') {
            if (isset($queryParams['date'])) {
                $query = $this->forDate($queryParams['date'], $queryParams['type']);
            } else {
                $query = $this->historico();
            }
        } else {
            if (isset($queryParams['date'])) {
                $query = $this->forDate($queryParams['date'], $queryParams['type']);
            } else {
                $query = $this->geral($queryParams);
            }
        }

        return $query;
    }

    private function forDate($date, $type)
    {
        $date = $this->filterDate($date);
        if ($type == 'historico') {
            $saldoTotal = $this->model->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
            $dados = $this->model->whereBetween('data', [$date['inicio'], $date['fim']])->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
        } else {
            $saldoTotal = $this->model->where('status', $type)->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
            $dados = $this->model->whereBetween('data', [$date['inicio'], $date['fim']])->where('status', $type)->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
        }

        if (!$dados) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        return [
            'dados'      => $dados,
            'saldoMes'   => $this->tools->calcularEntradaSaida($dados),
            'saldoTotal' => $this->tools->calcularEntradaSaida($saldoTotal),
            'month'     => isset($queryParams['date']) ? $queryParams['date'] : date('m'),
        ];
    }

    private function historico()
    {
        $dateYear = $this->dateYear();

        $saldoTotal = $this->model->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
        $dados = $this->model->whereBetween('data', [$dateYear['inicio'], $dateYear['fim']])->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();

        if (!$dados) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        return [
            'dados'      => $dados,
            'saldoMes'   => $this->tools->calcularEntradaSaida($dados),
            'saldoTotal' => $this->tools->calcularEntradaSaida($saldoTotal),
            'month'     => isset($queryParams['date']) ? $queryParams['date'] : date('m'),

        ];
    }

    private function geral($query)
    {
        $dateMonth = $this->dateMonth();

        $saldoTotal = $this->model->where('status', $query)->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
        $dados = $this->model->whereBetween('data', [$dateMonth['inicio'], $dateMonth['fim']])->where('status', $query)->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();

        if (!$dados) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        return [
            'dados'      => $dados,
            'saldoMes'   => $this->tools->calcularEntradaSaida($dados),
            'saldoTotal' => $this->tools->calcularEntradaSaida($saldoTotal),
            'month'     => isset($queryParams['date']) ? $queryParams['date'] : date('m'),
        ];
    }

    public function create($dados)
    {
        $save = [
            'data' => $this->dateNow(),
            'valor' => $dados['valor'],
            'descricao' => $dados['descricao'],
            'tipo' => $dados['tipo'],
            'status' => $dados['status']
        ];

        $res = $this->store($save);

        if (!$res->save()) {
            return ['message' => 'Falha ao cadastrar despesa!', 'code' => 500];
        }

        return $res;
    }

    // Tipos de caixas
    public function getAllItem()
    {
        $dados = TipoCaixa::all();
        return $dados;
    }

    public function getItemById($id)
    {
        $dados = TipoCaixa::where('id', '=', $id)->first();

        if (!$dados) {
            return false;
        }

        return $dados;
    }

    public function createItem($dados)
    {
        $dados['tipo'] = strtolower($dados['tipo']);
        
        $item = TipoCaixa::where('tipo', $dados['tipo'])->first();
        if ($item) {
            return ['message' => 'Item já cadastrado!', 'code' => 500];
        }

        $result = TipoCaixa::create($dados);
        if (!$result) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        return ['message' => 'Item cadastrado com sucesso!', 'code' => 200];
    }

    public function updateItem($dados, $id)
    {

        $item = TipoCaixa::where('id', '=', $id)->first();
        if (!$item) {
            return ['code' => 500, 'message' => 'Falha ao processar a entrega'];
        }

        $item->fill($dados);

        if (!$item->save()) {
            return ['message' => 'Falha ao atualizar dados!', 'code' => 500];
        }

        return ['message' => 'Atualizado com sucesso!'];
    }

    public function deleteItem($id)
    {
        $dados = TipoCaixa::where('id', '=', $id)->first();

        if (!$dados) {
            return false;
        }

        $dados->delete();

        return ['message' => 'Item deletado com sucesso!'];
    }
}
