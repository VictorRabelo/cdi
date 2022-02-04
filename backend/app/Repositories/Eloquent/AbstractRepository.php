<?php

namespace App\Repositories\Eloquent;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class AbstractRepository
{
    protected $model;
    protected $tools;
    protected $messages;
    protected $baseApp;

    public function __construct()
    {
        $this->model = $this->resolveModel();
        $this->tools = $this->resolveTools();
        $this->messages = $this->resolveMessages();
        $this->baseApp = $this->resolveBaseApp();
    }

    protected function resolveModel()
    {
        return app($this->model);
    }

    protected function resolveTools()
    {
        return app($this->tools);
    }

    protected function resolveMessages()
    {
        return app($this->messages);
    }

    protected function resolveBaseApp()
    {
        return app($this->baseApp);
    }

    public function verifica($dados)
    {
        if (!$dados) {
            return ['message' => 'Falha ao processar os dados!', 'code' => 500];
        }
    }

    public function dateNow()
    {
        date_default_timezone_set('America/Sao_Paulo');
        return date('Y-m-d H:i:s');
    }

    public function dateToday()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $date_start = date('Y-m-d'.' '.'00:00');
        $date_end = date('Y-m-d'.' '.'23:59');
        return ['inicio' => $date_start, 'fim' => $date_end];
    }
    
    public function dateYear()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $date_start = date('Y-01-01'.' '.'00:00');
        $date_end = date('Y-12-t'.' '.'23:59');
        return ['inicio' => $date_start, 'fim' => $date_end];
    }
    
    public function dateMonth()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $date_start = date('Y-m-01'.' '.'00:00');
        $date_end = date('Y-m-t'.' '.'23:59');
        return ['inicio' => $date_start, 'fim' => $date_end];
    }

    public function dateFilter($month)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $date_start = date('Y-' . $month . '-01'.' '.'00:00');
        $date_end = date('Y-' . $month . '-t'.' '.'23:59');
        return ['inicio' => $date_start, 'fim' => $date_end];
    }

    public function filterDate($date)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $date_start = date($date.'-01'.' '.'00:00');
        $date_end = date($date.'-t'.' '.'23:59');
        return ['inicio' => $date_start, 'fim' => $date_end];
    }
    
    public function userLogado()
    {
        return Auth::user();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function listing(Request $request)
    {
        return $this->model->all();
    }

    public function show($id)
    {
        return $this->model->findOrFail($id);
    }

    public function where($collum, $value, $operator = null)
    {
        if (!is_null($operator)) {
            return $this->model->where($collum, $operator, $value);
        }

        return $this->model->where($collum, $value);
    }

    public function store($dados)
    {
        return $this->model->create($dados);
    }

    public function update($dados, $id)
    {
        $resp = $this->model->findOrFail($id);

        if (empty($resp)) {
            return false;
        }

        $resp->fill($dados);

        if (!$resp->save()) {
            return ['message' => 'Falha ao atualizar dados!', 'code' => 500];
        }

        return $resp;
    }

    public function delete($id)
    {
        $resp = $this->model->findOrFail($id);

        if (empty($resp)) {
            return false;
        }

        $resp->delete();

        return $resp;
    }
}
