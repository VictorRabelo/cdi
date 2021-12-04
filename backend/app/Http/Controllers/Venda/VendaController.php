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

            if (isset($res['code'])) {
                return response()->json(['message' => $res['message']], $res['code']);
            }

            return response()->json(['response' => $res], 201);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    public function aReceber()
    {
        try{
            
            $vendas = Venda::with('produto', 'cliente')->where('status', 'pendente')->get();
            $count = $vendas->count();
            $saldoReceber = 0;
            $saldoPago = 0;
            $totalRestante = 0;
            
            foreach($vendas as $value) {
                if($value->restante == 0){
                    $value->update(['status' => 'pago']);
                }
                $saldoReceber = $saldoReceber + $value->total_final;
                $saldoPago = $saldoPago + $value->pago;
                $totalRestante = $totalRestante + $value->restante;
            }

            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => [
                    'vendas' => $vendas,
                    'saldo_receber' => $saldoReceber,
                    'saldo_pago' => $saldoPago,
                    'total_restante' => $totalRestante,
                    'numero' => $count
                ]
            ], 200);

        }catch(ModelNotFoundException $e) {

            return response()->json([

                'error' => $e->getMessage(),
                'codeStatus' => 500,
                'message' => 'Erro de servidor',
                'detailMessage' => $e->getMessage(),
                'success' => false

            ], 500);
        }
    }

    public function storee(Request $request)
    {
        try{
            date_default_timezone_set('America/Sao_Paulo');
            $dt_now = date('Y-m-d');
            $vendedor = Auth::user()->id;
            $cliente = Cliente::where('id_cliente', $request->cliente_id)->firstOrFail();

            $produtosVerify = $request->produtos;
            $qtdProduto = count($produtosVerify);
            
            $restante = $request->total_final - $request->pago;

            if($cliente){
                $venda = Venda::create([
                    'vendedor_id' => $vendedor,
                    'cliente_id' => $cliente->id_cliente,
                    'total_final' => $request->total_final, 
                    'lucro' => $request->lucro,
                    'pago' => $request->pago,
                    'pagamento' => $request->pagamento,
                    'status' => $request->status,
                    'qtd_produto' => $qtdProduto,
                    'restante' => $restante
                ]);

                $name_cliente = $venda->cliente->name;
                $id_venda = $venda['id_venda'];
                
                if(!$request->prazo) {
                    $movition = Movition::create([
                        'venda_id' => $id_venda,
                        'data' => $dt_now,
                        'valor' => $request->pago,
                        'descricao' => $name_cliente,
                        'tipo' => 'entrada',
                        'status' => $request->status_movition
                    ]);
                } else {
                    $venda->update(['caixa' => $request->status_movition]);
                }
            }
            
            $produtos = array();

            foreach ($produtosVerify as $key => $value) {
                
                $estoque = Estoque::where('id_estoque', $value['id_estoque'])->first();
                $produto = $estoque->produto;
                
                $itemVenda = ProdutoVenda::create([
                    'venda_id' => $venda['id_venda'],
                    'produto_id' => $estoque['produto_id'],
                    'qtd_venda' => $value['qtdVenda'],
                    'lucro_venda' => $value['lucroVenda'],
                    'preco_venda' => $value['precoVenda'],
                ]);
                
                $estoque->decrement('und', $value['qtdVenda']);
                
                if($estoque['und'] == 0){
                    $produto->update(['status' => 'vendido']);
                }
                
            }
            
            
            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Criado com sucesso',
                'success' => true
            ], 200);

        }catch(ModelNotFoundException $e) {

            return response()->json([

                'error' => $e->getMessage(),
                'codeStatus' => 500,
                'message' => 'Erro de servidor',
                'detailMessage' => $e->getMessage(),
                'success' => false

            ], 500);
        }
    }

    public function updatee(Request $request, $id)
    {
        try {

            $venda = Venda::where('id_venda', $id)->first();
            
            if($venda->status == 'pendente') {
                $venda->update([
                    'pago' => $request->pagoUpdate,
                    'pagamento' => $request->pagamentoUpdate,
                    'status' => $request->statusUpdate
                ]);
            }
            
            $venda->update([
                'pagamento' => $request->pagamentoUpdate,
                'status' => $request->statusUpdate
            ]);
            

            return response()->json([

                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Update com sucesso',
                'success' => true, 
                'entity' => $venda

            ],200);

        } catch (ModelNotFoundException $e) {

            return response()->json([

                'error' => $e->getMessage(),
                'codeStatus' => 500,
                'message' => 'Error de servidor',
                'detailMessage' => $e->getMessage(),
                'success' => false
            
            ], 500);
        }

    }

    public function updateReceber(Request $request, $id)
    {
        try {
            date_default_timezone_set('America/Sao_Paulo');
            $dt_now = date('Y-m-d');
            
            $params = $request->all();
            
            if(isset($params['areceber'])){
                $venda = Venda::create([
                    'vendedor_id' => Auth::user()->id,
                    'cliente_id' => $params['cliente_id'],
                    'total_final' => $params['total_final'], 
                    'pago' => $params['pago'],
                    'pagamento' => 'dinheiro',
                    'status' => 'pendente',
                    'restante' => $params['restante'],
                    'caixa' => $params['caixa'],
                ]);
                
                return response()->json([

                    'codeStatus' => 200,
                    'message' => 'Ok',
                    'detailMessage' => 'Update com sucesso',
                    'success' => true

                ],200);
            }
            
            $venda = Venda::where('id_venda', $id)->first();
            $cliente = $venda->cliente->name;
            
            
            Movition::create([
                'venda_id' => $venda['id_venda'],
                'data' => $dt_now,
                'valor' => $request->pago,
                'descricao' => $cliente,
                'tipo' => 'entrada',
                'status' => 'geral'
            ]);
        
            $pago = $venda->pago + $request->pago;
            $result = $venda->total_final - $pago;

            if($result == 0) {
                $venda->update([
                    'pago' => $pago,
                    'restante' => 0.00,
                    'status' => 'pago'
                ]);
            } elseif($pago > $venda->total_final) {
                $venda->update([
                    'pago' => $pago,
                    'restante' => 0.00,
                    'status' => 'pago'
                ]);
            } elseif($venda->restante < 0) {
                $venda->update([
                    'pago' => $pago,
                    'restante' => 0.00,
                    'status' => 'pago'
                ]);
            } else {
                $venda->update([
                    'pago' => $pago,
                    'restante' => $result,
                ]);
            }

            return response()->json([

                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Update com sucesso',
                'success' => true

            ],200);

        } catch (ModelNotFoundException $e) {

            return response()->json([

                'error' => $e->getMessage(),
                'codeStatus' => 500,
                'message' => 'Error de servidor',
                'detailMessage' => $e->getMessage(),
                'success' => false
            
            ], 500);
        }

    }

    public function destroy($id)
    {
        try {

            $venda = Venda::where('id_venda', $id)->first();
            
            if(!empty($venda)){
                $venda->delete();
            }

            return response()->json([

                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Excluido com sucesso',
                'success' => true,
            ], 200);    


        } catch (ModelNotFoundException $e) {

            return response()->json([

                'error' => $e->getMessage(),
                'codeStatus' => 404,
                'message' => 'usuario nao encontrado',
                'detailMessage' => $e->getMessage(),
                'success' => false

            ], 404);
        }
    }
    
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

            return response()->json(['response' => $res], 201);
            
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

            return response()->json(['response' => 'Deletado com sucesso'], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
}
