<?php

namespace ECidade\Tributario\Arrecadacao\Repository;

use ECidade\Tributario\Arrecadacao\Model\DiversosTaxa;

class DiversosTaxaRepository extends \BaseClassRepository
{
    private $daoDiversosTaxa;

    public function __construct()
    {
        $this->daoDiversosTaxa = new \cl_diversostaxa();
    }

    public function persist(DiversosTaxa $entity)
    {
        $this->daoDiversosTaxa->dv15_sequencial = $entity->getSequencial();
        $this->daoDiversosTaxa->dv15_coddiver = $entity->getCodigoDiversos();
        $this->daoDiversosTaxa->dv15_codtaxa = $entity->getCodigoTaxa();
        $this->daoDiversosTaxa->dv15_taxaprincipal = $entity->getIsPrincipal() ? 'true' : 'false';
        $this->daoDiversosTaxa->dv15_valor = $entity->getValor();

        if (!empty($this->daoDiversosTaxa->dv15_sequencial)) {
            $this->daoDiversosTaxa->alterar($this->daoDiversosTaxa->dv15_sequencial);
        } else {
            $this->daoDiversosTaxa->incluir(null);
        }

        if ($this->daoDiversosTaxa->erro_status == "0") {
            throw new \Exception($this->daoDiversosTaxa->erro_msg);
        }

        return $this->daoDiversosTaxa->dv15_sequencial;
    }

    public function make($oObject)
    {
        $diversosTaxa = new DiversosTaxa();

        if (!empty($oObject->dv15_sequencial)) {
            $diversosTaxa->setSequencial($oObject->dv15_sequencial);
        }

        if (!empty($oObject->dv15_coddiver)) {
            $diversosTaxa->setCodigoDiversos($oObject->dv15_coddiver);
        }

        if (!empty($oObject->dv15_codtaxa)) {
            $diversosTaxa->setCodigoTaxa($oObject->dv15_codtaxa);
        }

        if (!empty($oObject->dv15_taxaprincipal)) {
            $diversosTaxa->setIsPrincipal($oObject->dv15_taxaprincipal);
        }

        if (!empty($oObject->dv15_valor)) {
            $diversosTaxa->setValor($oObject->dv15_valor);
        }

        return $diversosTaxa;
    }

    public function getTaxasByCodigoDiversos($iCodDiver)
    {
        $result = db_query($this->daoDiversosTaxa->sql_query(
            null,
            '*',
            'dv15_taxaprincipal desc',
            "dv15_coddiver = $iCodDiver"
        ));

        if (!$result) {
            throw new \Exception("Erro ao buscar as taxas");
        }

        return \db_utils::getCollectionByRecord($result);
    }
}
