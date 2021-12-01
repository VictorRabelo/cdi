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

    public function index()
    {
        $date = $this->dateMonth();

        $dados = $this->model->with('produto', 'cliente', 'vendedor')->whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('id_venda', 'desc')->get();
        if (!$dados) {
            return $this->messages->error;
        }

        $qtdProduto = ProdutoVenda::whereBetween('created_at', [$date['inicio'], $date['fim']])->orderBy('venda_id', 'desc')->get();
        if (!$qtdProduto) {
            return $this->messages->error;
        }

        $lucro = 0;
        $total = 0;
        $pago = 0;

        $venda = [];
        foreach ($dados as $key => $value) {
            if ($value['lucro'] == null) {
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
            $qtd_venda = $value->qtd_venda + $qtd_venda;
        }

        if($total > 0){
            $media = $total / $qtd_venda;
        } else {
            $media = 0;
        }

        return [
            'vendas' => $venda,
            'lucro' => $lucro,
            'total' => $total,
            'pago' => $pago,
            'qtd' => $qtd_venda,
            'media' => $media,
        ];
    }
}
