<?php

namespace App\Domain\RecursosHumanos\Pessoal\Repository;

use App\Domain\RecursosHumanos\Pessoal\Model\PrevidenciaComplementarModel;

class PrevidenciaComplementarRepository
{
    /**
     * Busca previdencia complementar do servidor
     * @param integer $matricula
     */
    public function find($matricula)
    {
        $previdenciaComplementarModel = new PrevidenciaComplementarModel();

        return $previdenciaComplementarModel->where('rh312_matricula', '=', $matricula)->get();
    }
}
