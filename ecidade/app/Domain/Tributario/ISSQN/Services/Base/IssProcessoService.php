<?php

namespace App\Domain\Tributario\ISSQN\Services\Base;

use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssProcesso;

class IssProcessoService
{
    /**
     * @throws \Throwable
     */
    public function save(IssBase $issBase, Processo $processo)
    {
        $issProcesso = new IssProcesso();
        $issProcesso->q14_inscr = $issBase->q02_inscr;
        $issProcesso->q14_proces = $processo->p58_codproc;
        $issProcesso->saveOrFail();

        return $issProcesso;
    }
}
