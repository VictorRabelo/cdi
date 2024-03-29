<?php

namespace App\Utils;

use App\Models\Venda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Tools
{

    public function calcularSaldo($dados): float
    {
        $saldo = 0.00;
        foreach ($dados as $value) {
            $saldo = $saldo + $value->valor;
        }

        return $saldo;
    }

    public function calcularEntradaSaida($dados): float
    {
        $entrada = 0.00;
        $saida = 0.00;

        foreach ($dados as $value) {
            if ($value->tipo == 'entrada') {
                $entrada = $value->valor + $entrada;
            } else {
                $saida = $value->valor + $saida;
            }
        }

        $total = $entrada - $saida;

        return $total;
    }

    public function calculoVenda($dados, $date = null)
    {
        $totalVendas = Venda::select(DB::raw('sum(total_final) as total'))->get();

        $lucro = 0;
        $totalMensal = 0;
        $pago = 0;

        $dataSource = [];
        foreach ($dados as $item) {
            
            $lucro += $item->lucro;
            $totalMensal += $item->total_final;
            $pago += $item->pago;

            array_push($dataSource, $item);
        }

        return [
            'vendas'       => $dataSource,
            'totalMensal'  => $totalMensal,
            'totalVendas'  => $totalVendas[0]['total'],
            'lucro'        => $lucro,
            'pago'         => $pago,
            'data'         => is_null($date['inicio'])? date('Y-m-d'):$date['inicio'],
        ];
    }

    public function parse_file($file, $path, $file_old = "")
    {

        if (!empty($file_old) && Storage::disk('public')->exists($file_old)) { //deleta file old
            $this->_deletePhotoIfExists($file_old);
        }
        
        $file = preg_replace('#^data:image/[^;]+;base64,#', '', $file);
        $ext = $this->getExtensionFileName($file);
        $content = base64_decode($file);

        $file_name = md5(
            uniqid(
                microtime(),
                true
            )
        ) .'.'. $ext;

        $pathSave = "{$path}/{$file_name}";
        Storage::disk('public')->put($pathSave, $content);

        return $pathSave;
    }

    public function getExtensionFileName($img)
    {
        $extension = explode("/", $img);
        $ext = explode(";", $extension[1]);
        return $ext[0];
    }

    public function _deletePhotoIfExists($file_path): void
    {
        Storage::disk('public')->delete($file_path);
    }

    public function putFile($file, $path)
    {
        return Storage::disk('public')->put($path, $file);
    }

    public function getUrlFile($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }
        return null;
    }

    public function soNumero($str)
    {
        return preg_replace("/[^0-9]/", "", $str);
    }
    
    public function getPhoneFormattedAttribute($telefone): string
    {
        $phone = $telefone;

        $ac = substr($phone, 0, 2);
        $prefix = substr($phone, 2, 5);
        $suffix = substr($phone, 7);

        return "({$ac}) {$prefix}-{$suffix}";
    }
}
