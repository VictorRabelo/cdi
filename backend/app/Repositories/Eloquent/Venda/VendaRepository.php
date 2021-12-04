<?php

namespace App\Repositories\Eloquent\Venda;

use App\Models\ProdutoVenda;
use App\Models\Venda;
use App\Repositories\Contracts\Venda\VendaRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Utils\Messages;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendaRepository extends AbstractRepository implements VendaRepositoryInterface
{
    /**
     * @var Venda
     */
    protected $model = Venda::class;

    /**
     * @var Tools
     */
    protected $tools = Tools::class;

    /**
     * @var Messages
     */
    protected $messages = Messages::class;

    public function index($queryParams)
    {
        $totalVendas = $this->model->select(DB::raw('sum(total_final) as total'))->get();

        if(isset($queryParams['date'])) {
            if($queryParams['date'] == 0){
                $dados = $this->model->with('produto', 'cliente', 'vendedor')->orderBy('id_venda', 'desc')->get();
            } else {
                $date = $this->dateFilter($queryParams['date']);
                $dados = $this->model->with('produto', 'cliente', 'vendedor')->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('id_venda', 'desc')->get();
            }

            if (!$dados) {
                return $this->messages->error;
            }

        } else {
            $date = $this->dateMonth();
            $dados = $this->model->with('produto', 'cliente', 'vendedor')->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('id_venda', 'desc')->get();
            if (!$dados) {
                return $this->messages->error;
            }
        }
        
        $lucro = 0;
        $totalMensal = 0;
        $pago = 0;

        $dataSource = [];
        foreach ($dados as $key => $value) {
            if ($value['lucro'] == null) {
                unset($dados[$key]);
            } else {
                $value->name_cliente = $value->cliente->name;
                $value->name_vendedor = $value->vendedor->name;
                $lucro += $value->lucro;
                $totalMensal += $value->total_final;
                $pago += $value->pago;

                array_push($dataSource, $value);
            }
        }

        return [
            'vendas'       => $dataSource,
            'totalMensal'  => $totalMensal,
            'totalVendas'  => $totalVendas[0]['total'],
            'lucro'        => $lucro,
            'pago'         => $pago,
            'data'         => isset($date['inicio'])? $date['inicio']:date('Y-m-d'),
            'mounth'       => isset($queryParams['date'])? $queryParams['date']:date('m'),
        ];
    }

    public function create($dados)
    {
        if (!$dados) {
            $dados['vendedor_id'] = $this->userLogado()->id;
            return $this->store($dados);
        }
    }

    public function show($id){
        $dadosVenda = Venda::where('id_venda', '=', $id)->leftJoin('clientes','clientes.id_cliente', '=', 'vendas.cliente_id')->select('clientes.name as cliente', 'vendas.*')->first();
        if (!$dadosVenda) {
            return false;
        }
        
        $dadosProdutos = ProdutoVenda::with('produto')->where('venda_id', '=', $id)->orderBy('created_at', 'desc')->get();
        if (!$dadosProdutos) {
            return false;
        }

        return ['dadosVenda' => $dadosVenda, 'dadosProdutos' => $dadosProdutos];
    }

    public function getItemById($id){
        
    }

    public function createItem($dados){
        $result = ProdutoVenda::create($dados);
        if(!$result){
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        $dadosVenda = Venda::where('id_venda', '=', $dados['venda_id'])->first();
        if(!$dadosVenda){
            return ['message' => 'Falha ao procesar dados!', 'code' => 500];
        }

        $resultFinal = $dadosVenda->total_final + $result->preco_venda;
        $dadosVenda->update(['total_final' => $resultFinal]);
        return ['message' => 'Item cadastrado com sucesso!'];  
    }

    public function updateItem($dados, $id){
        
    }
    
    public function deleteItem($id){
        
    }
}
