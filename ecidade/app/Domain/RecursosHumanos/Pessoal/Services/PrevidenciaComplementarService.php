<?php

namespace App\Domain\RecursosHumanos\Pessoal\Services;

use App\Domain\RecursosHumanos\Pessoal\Repository\PrevidenciaComplementarRepository;

class PrevidenciaComplementarService
{
    /**
     * @var PrevidenciaComplementarRepository
     */
    private $previdenciaComplementarRepository;

    public function __construct(PrevidenciaComplementarRepository $previdenciaComplementarRepository)
    {
        $this->previdenciaComplementarRepository = $previdenciaComplementarRepository;
    }

    /**
     * Busca a previdencia complementar da matricula
     * @param integer $matricula
     */
    public function buscarPorMatricula($matricula)
    {
        return $this->previdenciaComplementarRepository->find($matricula)->first();
    }

    /**
     * Deleta a previdencia complementar da matricula
     * @param integer $matricula
     */
    public function deletarPorMatricula($matricula)
    {
        return $this->previdenciaComplementarRepository->find($matricula)->first()->delete();
    }
}
