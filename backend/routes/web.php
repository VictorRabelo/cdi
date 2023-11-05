<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/catalogo', function (Request $request) {
    $queryParams = $request->all();
    
    if (empty($queryParams['categoria']) && empty($queryParams['subcategoria'])) {
        $sql = 'SELECT * from `produtos` WHERE `produtos`.`status` = "ok" GROUP BY `produtos`.`categoria_id` ORDER BY `produtos`.`name` ASC, `produtos`.`preco` DESC';
        $products = DB::select($sql);
    } else {
        if (!empty($queryParams['categoria']) && empty($queryParams['subcategoria'])) {
            $sql = 'SELECT `produtos`.*, `categorias`.* from `produtos` inner join `categorias` on `categorias`.`id_categoria` = `produtos`.`categoria_id` WHERE `produtos`.`status` = "ok" AND `categorias`.`categoria` = "' . $queryParams['categoria'] . '" GROUP BY `produtos`.`name` ORDER BY `produtos`.`name` ASC, `produtos`.`preco` DESC';
        }

        if (empty($queryParams['categoria']) && !empty($queryParams['subcategoria'])) {
            $sql = 'SELECT `produtos`.*, `categorias`.* from `produtos` inner join `categorias` on `categorias`.`id_categoria` = `produtos`.`categoria_id` WHERE `produtos`.`status` = "ok" AND `categorias`.`subcategoria` = "' . $queryParams['subcategoria'] . '" GROUP BY `produtos`.`name` ORDER BY `produtos`.`name` ASC, `produtos`.`preco` DESC';
        }

        if (!empty($queryParams['categoria']) && !empty($queryParams['subcategoria'])) {
            $sql = 'SELECT `produtos`.*, `categorias`.* from `produtos` inner join `categorias` on `categorias`.`id_categoria` = `produtos`.`categoria_id` WHERE `produtos`.`status` = "ok" AND `categorias`.`categoria` = "' . $queryParams['categoria'] . '" AND `categorias`.`subcategoria` = "' . $queryParams['subcategoria'] . '" GROUP BY `produtos`.`name` ORDER BY `produtos`.`name` ASC, `produtos`.`preco` DESC';
        }

        $products = DB::select($sql);
    }

    return view('pdf.catalogo', ['products' => $products]);
});