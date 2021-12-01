<?php

namespace App\Http\Controllers\Estoque;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Data;
use App\Models\Valor;
use App\Models\Frete;
use App\Repositories\Contracts\Estoque\EstoqueRepositoryInterface;

class EstoqueController extends Controller
{
    private $estoqueRepository;

    public function __construct(EstoqueRepositoryInterface $estoqueRepository)
    {
        $this->estoqueRepository = $estoqueRepository;
    }

    public function index()
    {
        try{
            
            $estoque = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->join('categorias', 'categorias.id_categoria', '=', 'produtos.categoria_id')->join('datas', 'datas.id_data', '=', 'produtos.data_id')->join('valores', 'valores.id_valor', '=', 'produtos.valor_id')->join('fretes', 'fretes.id_frete', '=', 'produtos.frete_id')->join('fornecedores', 'fornecedores.id_fornecedor', '=', 'produtos.fornecedor_id')->orderBy('status', 'asc')->orderBy('name', 'asc')->get();

            $estoqueArray = [];
            $total_bytes = 0;

            foreach ($estoque as $value) {
                
                $file = storage_path('app/public/'.$value->path);
                $size = filesize($file);
                $result = file_get_contents($file);
                $value->path = base64_encode($result);
                $total_bytes = $size + $total_bytes;

                array_push($estoqueArray, $value);

            }

            
            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => [
                    'estoque' => $estoqueArray,
                    'bytes' => $total_bytes    
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
    
    
    public function especifico(Request $request)
    {
        try{
            $estoque = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->join('categorias', 'categorias.id_categoria', '=', 'produtos.categoria_id')->join('datas', 'datas.id_data', '=', 'produtos.data_id')->join('valores', 'valores.id_valor', '=', 'produtos.valor_id')->join('fretes', 'fretes.id_frete', '=', 'produtos.frete_id')->join('fornecedores', 'fornecedores.id_fornecedor', '=', 'produtos.fornecedor_id')->where('status', $request->status)->orderBy('name', 'asc')->get();

            $estoqueArray = [];
            $total_bytes = 0;

            foreach ($estoque as $value) {
                
                $file = storage_path('app/public/'.$value->path);
                $size = filesize($file);
                $result = file_get_contents($file);
                $value->path = base64_encode($result);
                $total_bytes = $size + $total_bytes;

                array_push($estoqueArray, $value);

            }
            
            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => $estoqueArray
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
    
    public function estoque()
    {
        try{
            
            $estoque = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->join('categorias', 'categorias.id_categoria', '=', 'produtos.categoria_id')->join('datas', 'datas.id_data', '=', 'produtos.data_id')->join('valores', 'valores.id_valor', '=', 'produtos.valor_id')->join('fretes', 'fretes.id_frete', '=', 'produtos.frete_id')->join('fornecedores', 'fornecedores.id_fornecedor', '=', 'produtos.fornecedor_id')->where('produtos.status', 'ok')->where('estoques.und', '>', '0')->orderBy('name', 'asc')->orderBy('name', 'asc')->get();
            
            $estoqueArray = [];

            foreach ($estoque as $value) {
                $file = storage_path('app/public/'.$value->path);
                $result = file_get_contents($file);
                $value->path = base64_encode($result);

                array_push($estoqueArray, $value);

            }

            
            return response()->json([
                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => $estoqueArray
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

    public function store(Request $request)
    {
        try{
            date_default_timezone_set('America/Sao_Paulo');
            $data_now = date('Y/m/d');
            
            if ($request->hasFile('foto')) {
                
                $file = $request->file('foto');

                if (file_exists($file) && !empty($file)) {
					
                    $file_name = $file->getClientOriginalName();
                    $credentials = $request->all();

                    $categoria = Categoria::where('categoria', 'like', '%'.$request->categoria.'%')->where('subcategoria', $request->subcategoria)->first();
                    $fornecedor = Fornecedor::where('fornecedor', 'like', '%'.$request->fornecedor.'%')->first();

                    switch ($request->tipo) {
                        case 'br':
                             
                            $data = Data::create(['data_pedido' => $data_now]);
                            
                            $valor = Valor::create(['total_site' => $request->total_site]);
                            
                            $frete = Frete::create(['total_frete' => $request->total_frete]);
                    
                            $produto = Produto::create([
                                'categoria_id' => $categoria->id_categoria,
                                'data_id' => $data->id_data,
                                'valor_id' => $valor->id_valor,
                                'frete_id' => $frete->id_frete,
                                'fornecedor_id' => $fornecedor->id_fornecedor,
                                'img' => $file_name,
                                'path' => $file->store($categoria->categoria),
                                'name' => $request->name,
                                'descricao' => $request->descricao,
                                'preco' => $request->preco,
                                'unitario' => $request->unitario,
                                'comissao' => $request->comissao,
                                'valor_total' => $request->valor_total,
                                'tipo' => $request->tipo,
                                'tipo_entrega' => $request->tipo_entrega,
                                'status' => $request->status
                            ]);

                            $estoque = Estoque::create([
                                'produto_id' => $produto->id_produto,
                                'und' => $request->und,
                                'pct' => $request->pct
                            ]);
                            
                            return response()->json([

                                'codeStatus' => 201,
                                'message' => 'Ok',
                                'detailMessage' => 'Cadastro com sucesso',
                                'success' => true
                
                            ], 201);

                            break;

                        case 'usa':
                            
                            $data = Data::create(['data_pedido' => $data_now]);

                            $valor = Valor::create([
                                'valor_site' => $request->valor_site,
                                'dolar' => $request->dolar,
                                'total_site' => $request->total_site
                            ]);
                            $frete = Frete::create([
                                'frete_mia_pjc' => $request->frete_mia_pjc,
                                'dolar_frete' => $request->dolar_frete,
                                'total_frete_mia_pjc' => $request->total_frete_mia_pjc,
                                'frete_pjc_gyn' => $request->frete_pjc_gyn,
                                'total_frete' => $request->total_frete
                            ]);
                            
                            $produto = Produto::create([
                                'categoria_id' => $categoria->id_categoria,
                                'data_id' => $data->id_data,
                                'valor_id' => $valor->id_valor,
                                'frete_id' => $frete->id_frete,
                                'fornecedor_id' => $fornecedor->id_fornecedor,
                                'img' => $file_name,
                                'path' => $file->store($categoria->name),
                                'name' => $request->name,
                                'descricao' => $request->descricao,
                                'preco' => $request->preco,
                                'unitario' => $request->unitario,
                                'comissao' => $request->comissao,
                                'valor_total' => $request->valor_total,
                                'tipo' => $request->tipo,
                                'tipo_entrega' => $request->tipo_entrega,
                                'status' => $request->status
                            ]);

                            $estoque = Estoque::create([
                                'produto_id' => $produto->id_produto,
                                'und' => $request->und,
                                'pct' => $request->pct
                            ]);
                            
                            return response()->json([

                                'codeStatus' => 201,
                                'message' => 'Ok',
                                'detailMessage' => 'Cadastro com sucesso',
                                'success' => true
                
                            ], 201);

                            break;
                    }
                    
                    // $path = $file->store();
                } else {
                    throw new ModelNotFoundException('Não tem foto');
                    return;
                }
            }

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
            
            $estoque = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->join('categorias', 'categorias.id_categoria', '=', 'produtos.categoria_id')->join('datas', 'datas.id_data', '=', 'produtos.data_id')->join('valores', 'valores.id_valor', '=', 'produtos.valor_id')->join('fretes', 'fretes.id_frete', '=', 'produtos.frete_id')->join('fornecedores', 'fornecedores.id_fornecedor', '=', 'produtos.fornecedor_id')->where('produto_id', $id)->first();
            $file = storage_path('app/public/'.$estoque->path);
            $size = filesize($file);
            $result = file_get_contents($file);
            $estoque->path = base64_encode($result);

            return response()->json([

                'codeStatus' => 200,
                'message' => 'Ok',
                'detailMessage' => 'Listagem com sucesso',
                'success' => true, 
                'entity' => $estoque

            ], 200);

        } catch(ModelNotFoundException $e){

            return response()->json([

                'error' => $e->getMessage(),
                'codeStatus' => 403,
                'message' => 'Nao autorizado. O usuario precisa ser autenticado',
                'detailMessage' => $e->getMessage(),
                'success' => false
            ], 403);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $estoque = Estoque::findOrFail($id);
            $estoque->update(['und' => $request->undUpdate, 'pct' => $request->pctUpdate]);

            $id = $estoque->produto_id;
            $produto = Produto::findOrFail($id);
            
            $data = $produto->data()->first();
            $valor = $produto->valor()->first();
            $frete = $produto->frete()->first();
            
            if($estoque->und > 0) {
                $produto->update(['status' => 'ok']);        
            }
            
            if($request->statusUpdate) {
                $produto->update(['status' => $request->statusUpdate]);        
            }

            $produto->update([
                'name' => $request->nameUpdate,
                'descricao' => $request->descricaoUpdate,
                'preco' => $request->precoUpdate,
                'unitario' => $request->unitarioUpdate,
                'valor_total' => $request->valor_totalUpdate
            ]);

            switch ($request->tipoUpdate) {
                
                case 'br':
                                        
                    $data->update([
                        'data_pedido' => $request->data_pedidoUpdate,
                        'data_gyn' => $request->data_gynUpdate
                    ]);
                    
                    $valor->update(['total_site' => $request->total_siteUpdate]);
                    
                    $frete->update(['total_frete' => $request->total_freteUpdate]);
    
                    return response()->json([
    
                        'codeStatus' => 200,
                        'message' => 'Ok',
                        'detailMessage' => 'Atualizado com sucesso',
                        'success' => true
                    
                    ], 200);

                break;

                case 'usa':
                            
                    $data->update([
                        'data_pedido' => $request->data_pedidoUpdate,
                        'data_gyn' => $request->data_gynUpdate,
                        'data_pjc' => $request->data_pjcUpdate,
                        'data_miami' => $request->data_miamiUpdate,
                    ]);
                    
                    $valor->update(['valor_site' => $request->valor_siteUpdate, 'dolar' => $request->dolarUpdate, 'total_site' => $request->total_siteUpdate ]);
                    
                    $frete->update([
                        'frete_mia_pjc' => $request->frete_mia_pjcUpdate,
                        'dolar_frete' => $request->dolar_freteUpdate,
                        'total_frete_mia_pjc' => $request->total_frete_mia_pjcUpdate,
                        'frete_pjc_gyn' => $request->frete_pjc_gynUpdate,
                        'total_frete' => $request->total_freteUpdate
                    ]);

                    return response()->json([

                        'codeStatus' => 200,
                        'message' => 'Ok',
                        'detailMessage' => 'Atualizado com sucesso',
                        'success' => true,
                        'entity' => $id
                
                    ], 200);

                break;

                case 'py':
                            
                    $data->update(['data_pedido' => $request->data_pedidoUpdate, 'data_gyn' => $request->data_gynUpdate, 'data_pjc' => $request->data_pjcUpdate ]);
                    
                    $valor->update(['valor_site' => $request->valor_siteUpdate, 'dolar' => $request->dolarUpdate, 'total_site' => $request->total_siteUpdate ]);
                    
                    $frete->update(['frete_pjc_gyn' => $request->frete_pjc_gynUpdate ]);
                            
                    return response()->json([

                        'codeStatus' => 200,
                        'message' => 'Ok',
                        'detailMessage' => 'Atualizado com sucesso',
                        'success' => true
                
                    ], 200);

                break;
            }
           
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

            $produto = Estoque::findOrFail($id);

            $produto->delete();

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
