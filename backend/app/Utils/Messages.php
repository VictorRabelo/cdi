<?php

namespace App\Utils;

class Messages
{
    const delete = ['message' => 'Deletado com Sucesso!', 'code' => 200];

    const update = ['message' => 'Atualizado com sucesso!', 'code' => 200];

    const create = ['message' => 'Cadastrado com Sucesso!', 'code' => 201];
    
    const notFound = ['message' => 'Não foi encontrado no Banco de Dados.', 'code' => 404];
    
    const error = ['message' => 'Falha no processamento de informação.', 'code' => 500];
}