<?php

use Illuminate\Support\Facades\Route;
use App\Models\Venda;

Route::get('/teste', function(){

    $datas = Venda::all();

    date_default_timezone_set('America/Sao_Paulo');
    $data_now = date('Y-m-d');

    $pdf = PDF::loadView('pdf.cliente', compact('datas'));
    return $pdf->download('vendas-'.$data_now.'.pdf');
});