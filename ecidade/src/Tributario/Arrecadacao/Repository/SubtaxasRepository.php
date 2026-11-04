<?php

namespace ECidade\Tributario\Arrecadacao\Repository;

use ECidade\Tributario\Arrecadacao\Model\Subtaxas;

class SubtaxasRepository extends \BaseClassRepository
{
    private $daoSubtaxas;

    public function __construct()
    {
        $this->daoSubtaxas = new \cl_subtaxas();
    }

    public function persist(Subtaxas $entity)
    {
        $this->daoSubtaxas->ar54_sequencial = $entity->getSequencial();
        $this->daoSubtaxas->ar54_taxa = $entity->getSequencialTaxa();
        $this->daoSubtaxas->ar54_subtaxa = $entity->getSequencialSubtaxa();

        if (!empty($this->daoSubtaxas->ar54_sequencial)) {
            $this->daoSubtaxas->alterar($this->daoSubtaxas->ar54_sequencial);
        } else {
            $this->daoSubtaxas->incluir(null);
        }

        if ($this->daoSubtaxas->erro_status == "0") {
            throw new \Exception($this->daoSubtaxas->erro_msg);
        }

        return $this->daoSubtaxas->ar54_sequencial;
    }

    public function make($oObject)
    {
        $subTaxa = new Subtaxas();

        if (!empty($oObject->ar54_sequencial)) {
            $subTaxa->setSequencial($oObject->ar54_sequencial);
        }

        if (!empty($oObject->ar54_taxa)) {
            $subTaxa->setSequencialTaxa($oObject->ar54_taxa);
        }

        if (!empty($oObject->ar54_subtaxa)) {
            $subTaxa->setSequencialSubtaxa($oObject->ar54_subtaxa);
        }

        return $subTaxa;
    }

    public function getSubtaxas($sCampos = "*", $sWhere = "")
    {
        $result = db_query($this->daoSubtaxas->sql_query(null, $sCampos, "ar54_sequencial", $sWhere));

        if (!$result) {
            throw new \Exception("Erro ao buscar as subtaxas");
        }

        return \db_utils::getCollectionByRecord($result);
    }

    public function excluirSubtaxas($iSequencial = null, $sWhere = null)
    {
        try {
            $this->daoSubtaxas->excluir($iSequencial, $sWhere);
        } catch (\Exception $error) {
            throw new \Exception("Erro ao excluir a(s) subtaxa(s)");
        }
    }
}
