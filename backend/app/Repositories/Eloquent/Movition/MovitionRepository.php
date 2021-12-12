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

    public function index($queryParams)
    {
        if (!isset($queryParams['type'])) {
            return ['message' => 'O tipo de movivementação não foi selecionado!', 'code' => 500];
        }

        if ($queryParams['type'] == 'geral') {
            if (isset($queryParams['date'])) {
                $query = $this->forDate($queryParams['date'],$queryParams['type']);
            } else {
                $query = $this->geral($queryParams['type']);
            }
        }
        
        if ($queryParams['type'] == 'eletronico') {
            if (isset($queryParams['date'])) {
                $query = $this->forDate($queryParams['date'],$queryParams['type']);
            } else {
                $query = $this->eletronico();
            }
        }
        
        if ($queryParams['type'] == 'historico') {
            if (isset($queryParams['date'])) {
                $query = $this->forDate($queryParams['date'],$queryParams['type']);
            } else {
                $query = $this->historico();
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
        } 

        if ($type == 'geral') {
            $saldoTotal = $this->model->where('status', 'geral')->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
            $dados = $this->model->whereBetween('data', [$date['inicio'], $date['fim']])->where('status', 'geral')->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
        }

        if ($type == 'eletronico') {
            $saldoTotal = $this->model->where('status', 'eletronico')->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
            $dados = $this->model->whereBetween('data', [$date['inicio'], $date['fim']])->where('status', 'eletronico')->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
        }

        if (!$dados) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        return [
            'dados'      => $dados,
            'saldoMes'   => $this->tools->calcularEntradaSaida($dados),
            'saldoTotal' => $this->tools->calcularEntradaSaida($saldoTotal),
            'month'     => isset($queryParams['date'])? $queryParams['date']:date('m'),
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
            'month'     => isset($queryParams['date'])? $queryParams['date']:date('m'),

        ];
    }
    
    private function geral()
    {
        $dateYear = $this->dateYear();
        $dateMonth = $this->dateMonth();

        $saldoTotal = $this->model->whereBetween('data', [$dateYear['inicio'], $dateYear['fim']])->where('status', 'geral')->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
        $dados = $this->model->whereBetween('data', [$dateMonth['inicio'], $dateMonth['fim']])->where('status', 'geral')->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();

        if (!$dados) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        return [
            'dados'      => $dados,
            'saldoMes'   => $this->tools->calcularEntradaSaida($dados),
            'saldoTotal' => $this->tools->calcularEntradaSaida($saldoTotal),
            'month'     => isset($queryParams['date'])? $queryParams['date']:date('m'),
        ];
    }
    
    private function eletronico()
    {
        $dateMonth = $this->dateMonth();
        $dateYear = $this->dateYear();

        $saldoTotal = $this->model->whereBetween('data', [$dateYear['inicio'], $dateYear['fim']])->where('status', 'eletronico')->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();
        $dados = $this->model->whereBetween('data', [$dateMonth['inicio'], $dateMonth['fim']])->where('status', 'eletronico')->orderBy('data', 'desc')->orderBy('id_movition', 'desc')->get();

        if (!$dados) {
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        return [
            'dados'      => $dados,
            'saldoMes'   => $this->tools->calcularEntradaSaida($dados),
            'saldoTotal' => $this->tools->calcularEntradaSaida($saldoTotal),
            'month'     => isset($queryParams['date'])? $queryParams['date']:date('m'),
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
}
