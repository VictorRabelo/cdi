<?php

namespace App\Http\Controllers\Venda;

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

    public function index()
    {
        try {

            $res = $this->vendaRepository->index();

            if (!$res) {
                return response()->json(['response' => 'Erro de Servidor'], 500);
            }

            return response()->json(['response' => $res], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro de servidor'], 500);
        }
    }
    
    public function all()
    {
        try{
            $vendas = Venda::with('produto', 'cliente', 'vendedor')->orderBy('id_venda', 'desc')->get();
            $qtdProduto = ProdutoVenda::orderBy('venda_id', 'desc')->get();
            
            $lucro = 0;
            $total = 0;
            $pago = 0;
            
            $venda = [];
            
            foreach ($vendas as $key => $value) {
                if($value['lucro'] == null) {
                    unset($vendas[$key]);
                } else {
                    $value->name_cliente = $value->cliente->name;
                    $value->name_vendedor = $value->vendedor->name;
                    $lucro = $value->lucro + $lucro;
                    $total = $value->total_final + $total;
                    $pago = $value->pago + $pago;
                    
                    array_push($venda, $value);
                }
            }

            $qtd_venda = 0;
            foreach ($qtdProduto as $value) {
                if($value->qtd_venda == null) {
                    unset($vendas[$key]);
                } else {
                    $qtd_venda = $value->qtd_venda + $qtd_venda;
                }
            }
            
            $media = $total / $qtd_venda;
            
            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => [
                    'vendas' => $vendas,
                    'venda' => $venda,
                    'lucro' => $lucro,
                    'total' => $total,
                    'pago' => $pago,
                    'qtd' => $qtd_venda,
                    'media' => $media,
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
    
    public function vendasDoDia()
    {
        try{
            date_default_timezone_set('America/Sao_Paulo');
            $data_now = date('Y-m-d');
            
            $vendas = ProdutoVenda::where('created_at', 'LIKE', '%'.$data_now.'%')->orderBy('created_at', 'desc')->get()->groupBy('venda_id');
            $count = $vendas->count();
            
            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => $count
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
    
    
    public function vendaEspecifica(Request $request) {
        try{
            date_default_timezone_set('America/Sao_Paulo');
            $data_start = date('Y-'.$request->date.'-01');
            $data_end = date('Y-'.$request->date.'-t');

            $vendas = Venda::with('produto', 'cliente', 'vendedor')->whereBetween('created_at', [$data_start, $data_end])->orderBy('id_venda', 'desc')->get();
            $qtdProduto = ProdutoVenda::whereBetween('created_at', [$data_start, $data_end])->orderBy('venda_id', 'desc')->get();
            
            $count = $vendas->count();
            
            $lucro = 0;
            $total = 0;
            $pago = 0;
            $qtd = 0;
            
            $venda = array();

            foreach ($vendas as $value) {
                $value->name_cliente = $value->cliente->name;
                $value->name_vendedor = $value->vendedor->name;
                $lucro = $value->lucro + $lucro;
                $total = $value->total_final + $total;
                $pago = $value->pago + $pago;
                array_push($venda, $value);
                
            }

            foreach ($qtdProduto as $value) {
                $qtd = $value->qtd_venda + $qtd;
            }
            
            if($qtd == 0 && $total == 0) {
                return response()->json([
                    'codeStatus' => 200,
                    'message' => 'Ok',
                    'detailMessage' => 'Listagem com sucesso',
                    'success' => true, 
                    'entity' => [
                        'venda' => $venda,
                        'lucro' => $lucro,
                        'total' => $total,
                        'pago' => $pago,
                        'qtd' => $qtd,
                        'media' => 0,
                        'count' => $count
                    ]
                ], 200);
            }
            
            $media = $total / $qtd;
            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => [
                    'vendas' => $vendas,
                    'venda' => $venda,
                    'lucro' => $lucro,
                    'total' => $total,
                    'pago' => $pago,
                    'qtd' => $qtd,
                    'media' => $media,
                    'count' => $count
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
    
    public function vendasDoMes()
    {
        try{
            
            date_default_timezone_set('America/Sao_Paulo');
            $data_start = date('Y-m-01');
            $data_end = date('Y-m-t');

            $vendas = ProdutoVenda::whereBetween('created_at', [$data_start, $data_end])->get();
            $count = $vendas->count();
            $lucro = 0;
            $total = 0;
            $pago = 0;
            
            $venda = array();
            $cliente;
            $vendedor;

            foreach ($vendas as $value) {
                $venda[] = $value->venda;
                $cliente = $value->venda->cliente;
                $vendedor = $value->venda->vendedor;
                $lucro = $value->venda->lucro + $lucro;
                $total = $value->venda->total_final + $total;
                $pago = $value->venda->pago + $pago;
                
            }

            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => [
                    'vendas' => $vendas,
                    'venda' => $venda,
                    'lucro' => $lucro,
                    'total' => $total,
                    'pago' => $pago,
                    'count' => $count
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

    public function total()
    {
        try{
            
            $vendas = Venda::all();
            $count = $vendas->count();

            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => $count
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

    public function createReceber(Request $request)
    {
        try{
            return response()->json($request);
        } catch(ModelNotFoundException $e) {
            return response()->json([

                'error' => $e->getMessage(),
                'codeStatus' => 500,
                'message' => 'Erro de servidor',
                'detailMessage' => $e->getMessage(),
                'success' => false

            ], 500);
        }
    }
    
    public function store(Request $request)
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

    public function show($id)
    {
        try{
            
            $venda = Venda::where('id_venda', '=', $id)->join('clientes','clientes.id_cliente', '=', 'vendas.cliente_id')->select('clientes.name', 'clientes.telefone', 'vendas.*')->first();
            $produtos = ProdutoVenda::with('produto')->where('venda_id', '=', $id)->orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => [
                    'venda' => $venda,
                    'produtos' => $produtos
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

    public function update(Request $request, $id)
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
}
