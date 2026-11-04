<?php

namespace App\Domain\Tributario\ISSQN\Services\Base;

use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssBasePorte;

class IssBasePorteService
{
    /**
     * @throws \Throwable
     */
    public function save(IssBase $issBase, $businessSizeCode)
    {
        $issBasePorte = new IssBasePorte();
        $issBasePorte->q45_inscr = $issBase->q02_inscr;
        $issBasePorte->q45_codporte = $businessSizeCode;
        $issBasePorte->saveOrFail();

        return $issBasePorte;
    }
}
