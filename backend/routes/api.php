<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' =>'/v1'], function() {

    // Autentificação
    Route::group(['prefix' =>'/oauth'], function() {
        Route::post('/login', 'Auth\AuthController@login');
        Route::post('/alter-password', 'Auth\AuthController@alterSenha')->middleware('auth:api');
        Route::get('/me', 'Auth\AuthController@me')->middleware('auth:api');
        Route::get('/logout', 'Auth\AuthController@logout')->middleware('auth:api');
    });
    
    Route::group(['prefix' =>'/dashboard'], function() {
        
        Route::get('/vendas-dia','Dashboard\DashboardController@getVendasDia')->middleware(['auth:api', 'scope:admin']);
        Route::get('/vendas-mes','Dashboard\DashboardController@getVendasMes')->middleware(['auth:api', 'scope:admin']);
        Route::get('/vendas-total','Dashboard\DashboardController@getVendasTotal')->middleware(['auth:api', 'scope:admin']);
        Route::get('/clientes-total','Dashboard\DashboardController@getTotalClientes')->middleware(['auth:api', 'scope:admin']);
        Route::get('/produtos-enviados','Dashboard\DashboardController@getProdutosEnviados')->middleware(['auth:api', 'scope:admin']);
        Route::get('/produtos-cadastrados','Dashboard\DashboardController@getProdutosCadastrados')->middleware(['auth:api', 'scope:admin']);
        Route::get('/produtos-pagos','Dashboard\DashboardController@getProdutosPagos')->middleware(['auth:api', 'scope:admin']);
        Route::get('/produtos-estoque','Dashboard\DashboardController@getProdutosEstoque')->middleware(['auth:api', 'scope:admin']);
        Route::get('/produtos-vendidos','Dashboard\DashboardController@getProdutosVendidos')->middleware(['auth:api', 'scope:admin']);
        Route::get('/contas-receber','Dashboard\DashboardController@getContasReceber')->middleware(['auth:api', 'scope:admin']);
        
    });
    
    Route::group(['prefix' =>'/clientes'], function() {
        
        Route::get('/','Cliente\ClienteController@index')->middleware(['auth:api', 'scope:admin']);
        Route::get('/total','Cliente\ClienteController@total')->middleware(['auth:api', 'scope:admin']);
        Route::get('/{id}','Cliente\ClienteController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','Cliente\ClienteController@store')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','Cliente\ClienteController@update')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','Cliente\ClienteController@destroy')->middleware(['auth:api', 'scope:admin']);

    });
    
    Route::group(['prefix' =>'/fornecedores'], function() {
        
        Route::get('/','Fornecedor\FornecedorController@index')->middleware(['auth:api', 'scope:admin']);
        Route::get('/{id}','Fornecedor\FornecedorController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','Fornecedor\FornecedorController@store')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','Fornecedor\FornecedorController@update')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','Fornecedor\FornecedorController@destroy')->middleware(['auth:api', 'scope:admin']);

    });
    
    Route::group(['prefix' =>'/users'], function() {
        
        Route::get('/','User\UserController@index')->middleware(['auth:api', 'scope:admin']);
        Route::get('/{id}','User\UserController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','User\UserController@store')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','User\UserController@update')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','User\UserController@destroy')->middleware(['auth:api', 'scope:admin']);

    });
    
    Route::group(['prefix' =>'/categorias'], function() {
        
        Route::get('/','Categoria\CategoriaController@index')->middleware(['auth:api', 'scope:admin']);
        Route::get('/{id}','Categoria\CategoriaController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','Categoria\CategoriaController@store')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','Categoria\CategoriaController@update')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','Categoria\CategoriaController@destroy')->middleware(['auth:api', 'scope:admin']);

    });
    
    Route::group(['prefix' =>'/produtos'], function() {
        
        Route::get('/','Produto\ProdutoController@index')->middleware(['auth:api', 'scope:admin']);
        Route::get('/estoque','Produto\ProdutoController@estoque')->middleware(['auth:api', 'scope:admin']);
        Route::get('/masculino','Produto\ProdutoController@perfumeMasculino')->middleware(['auth:api', 'scope:admin']);
        Route::get('/feminino','Produto\ProdutoController@perfumeFeminino')->middleware(['auth:api', 'scope:admin']);
        Route::get('/pago','Produto\ProdutoController@pago')->middleware(['auth:api', 'scope:admin']);
        Route::get('/enviados','Produto\ProdutoController@enviados')->middleware(['auth:api', 'scope:admin']);
        Route::get('/vendidos','Produto\ProdutoController@vendidos')->middleware(['auth:api', 'scope:admin']);
        
        Route::get('/{id}','Produto\ProdutoController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','Produto\ProdutoController@store')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/masculino','Produto\ProdutoController@storeDolarMasculino')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/feminino','Produto\ProdutoController@storeDolarFeminino')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','Produto\ProdutoController@update')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','Produto\ProdutoController@destroy')->middleware(['auth:api', 'scope:admin']);

    });
    
    Route::group(['prefix' =>'/estoques'], function() {
        
        Route::get('/','Estoque\EstoqueController@index')->middleware(['auth:api', 'scope:admin']);        
        Route::get('/{id}','Estoque\EstoqueController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','Estoque\EstoqueController@store')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','Estoque\EstoqueController@update')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','Estoque\EstoqueController@destroy')->middleware(['auth:api', 'scope:admin']);

    });
    
    Route::group(['prefix' =>'/vendas'], function() {
        
        Route::get('/','Venda\VendaController@index')->middleware(['auth:api', 'scope:admin']);
        Route::get('/a-receber','Venda\VendaController@aReceber')->middleware(['auth:api', 'scope:admin']);
        Route::get('/{id}','Venda\VendaController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','Venda\VendaController@store')->middleware(['auth:api', 'scope:admin']);   
        Route::post('/finish','Venda\VendaController@finishVenda')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','Venda\VendaController@update')->middleware(['auth:api', 'scope:admin']);
        Route::put('/{id}/receber','Venda\VendaController@updateReceber')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','Venda\VendaController@destroy')->middleware(['auth:api', 'scope:admin']);

        Route::group(['prefix' =>'/item'], function() {
            Route::get('/{id}','Venda\VendaController@showItem')->middleware(['auth:api', 'scope:admin']);
            Route::post('/','Venda\VendaController@storeItem')->middleware(['auth:api', 'scope:admin']);
            Route::put('/{id}','Venda\VendaController@updateItem')->middleware(['auth:api', 'scope:admin']);
            Route::delete('/{id}','Venda\VendaController@destroyItem')->middleware(['auth:api', 'scope:admin']);

        });
    });
    
    Route::group(['prefix' =>'/entregas'], function() {
        
        Route::get('/','Entrega\EntregaController@index')->middleware(['auth:api', 'scope:admin']);
        Route::get('/{id}','Entrega\EntregaController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','Entrega\EntregaController@store')->middleware(['auth:api', 'scope:admin']);   
        Route::post('/finish','Entrega\EntregaController@finishEntrega')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','Entrega\EntregaController@update')->middleware(['auth:api', 'scope:admin']);
        Route::put('/{id}/dar-baixa','Entrega\EntregaController@baixaEntrega')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','Entrega\EntregaController@destroy')->middleware(['auth:api', 'scope:admin']);

        Route::group(['prefix' =>'/item'], function() {
            Route::get('/{id}','Entrega\EntregaController@showItem')->middleware(['auth:api', 'scope:admin']);
            Route::post('/','Entrega\EntregaController@storeItem')->middleware(['auth:api', 'scope:admin']);
            Route::put('/{id}','Entrega\EntregaController@updateItem')->middleware(['auth:api', 'scope:admin']);
            Route::delete('/{id}','Entrega\EntregaController@destroyItem')->middleware(['auth:api', 'scope:admin']);

        });
    });
    
    Route::group(['prefix' =>'/despesas'], function() {
        
        Route::get('/','Despesa\DespesaController@index')->middleware(['auth:api', 'scope:admin']);
        Route::get('/movimentacao','Despesa\DespesaController@movimentacao')->middleware(['auth:api', 'scope:admin']);
        Route::get('/{id}','Despesa\DespesaController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','Despesa\DespesaController@store')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','Despesa\DespesaController@update')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','Despesa\DespesaController@destroy')->middleware(['auth:api', 'scope:admin']);

    });
    
    Route::group(['prefix' =>'/dolars'], function() {
        
        Route::get('/','Dolar\DolarController@index')->middleware(['auth:api', 'scope:admin']);
        Route::get('/{id}','Dolar\DolarController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','Dolar\DolarController@store')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','Dolar\DolarController@update')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','Dolar\DolarController@destroy')->middleware(['auth:api', 'scope:admin']);

    });
    
    Route::group(['prefix' =>'/movition'], function() {
        
        Route::get('/','Movition\MovitionController@index')->middleware(['auth:api', 'scope:admin']);
        Route::get('/geral','Movition\MovitionController@geral')->middleware(['auth:api', 'scope:admin']);
        Route::get('/eletronico','Movition\MovitionController@eletronico')->middleware(['auth:api', 'scope:admin']);
        
        Route::get('/{id}','Movition\MovitionController@show')->middleware(['auth:api', 'scope:admin']);
        
        Route::post('/','Movition\MovitionController@store')->middleware(['auth:api', 'scope:admin']);
        
        Route::put('/{id}','Movition\MovitionController@update')->middleware(['auth:api', 'scope:admin']);
        
        Route::delete('/{id}','Movition\MovitionController@destroy')->middleware(['auth:api', 'scope:admin']);

    });
    
    Route::group(['prefix' =>'/relatorios'], function() {
        
        Route::get('/vendas','Relatorio\RelatorioController@vendas')->middleware(['auth:api', 'scope:admin']);
        
        Route::get('/clientes','Relatorio\RelatorioController@clientes')->middleware(['auth:api', 'scope:admin']);
        
        Route::get('/estoque','Relatorio\RelatorioController@estoque')->middleware(['auth:api', 'scope:admin']);
        
        Route::get('/vendidos','Relatorio\RelatorioController@vendidos')->middleware(['auth:api', 'scope:admin']);
        
        Route::get('/entregas','Relatorio\RelatorioController@entregas')->middleware(['auth:api', 'scope:admin']);
        
        Route::get('/entrega-detalhes/{id}','Relatorio\RelatorioController@entregaDetalhes')->middleware(['auth:api', 'scope:admin']);

    });

});

