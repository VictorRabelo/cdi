<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\CodeStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\Dashboard\DashboardRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DashboardController extends Controller
{
    private $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }
    
    public function getVendasDia()
    {
        try {

            $res = $this->dashboardRepository->getVendasDia();

            if (isset($res['code']) && $res['code'] == 500) {
                return response()->json(['error' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function getVendasMes()
    {
        try {

            $res = $this->dashboardRepository->getVendasMes();

            if (isset($res['code']) && $res['code'] == 500) {
                return response()->json(['error' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function getVendasTotal()
    {
        try {

            $res = $this->dashboardRepository->getVendasTotal();

            if (isset($res['code']) && $res['code'] == 500) {
                return response()->json(['error' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function getProdutosEnviados()
    {
        try {
            $res = $this->dashboardRepository->getProdutosEnviados();

            if (!$res) {
                return response()->json(['error' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function getProdutosPagos()
    {
        try {
            $res = $this->dashboardRepository->getProdutosPagos();

            if (!$res) {
                return response()->json(['error' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }

    public function getProdutosEstoque()
    {
        try {

            $res = $this->dashboardRepository->getProdutosEstoque();

            if (!$res) {
                return response()->json(['error' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function getProdutosVendidos()
    {
        try {

            $res = $this->dashboardRepository->getProdutosVendidos();

            if (!$res) {
                return response()->json(['error' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function getContasReceber()
    {
        try {

            $res = $this->dashboardRepository->getContasReceber();

            if (!$res) {
                return response()->json(['error' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function getTotalClientes()
    {
        try {

            $res = $this->dashboardRepository->getTotalClientes();

            if (!$res) {
                return response()->json(['error' => 'Erro de Servidor'], 500);
            }

            return response()->json($res, 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
}
