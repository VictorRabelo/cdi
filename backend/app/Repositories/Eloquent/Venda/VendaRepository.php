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
            $date = $this->dateFilter($queryParams['date']);
            $dados = $this->model->with('produto', 'cliente', 'vendedor')->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('id_venda', 'desc')->get();
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
            'data'         => $date['inicio'],
            'mounth'       => isset($queryParams['date'])? $queryParams['date']: date('m'),
        ];
    }
}
