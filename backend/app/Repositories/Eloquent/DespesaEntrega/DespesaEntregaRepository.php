<?php

namespace App\Repositories\Eloquent\DespesaEntrega;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\DespesaEntrega;
use App\Models\ProdutoVenda;
use App\Models\User;
use App\Repositories\Contracts\DespesaEntrega\DespesaEntregaRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Resolvers\AppResolverInterface;
use App\Utils\Messages;
use App\Utils\Tools;

class DespesaEntregaRepository extends AbstractRepository implements DespesaEntregaRepositoryInterface
{
    /**
     * @var DespesaEntrega
     */
    protected $model = DespesaEntrega::class;

    /**
     * @var AppResolverInterface
     */
    protected $baseApp = AppResolverInterface::class;

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
        if(isset($queryParams['adm'])) {
            if(isset($queryParams['date'])) {
                if($queryParams['date'] == 0){
                    $dados = $this->model->with('entregador')->orderBy('created_at', 'desc')->get();
                } else {
                    $date = $this->filterDate($queryParams['date']);
                    $dados = $this->model->with('entregador')->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('created_at', 'desc')->get();
                }

                if (!$dados) {
                    return $this->messages->error;
                }

            } else {
                $date = $this->dateMonth();
                $dados = $this->model->with('entregador')->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('created_at', 'desc')->get();
                if (!$dados) {
                    return $this->messages->error;
                }
            }

            $saldo = $this->tools->calcularSaldo($dados);

            return ['response' => $dados, 'saldo' => $saldo];
        }

        $id = auth()->user()->id;

        if(isset($queryParams['date']) && $queryParams['date'] !== '') {
            if($queryParams['date'] == 0){
                $dados = $this->model->with('entregador')->where('entregador_id', $id)->orderBy('created_at', 'desc')->get();
            } else {
                $date = $this->filterDate($queryParams['date']);
                $dados = $this->model->with('entregador')->where('entregador_id', $id)->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('created_at', 'desc')->get();
            }

            if (!$dados) {
                return $this->messages->error;
            }

        } else {
            $date = $this->dateToday();
            $dados = $this->model->with('entregador')->where('entregador_id', $id)->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('created_at', 'desc')->get();
            if (!$dados) {
                return $this->messages->error;
            }
        }

        $saldo = $this->tools->calcularSaldo($dados);

        return ['response' => $dados, 'saldo' => $saldo];
    }

    public function movimentacao()
    {
        $despesas = DespesaEntrega::where('created_at', 'LIKE', '%' . $this->dateNow() . '%')->get();
        if (!$despesas) {
            return ['message' => 'Falha ao processar as despesas!', 'code' => 500];
        }

        $vendas = ProdutoVenda::where('created_at', 'LIKE', '%' . $this->dateNow() . '%')->orderBy('created_at', 'desc')->get()->groupBy('venda_id');
        if (!$vendas) {
            return ['message' => 'Falha ao processar as vendas!', 'code' => 500];
        }

        return ['despesas' => $despesas, 'vendas' => $vendas];
    }

    public function create($dados)
    {
        if(!isset($dados['api'])){
            $resp = $this->baseApp->postDespesaEntrega($dados);
        }
        
        $dados['entregador_id'] = User::where('login', $dados['entregador_login'])->first()->id;
        
        
        $dados['valor'] =  $dados['valor'] / 2;

        if (!isset($dados['data'])) {
            $dados['data'] = $this->dateNow();
        }

        $res = DespesaEntrega::create($dados);

        if (!$res) {
            return ['message' => 'Falha ao cadastrar despesa!', 'code' => 500];
        }

        return $res;
    }
}
