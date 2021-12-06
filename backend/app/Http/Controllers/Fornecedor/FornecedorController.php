<?php

namespace App\Http\Controllers\Fornecedor;

use App\Enums\CodeStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fornecedor\FornecedorRequest;
use Illuminate\Http\Request;
use App\Repositories\Contracts\Fornecedor\FornecedorRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FornecedorController extends Controller
{
    private $fornecedorRepository;

    public function __construct(FornecedorRepositoryInterface $fornecedorRepository)
    {
        $this->fornecedorRepository = $fornecedorRepository;
    }

    public function index()
    {
        try {

            $res = $this->fornecedorRepository->index();

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => $res], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function show($id)
    {
        try {

            $res = $this->fornecedorRepository->show($id);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => $res], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function store(FornecedorRequest $request)
    {
        try {
            $dados = $request->all();

            $res = $this->fornecedorRepository->store($dados);

            if (empty($res)) {
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

            $res = $this->fornecedorRepository->update($dados, $id);

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

            $res = $this->fornecedorRepository->delete($id);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => 'Deletado com sucesso'], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
}
