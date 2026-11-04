<?php

namespace ECidade\Tributario\Cadastro\Repository;

use ECidade\Tributario\Library\Repository;
use ECidade\Tributario\Cadastro\Model\Iptubase;
use ECidade\Tributario\Cadastro\Collection\IptubaseCollection;

class IptubaseRepository extends Repository
{
    /**
     * @return Iptubase
     */
    public function make($object)
    {
        if (empty($object)) {
            return null;
        }

        $iptubase = new Iptubase();

        $iptubase->setMatric($object->j01_matric);
        $iptubase->setNumcgm($object->j01_numcgm);
        $iptubase->setIdbql($object->j01_idbql);
        $iptubase->setBaixa($object->j01_baixa);
        $iptubase->setCodave($object->j01_codave);
        $iptubase->setFracao($object->j01_fracao);

        return $iptubase;
    }

    /**
     * @return Iptubase
     */
    public function find($matricula)
    {
        $sql = $this->dao->sql_query_file($matricula);

        $result = $this->dataBase->execute($sql);

        $object = $this->dataBase->fetchRow($result, 0);

        return $this->make($object);
    }

    /**
     * @return IptubaseCollection
     */
    public function findAll($where = '')
    {
        $sql = $this->dao->sql_query_file(null, "*", null, $where);

        return $this->findAllFromSQL($sql);
    }

    /**
     * @return IptubaseCollection
     */
    public function findAllFromSQL($sql)
    {
        $result = $this->dataBase->execute($sql);

        return new IptubaseCollection($result);
    }


    public function getConstrucoesMatricula($matricula, $codigosAreaIrregular = [])
    {
        $sql = $this->dao->sql_query_construcoesMatricula($matricula, $codigosAreaIrregular);
        $result = $this->dataBase->execute($sql);
        return $this->dataBase->getCollectionByRecord($result);
    }
}
