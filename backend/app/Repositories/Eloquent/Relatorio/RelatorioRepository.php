<?php

namespace App\Repositories\Eloquent\Relatorio;

use PDF;
use App\Models\Cliente;
use App\Models\Venda;
use App\Repositories\Contracts\Relatorio\RelatorioRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Utils\Messages;
use App\Utils\Tools;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RelatorioRepository extends AbstractRepository implements RelatorioRepositoryInterface
{
    /**
     * @var Tools
     */
    protected $tools = Tools::class;

    /**
     * @var Messages
     */
    protected $messages = Messages::class;

    public function vendas()
    {
        $data_now = $this->dateNow();
        
        $user = Auth::user()->id;
        $datas = Venda::with('produtos', 'cliente')->where('vendedor_id', $user)->orderBy('id_venda', 'desc')->get();
        
        if(empty($datas)){
            return $this->messages->notFound;
        }

        $pdf = PDF::loadView('pdf.vendas', compact('datas'));
        $result = $pdf->download($data_now.'.pdf');
        
        $base = base64_encode($result);

        return ['file' => $base,'data' => $data_now];
    }

    public function clientes()
    {
        $data_now = $this->dateNow();
        
        $datas = Cliente::with('vendas')->get();
        if(empty($datas)){
            return $this->messages->error;
        }

        foreach ($datas as $value) {
            $value->gastos = 0;
            $value->telefone = $this->tools->getPhoneFormattedAttribute($value->telefone);
            foreach ($value->vendas as $v) {
                $value->gastos = $v->pago + $value->gastos; 
            }
        }

        $resultado = $datas->sortByDesc('gastos');
        $result = $resultado->values()->all();
        $pdf = PDF::loadView('pdf.cliente', compact('result'));
        $file = $pdf->download($data_now.'.pdf');

        $base = base64_encode($file);

        return ['file' => $base,'data' => $data_now];
    }

    public function estoque()
    {
        $data_now = $this->dateNow();

        $datas = DB::table('estoques')->join('produtos', 'produtos.id_produto', '=', 'estoques.produto_id')->join('categorias', 'categorias.id_categoria', '=', 'produtos.categoria_id')->join('datas', 'datas.id_data', '=', 'produtos.data_id')->join('valores', 'valores.id_valor', '=', 'produtos.valor_id')->join('fretes', 'fretes.id_frete', '=', 'produtos.frete_id')->join('fornecedores', 'fornecedores.id_fornecedor', '=', 'produtos.fornecedor_id')->where('produtos.status', 'ok')->where('estoques.und', '>', '0')->orderBy('name', 'asc')->get();
        if(empty($datas)){
            return $this->messages->error;
        }
        
        $pdf = PDF::loadView('pdf.estoque', compact('datas'));
        $result = $pdf->download($data_now.'.pdf');
        
        $base = base64_encode($result);

        return ['file' => $base,'data' => $data_now];
    }

    public function vendidos()
    {
        $data_now = $this->dateNow();
    
        $datas = 0;
    
        $pdf = PDF::loadView('pdf.cliente', compact('datas'));
        $result = $pdf->download($data_now.'.pdf');
    
        $base = base64_encode($result);
    
        return ['file' => $base,'data' => $data_now];
            
        
    }
}
