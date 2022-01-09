<?php

namespace App\Resolvers;

interface AppResolverInterface
{
    public function getVendas($queryParams, $date);
    public function getEntregas($queryParams);
    
    public function getByIdVendas($queryParams);
    public function getByIdEntregas($queryParams);
    
    public function postVendas($request);
    public function postEntregas($request);
    
    public function putVendas($request);
    public function putEntregas($request);
    
    public function deleteVendas($request);
    public function deleteEntregas($request);

}
