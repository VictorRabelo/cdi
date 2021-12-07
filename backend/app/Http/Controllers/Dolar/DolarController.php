<?php

namespace App\Http\Controllers\Dolar;

use App\Enums\CodeStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\Contracts\Dolar\DolarRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DolarController extends Controller
{

    private $dolarRepository;

    public function __construct(DolarRepositoryInterface $dolarRepository)
    {
        $this->dolarRepository = $dolarRepository;
    }

    public function index()
    {
        try {

            $res = $this->dolarRepository->index();

            if (isset($res['code'])) {
                return response()->json($res, $res['code']);
            }

            return response()->json($res, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }


    public function show($id)
    {
        try {

            $res = $this->dolarRepository->show($id);

            if (empty($res)) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => $res], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $dados = $request->all();
            $res = $this->dolarRepository->store($dados);

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

            $res = $this->dolarRepository->update($dados, $id);

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

            $res = $this->dolarRepository->delete($id);

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => 'Deletado com sucesso'], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
}
