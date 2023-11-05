<?php

namespace App\Http\Controllers\Movition;

use App\Enums\CodeStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\Venda;
use App\Models\Movition;
use App\Repositories\Contracts\Movition\MovitionRepositoryInterface;

class MovitionController extends Controller
{
    private $movitionRepository;

    private $codeStatus;

    public function __construct(MovitionRepositoryInterface $movitionRepository, CodeStatusEnum $codeStatus)
    {
        $this->movitionRepository = $movitionRepository;
        $this->codeStatus = $codeStatus;
    }

    public function index(Request $request)
    {
        try {
            $queryParams = $request->all();
            $res = $this->movitionRepository->index($queryParams);

            if (isset($res->code) && $res->code == $this->codeStatus::ERROR_SERVER) {
                return response()->json(['message' => $res->message], $res->code);
            }

            return response()->json($res, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $dados = $request->all();

            $res = $this->movitionRepository->create($dados);

            if (isset($res->code) && $res->code == $this->codeStatus::ERROR_SERVER) {
                return response()->json(['message' => $res->message], $res->code);
            }

            return response()->json(['response' => 'Cadastro efetuado com sucesso!'], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $res = $this->movitionRepository->delete($id);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => 'Deletado com sucesso'], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    // Tipos de caixas
    public function getAllItem()
    {
        try {
            $res = $this->movitionRepository->getAllItem();
            
            if (!$res) {
                return response()->json(['message' => 'Falha ao processar o produto!'], 500);
            }
            
            return response()->json($res, 200);


        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function getItemById($id)
    {

        try {
            $res = $this->movitionRepository->getItemById($id);
            
            if (!$res) {
                return response()->json(['message' => 'Falha ao processar o produto!'], 500);
            }
            
            return response()->json($res, 200);


        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function storeItem(Request $request)
    {   
        try {
            $dados = $request->all();
            $res = $this->movitionRepository->createItem($dados);

            if (isset($res['code']) && $res['code'] == 500) {
                return response()->json($res, 500);
            }

            return response()->json(['response' => $res], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function updateItem(Request $request, $id)
    {
        try {
            $dados = $request->all();

            $res = $this->movitionRepository->updateItem($dados, $id);

            if (isset($res->code) && $res->code == CodeStatusEnum::ERROR_SERVER) {
                return response()->json(['message' => $res->message], $res->code);
            }

            return response()->json($res, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function destroyItem($id)
    {
        try {

            $res = $this->movitionRepository->deleteItem($id);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
}
