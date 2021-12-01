<?php

namespace App\Repositories\Eloquent\Historico;

use App\Models\Historico;
use App\Repositories\Contracts\Historico\HistoricoRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;

class HistoricoRepository extends AbstractRepository implements HistoricoRepositoryInterface
{
    /**
     * @var Historico
    */
    protected $model = Historico::class;

    public function create($dados)
    {
        $dados['data'] = $this->dateNow();
        $dados['produto_id'] = $dados['id'];
        $dados['comentario'] = isset($dados['historico'])?$dados['historico']:$dados['comentario'];

        return $this->store($dados);
    }
}