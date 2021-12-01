<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface CrudRepositoryInterface
{
    public function all();
    public function listing(Request $request);
    public function show($id);
    public function where($collum, $value, $operator = null);
    public function store($dados);
    public function update($dados, $id);
    public function delete($id);
}