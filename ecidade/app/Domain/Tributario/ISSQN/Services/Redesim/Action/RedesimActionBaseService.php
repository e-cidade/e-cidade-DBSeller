<?php

namespace App\Domain\Tributario\ISSQN\Services\Redesim\Action;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishment;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishmentData;

class RedesimActionBaseService
{
    /**
     * @var array
     */
    protected $establishmentData;

    /**
     * @var ProcessedEstablishment
     */
    protected $processedEstablishment;

    /**
     * @var Issbase
     */
    protected $issBase;

    /**
     * @var Cgm
     */
    protected $cgm;

    /**
     * @param ProcessedEstablishment $processedEstablishment
     * @return RedesimActionBaseService
     */
    public function setProcessedEstablishment($processedEstablishment)
    {
        $this->processedEstablishment = $processedEstablishment;
        return $this;
    }

    /**
     * @param Issbase $issBase
     * @return RedesimActionBaseService
     */
    public function setIssBase($issBase)
    {
        $this->issBase = $issBase;
        return $this;
    }

    /**
     * @param Cgm $cgm
     * @return RedesimActionBaseService
     */
    public function setCgm($cgm)
    {
        $this->cgm = $cgm;
        return $this;
    }

    /**
     * @return array
     */
    public function getEstablishmentData()
    {
        return $this->establishmentData;
    }

    /**
     * @throws \Exception
     */
    public function findEstablishmentData()
    {
        $processedEstablishmentData = ProcessedEstablishmentData::whereProcessedEstablishment(
            $this->processedEstablishment->q190_sequencial
        )->first();

        if (!$processedEstablishmentData) {
            throw new \Exception("Dados do estabelecimento não encontrados.");
        }

        $this->establishmentData = json_decode($processedEstablishmentData->q193_data, true);
    }
}
