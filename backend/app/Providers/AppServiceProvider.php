<?php

namespace App\Providers;

use App\Services\SocialUserResolver;
use Illuminate\Support\ServiceProvider;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;

class AppServiceProvider extends ServiceProvider
{
    
    public $bindings = [
        SocialUserResolverInterface::class => SocialUserResolver::class,
    ];

    public function register()
    {
        $this->app->bind('App\Repositories\Contracts\Categoria\CategoriaRepositoryInterface','App\Repositories\Eloquent\Categoria\CategoriaRepository');
        $this->app->bind('App\Repositories\Contracts\Cliente\ClienteRepositoryInterface','App\Repositories\Eloquent\Cliente\ClienteRepository');
        $this->app->bind('App\Repositories\Contracts\User\UserRepositoryInterface','App\Repositories\Eloquent\User\UserRepository');
        $this->app->bind('App\Repositories\Contracts\Despesa\DespesaRepositoryInterface','App\Repositories\Eloquent\Despesa\DespesaRepository');
        $this->app->bind('App\Repositories\Contracts\Dashboard\DashboardRepositoryInterface','App\Repositories\Eloquent\Dashboard\DashboardRepository');
        $this->app->bind('App\Repositories\Contracts\Dolar\DolarRepositoryInterface','App\Repositories\Eloquent\Dolar\DolarRepository');
        $this->app->bind('App\Repositories\Contracts\Estoque\EstoqueRepositoryInterface','App\Repositories\Eloquent\Estoque\EstoqueRepository');
        $this->app->bind('App\Repositories\Contracts\Fornecedor\FornecedorRepositoryInterface','App\Repositories\Eloquent\Fornecedor\FornecedorRepository');
        $this->app->bind('App\Repositories\Contracts\Historico\HistoricoRepositoryInterface','App\Repositories\Eloquent\Historico\HistoricoRepository');
        $this->app->bind('App\Repositories\Contracts\Movition\MovitionRepositoryInterface','App\Repositories\Eloquent\Movition\MovitionRepository');
        $this->app->bind('App\Repositories\Contracts\Produto\ProdutoRepositoryInterface','App\Repositories\Eloquent\Produto\ProdutoRepository');
        $this->app->bind('App\Repositories\Contracts\Relatorio\RelatorioRepositoryInterface','App\Repositories\Eloquent\Relatorio\RelatorioRepository');
        $this->app->bind('App\Repositories\Contracts\Venda\VendaRepositoryInterface','App\Repositories\Eloquent\Venda\VendaRepository');
        $this->app->bind('App\Repositories\Contracts\Entrega\EntregaRepositoryInterface','App\Repositories\Eloquent\Entrega\EntregaRepository');
    }
    
    public function boot()
    {
        //
    }
}
