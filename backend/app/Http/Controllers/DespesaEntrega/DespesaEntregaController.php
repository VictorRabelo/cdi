<?php

namespace App\Http\Controllers\DespesaEntrega;

use App\Enums\CodeStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\Contracts\DespesaEntrega\DespesaEntregaRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DespesaEntregaController extends Controller
{
    private $despesaRepository;
    private $codeStatus;

    public function __construct(DespesaEntregaRepositoryInterface $despesaRepository, CodeStatusEnum $codeStatus)
    {
        $this->despesaRepository = $despesaRepository;
        $this->codeStatus = $codeStatus;
    }

    public function index()
    {
        try {

            $res = $this->despesaRepository->index();

            if (empty($res)) {
                return response()->json(['message' => 'Erro de servidor!',], 500);
            }

            return response()->json($res, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor!',], 500);
        }
    }

    public function movimentacao()
    {
        try {

            $res = $this->despesaRepository->movimentacao();

            if (isset($res->code) && $res->code == $this->codeStatus::ERROR_SERVER) {
                return response()->json(['message' => $res->message], $res->code);
            }

            return response()->json(['response' => $res], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor!',], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $dados = $request->all();

            $res = $this->despesaRepository->create($dados);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => 'Cadastro efetuado com sucesso!'], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function show($id)
    {
        try {

            $res = $this->despesaRepository->show($id);

            if (empty($res)) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => $res], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $dados = $request->all();

            $res = $this->despesaRepository->update($dados, $id);

            if (isset($res->code) && $res->code == $this->codeStatus::ERROR_SERVER) {
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

            $res = $this->despesaRepository->delete($id);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => 'Deletado com sucesso'], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
}
