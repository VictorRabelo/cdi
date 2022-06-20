<?php

namespace App\Http\Controllers\FormaPagamento;

use App\Enums\CodeStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormaPagamento\FormaPagamentoRequest;
use Illuminate\Http\Request;
use App\Repositories\Contracts\FormaPagamento\FormaPagamentoRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FormaPagamentoController extends Controller
{
    private $formaPagamentoRepository;

    public function __construct(FormaPagamentoRepositoryInterface $formaPagamentoRepository)
    {
        $this->formaPagamentoRepository = $formaPagamentoRepository;
    }
    
    public function index()
    {
        try {

            $res = $this->formaPagamentoRepository->index();

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => $res, 'count' => $res->count()], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function total()
    {
        try {

            $res = $this->formaPagamentoRepository->all();

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => $res, 'count' => $res->count()], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function show($id)
    {
        try {

            $res = $this->formaPagamentoRepository->show($id);

            if (empty($res)) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => $res], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function store(ClienteRequest $request)
    {
        try {
            $dados = $request->all();
            $res = $this->formaPagamentoRepository->create($dados);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => 'Cadastro efetuado com sucesso!'], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $dados = $request->all();

            $res = $this->formaPagamentoRepository->update($dados, $id);

            if (isset($res->code) && $res->code == CodeStatusEnum::ERROR_SERVER) {
                return response()->json(['message' => $res->message], $res->code);
            }

            return response()->json(['response' => $res], 201);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $res = $this->formaPagamentoRepository->delete($id);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => 'Deletado com sucesso'], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
}
