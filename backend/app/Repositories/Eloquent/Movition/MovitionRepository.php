<?php

namespace App\Repositories\Eloquent\Movition;

use App\Models\Movition;
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

    public function index()
    {
        $dados = $this->model->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();

        if (!$dados) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        return [
            'movition' => $dados,
            'numero' => $dados->count(),
            'total' => $this->tools->calcularEntradaSaida($dados)
        ];
    }

    public function filtrarMes($dados)
    {
        $date = $this->dateFilter($dados['date']);
        $dados = $this->model->whereBetween('data', [$date['inicio'], $date['fim']])->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();

        if (!$dados) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        return [
            'movition' => $dados,
            'numero' => $dados->count(),
            'total' => $this->tools->calcularEntradaSaida($dados)
        ];
    }

    public function geral()
    {
        $dados = $this->model->with(['venda'])->where('data', 'LIKE', '%' . $this->dateNow() . '%')->where('status', 'geral')->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();

        if (!$dados) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        return [
            'movition' => $dados,
            'numero' => $dados->count(),
            'total' => $this->tools->calcularEntradaSaida($dados)
        ];
    }

    public function create($dados)
    {
        $save = [
            'data' => $this->dateNow(),
            'valor' => $dados['valor'],
            'descricao' => $dados['descricao'],
            'tipo' => $dados['tipo'],
            'status' => $dados['status_movition']
        ];

        $res = $this->store($save);

        if (!$res->save()) {
            return ['message' => 'Falha ao cadastrar despesa!', 'code' => 500];
        }

        return $res;
    }
}
