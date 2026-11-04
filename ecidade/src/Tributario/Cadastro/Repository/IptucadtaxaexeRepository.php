<?php

namespace ECidade\Tributario\Cadastro\Repository;

use ECidade\Tributario\Library\Repository;
use ECidade\Tributario\Cadastro\Model\Iptucadtaxaexe;

final class IptucadtaxaexeRepository extends Repository
{
    public function make($object)
    {
        if (empty($object)) {
            return null;
        }

        $iptucadtaxaexe = new Iptucadtaxaexe();

        $iptucadtaxaexe->setIptucadtaxaexe($object->j08_iptucadtaxaexe);
        $iptucadtaxaexe->setIptucadtaxa($object->j08_iptucadtaxa);
        $iptucadtaxaexe->setTabrec($object->j08_tabrec);
        $iptucadtaxaexe->setValor($object->j08_valor);
        $iptucadtaxaexe->setAliq($object->j08_aliq);
        $iptucadtaxaexe->setAnousu($object->j08_anousu);
        $iptucadtaxaexe->setIptucalh($object->j08_iptucalh);
        $iptucadtaxaexe->setDbsysfuncoes($object->j08_db_sysfuncoes);
        $iptucadtaxaexe->setHistisen($object->j08_histisen);

        return $iptucadtaxaexe;
    }

    private function makeCollection($array)
    {
        $collection = array();

        if (empty($array)) {
            return $collection;
        }

        foreach ($array as $value) {
            $collection[] = $this->make((object)$value);
        }

        return $collection;
    }

    public function find($iptucadtaxaexe)
    {
        $sql = $this->dao->sql_query_file($iptucadtaxaexe);

        $result = $this->dataBase->execute($sql);

        $object = $this->dataBase->fetchRow($result, 0);

        return $this->make($object);
    }

    public function findAll($where = '')
    {
        $sql = $this->dao->sql_query_file(null, '*', null, $where);

        $result = $this->dataBase->execute($sql);

        $array = $this->dataBase->getCollectionByRecord($result);
        
        return $this->makeCollection($array);
    }
}
