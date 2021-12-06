<?php

namespace App\Http\Controllers\Estoque;

use App\Enums\CodeStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\Contracts\Estoque\EstoqueRepositoryInterface;

class EstoqueController extends Controller
{
    private $estoqueRepository;

    public function __construct(EstoqueRepositoryInterface $estoqueRepository)
    {
        $this->estoqueRepository = $estoqueRepository;
    }

    public function index(Request $request)
    {
        try {
            $queryParams = $request->all();
            
            $res = $this->estoqueRepository->index($queryParams);

            if (isset($res['code']) && $res['code'] == 500) {
                return response()->json($res, 500);
            }

            return response()->json(['response' => $res, 'count' => $res->count()], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function store(Request $request)
    {   
        try {
            $res = $this->estoqueRepository->create($request);

            if (isset($res['code']) && $res['code'] == 500) {
                return response()->json($res, 500);
            }

            return response()->json(['response' => $res], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function show($id)
    {
        try {
            $res = $this->estoqueRepository->getById($id);
            
            if (!$res) {
                return response()->json(['message' => 'Falha ao processar o produto!'], 500);
            }
            
            return response()->json($res, 200);


        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $dados = $request->all();

            $res = $this->estoqueRepository->updateEstoque($dados, $id);

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

            $res = $this->estoqueRepository->deleteEstoque($id);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => 'Deletado com sucesso'], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

}
