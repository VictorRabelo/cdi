<?php

namespace App\Http\Controllers\Venda;

use App\Enums\CodeStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use Auth;
use App\Models\Venda;
use App\Models\Movition;
use App\Models\Estoque;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\ProdutoVenda;
use App\Repositories\Contracts\Venda\VendaRepositoryInterface;
use Illuminate\Support\Carbon;

class VendaController extends Controller
{
    private $vendaRepository;

    public function __construct(VendaRepositoryInterface $vendaRepository)
    {
        $this->vendaRepository = $vendaRepository;
    }

    public function index(Request $request)
    {
        try {
            $queryParams = $request->all();
            $res = $this->vendaRepository->index($queryParams);

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
            $res = $this->vendaRepository->show($id);
            
            if (!$res) {
                return response()->json(['message' => 'Erro de servidor'], 500);
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
            $res = $this->vendaRepository->create($dados);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => $res], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $dados = $request->all();

            $res = $this->vendaRepository->update($dados, $id);

            if ($res['code'] == 500) {
                return response()->json(['message' => $res['message']], $res['code']);
            }

            return response()->json($res['message'], $res['code']);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function destroy($id)
    {
        try {

            $res = $this->vendaRepository->deleteVenda($id);

            if ($res['code'] == 500) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function finishVenda(Request $request)
    {
        try {
            $dados = $request->all();

            $res = $this->vendaRepository->finishVenda($dados);

            if ($res['code'] == 500) {
                return response()->json(['message' => $res['message']], $res['code']);
            }

            return response()->json($res['message'], $res['code']);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    // Item
    public function showItem($id)
    {
        try {
            $res = $this->vendaRepository->getItemById($id);
            
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
            $res = $this->vendaRepository->createItem($dados);

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

            $res = $this->vendaRepository->updateItem($dados, $id);

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

            $res = $this->vendaRepository->deleteItem($id);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
}
