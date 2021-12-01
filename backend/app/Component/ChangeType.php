<?php

namespace App\Component;

use Illuminate\Http\Request;

use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Data;
use App\Models\Valor;
use App\Models\Frete;


class ChangeType
{
    public function itBr($file_name, $request){
        dd($request);
        return response()->json($request);
    }

    public function itUsa($file_name, $request){

    }

    public function itPy($file_name, $request){

    }
}
