<?php

namespace App\Domain\Saude\Ambulatorial\Repositories\Contracts;

use App\Domain\Saude\Ambulatorial\Requests\AtualizarCgsCpfRequest;

interface CgsUnidadeRepository
{
    /**
     * @param $dados
     * @return mixed
     */
    public function atualizarCgsCpf($dados);
}
