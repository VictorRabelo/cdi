<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes(function ($router) {

            $router->forAccessTokens();
        
        });

        Passport::tokensExpireIn(now()->addDays(30));

        Passport::refreshTokensExpireIn(now()->addDays(30));
    
        Passport::personalAccessTokensExpireIn(now()->addMonths(1));

        // Mandatory to define Scope
        Passport::tokensCan([
           'admin' => 'Acessos Restitos',
        ]);

        Passport::setDefaultScope([
            'admin',
        ]);

        
    }   
}
