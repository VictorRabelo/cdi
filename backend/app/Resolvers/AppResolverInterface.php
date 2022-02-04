<?php

namespace App\Resolvers;

interface AppResolverInterface
{
    public function getVendas($queryParams, $date);
    public function finishSale($dados);
    
    public function getAllItemAvailable($queryParams);
    public function getEntregasDisponiveis();
}
