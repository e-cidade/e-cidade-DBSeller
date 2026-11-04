<?php

namespace App\Domain\Saude\Ambulatorial\Services;

use App\Domain\Saude\Ambulatorial\Repositories\ProntuarioRepository;
use Illuminate\Database\Eloquent\Collection;

class StatusPacienteService
{

    /**
     * @param $filtros
     * @return Collection
     */
    public function buscaDados($filtros)
    {
        return (new ProntuarioRepository())->buscaMovimentacoes($filtros);
    }

    /**
     * @param $requisicao
     * @return Collection
     */
    public function buscaPacienteProntuario($requisicao)
    {
        return (new ProntuarioRepository())->buscaPacienteProntuario($requisicao);
    }
}
