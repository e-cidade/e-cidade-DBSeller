<?php

namespace App\Domain\Tributario\ISSQN\Services\Base;

use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssZona;

class IssZonaService
{
    /**
     * @throws \Throwable
     */
    public function save(IssBase $issBase, $zoneCode)
    {
        $issZona = new IssZona();
        $issZona->q35_inscr = $issBase->q02_inscr;
        $issZona->q35_zona = $zoneCode;
        $issZona->saveOrFail();

        return $issZona;
    }
}
