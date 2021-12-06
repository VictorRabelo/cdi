<?php

namespace App\Repositories\Eloquent\Estoque;

use App\Repositories\Contracts\Estoque\EstoqueRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;

use App\Models\Categoria;
use App\Models\Data;
use App\Models\Estoque;
use App\Models\Fornecedor;
use App\Models\Frete;
use App\Models\Produto;
use App\Models\Valor;
use App\Utils\Tools;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EstoqueRepository extends AbstractRepository implements EstoqueRepositoryInterface
{
    /**
     * @var Estoque
    */
    protected $model = Estoque::class;

    /**
     * @var Tools
     */
    protected $tools = Tools::class;

    public function index($queryParams){

        if(!isset($queryParams['status']) || $queryParams['status'] == 'all'){
            $dados = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->leftJoin('categorias', 'categorias.id_categoria', '=', 'produtos.categoria_id')->join('datas', 'datas.id_data', '=', 'produtos.data_id')->join('valores', 'valores.id_valor', '=', 'produtos.valor_id')->join('fretes', 'fretes.id_frete', '=', 'produtos.frete_id')->leftJoin('fornecedores', 'fornecedores.id_fornecedor', '=', 'produtos.fornecedor_id')->orderBy('status', 'asc')->orderBy('name', 'asc')->get();
        }

        if (isset($queryParams['status']) && $queryParams['status'] !== 'all') {
            $dados = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->leftJoin('categorias', 'categorias.id_categoria', '=', 'produtos.categoria_id')->join('datas', 'datas.id_data', '=', 'produtos.data_id')->join('valores', 'valores.id_valor', '=', 'produtos.valor_id')->join('fretes', 'fretes.id_frete', '=', 'produtos.frete_id')->leftJoin('fornecedores', 'fornecedores.id_fornecedor', '=', 'produtos.fornecedor_id')->where('status', $queryParams['status'])->orderBy('name', 'asc')->get();
        }

        if (!$dados) {
            return ['message' => 'Falha ao processar o estoque!', 'code' => 500];
        }

        foreach ($dados as $value) {
            $url = $this->tools->getUrlFile($value->path);
            $value->path = $url;
        }

        return $dados;
    }

    public function getById($id){
        $dados = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->leftJoin('categorias', 'categorias.id_categoria', '=', 'produtos.categoria_id')->join('datas', 'datas.id_data', '=', 'produtos.data_id')->join('valores', 'valores.id_valor', '=', 'produtos.valor_id')->join('fretes', 'fretes.id_frete', '=', 'produtos.frete_id')->leftJoin('fornecedores', 'fornecedores.id_fornecedor', '=', 'produtos.fornecedor_id')->where('id_estoque', $id)->first();
        if (!$dados) {
            return false;
        }
        
        $dados->path = $this->tools->getUrlFile($dados->path);
             
        return $dados;
    }

    public function create(Request $request){
        
        $dataNow = $this->dateNow();
        
        $dados = $request->all();

        if (!$dados['file']) {
            return ['message' => 'Não há foto do produto!', 'code' => 404];
        }
                
        $categoria = Categoria::where('id_categoria', $request->categoria_id)->first();
        if (!$categoria) {
            return ['message' => 'Falha ao processar a categoria!', 'code' => 500];
        }
        
        $fornecedor = Fornecedor::where('id_fornecedor', $request->fornecedor_id)->first();
        if (!$fornecedor) {
            return ['message' => 'Falha ao processar o fornecedor!', 'code' => 500];
        }
        
        $pathFile = $this->tools->parse_file($dados['file'], $categoria->categoria);
        
        $data = Data::create(['data_pedido' => $dataNow]);

        switch ($request->tipo) {
            case 'br':
                $valor = Valor::create(['total_site' => $request->total_site]);
                            
                $frete = Frete::create(['total_frete' => $request->total_frete]);
                    
                $produto = Produto::create([
                    'categoria_id' => $categoria->id_categoria,
                    'data_id' => $data->id_data,
                    'valor_id' => $valor->id_valor,
                    'frete_id' => $frete->id_frete,
                    'fornecedor_id' => $fornecedor->id_fornecedor,
                    'path' => $pathFile,
                    'name' => $request->name,
                    'descricao' => $request->descricao,
                    'preco' => $request->preco,
                    'valor_total' => $request->valor_total,
                    'tipo' => $request->tipo,
                    'status' => $request->status
                ]);

                Estoque::create([
                    'produto_id' => $produto->id_produto,
                    'und' => $request->und,
                ]);

                return ['message' => 'Cadastrado com sucesso!', 'code' => 200];
                
                break;

            case 'usa':
                
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
                    'path' => $pathFile,
                    'name' => $request->name,
                    'descricao' => $request->descricao,
                    'preco' => $request->preco,
                    'valor_total' => $request->valor_total,
                    'tipo' => $request->tipo,
                    'status' => $request->status
                ]);

                Estoque::create([
                    'produto_id' => $produto->id_produto,
                    'und' => $request->und
                ]);

                return ['message' => 'Cadastrado com sucesso!', 'code' => 200];

                break;
            
            case 'py':
                
                $valor = Valor::create(['valor_site' => $request->valor_site, 'dolar' => $request->dolar, 'total_site' => $request->total_site]);
                $frete = Frete::create(['frete_pjc_gyn' => $request->frete_pjc_gyn]);
                
                $produto = Produto::create([
                    'categoria_id' => $categoria->id_categoria,
                    'data_id' => $data->id_data,
                    'valor_id' => $valor->id_valor,
                    'frete_id' => $frete->id_frete,
                    'fornecedor_id' => $fornecedor->id_fornecedor,
                    'path' => $pathFile,
                    'name' => $request->name,
                    'descricao' => $request->descricao,
                    'preco' => $request->preco,
                    'valor_total' => $request->valor_total,
                    'tipo' => $request->tipo,
                    'status' => $request->status
                ]);

                Estoque::create([
                    'produto_id' => $produto->id_produto,
                    'und' => $request->und
                ]);
                
                return ['message' => 'Cadastrado com sucesso!', 'code' => 200];

                break;
        }

    }
    
    public function updateEstoque($dados, $id) {
        $query = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->leftJoin('categorias', 'categorias.id_categoria', '=', 'produtos.categoria_id')->join('datas', 'datas.id_data', '=', 'produtos.data_id')->join('valores', 'valores.id_valor', '=', 'produtos.valor_id')->join('fretes', 'fretes.id_frete', '=', 'produtos.frete_id')->leftJoin('fornecedores', 'fornecedores.id_fornecedor', '=', 'produtos.fornecedor_id')->where('produto_id', $id)->first();

        if (!$query) {
            return false;
        }
        
        if (isset($dados['file'])) {
            $dados['path'] = $this->tools->parse_file($dados['file'], $query->categoria, $query->path);
        } else {
            unset($dados['path']);
        }
        
        $estoque = Estoque::findOrFail($id);
        if (empty($estoque)) {
            return false;
        }
        
        $estoque->fill($dados);
        if (!$estoque->save()) {
            return ['message' => 'Falha ao atualizar dados!', 'code' => 500];
        }
        
        $produto = Produto::findOrFail($query->produto_id);
        if (empty($produto)) {
            return false;
        }
        
        $produto->fill($dados);
        if (!$produto->save()) {
            return ['message' => 'Falha ao atualizar dados!', 'code' => 500];
        }

        $data = $produto->data()->first();
        $valor = $produto->valor()->first();
        $frete = $produto->frete()->first();

        
        $data->fill($dados);
        if (!$data->save()) {
            return ['message' => 'Falha ao atualizar dados!', 'code' => 500];
        }
        
        $valor->fill($dados);
        if (!$valor->save()) {
            return ['message' => 'Falha ao atualizar dados!', 'code' => 500];
        }

        $frete->fill($dados);
        if (!$frete->save()) {
            return ['message' => 'Falha ao atualizar dados!', 'code' => 500];
        }

        return ['message' => 'Sucesso ao atualizar dados!', 'code' => 200];
    }

    public function deleteEstoque($id) {
        $dados = $this->model->findOrFail($id);

        if (empty($dados)) {
            return false;
        }

        $dados->produto()->first()->delete();

        $dados->delete();

        return true;
    }
}