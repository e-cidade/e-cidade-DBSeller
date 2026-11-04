<?php

namespace ECidade\Tributario\Cadastro\Repository;

use ECidade\Tributario\Cadastro\Collection\LoteCollection;
use ECidade\Tributario\Library\Repository;
use Lote;

class LoteRepository extends Repository
{
    /**
     * @return Lote
     */
    public function make($object)
    {
        if (empty($object)) {
            return null;
        }

        $lote = new Lote();

        $lote->setCodigoSetor($object->j34_setor);
        $lote->setSetor($object->j30_descr);
        $lote->setQuadra($object->j34_quadra);
        $lote->setLote($object->j34_lote);
        $lote->setAreaLote($object->j34_area);
        $lote->setCodigoBairro($object->j34_bairro);
        $lote->setBairro($object->j13_descr);
        $lote->setAreaMedida($object->j34_areal);
        $lote->setTotalConstruido($object->j34_totcon);
        $lote->setZona($object->j34_zona);
        $lote->setQuantidadeMatriculas($object->j34_quamat);
        $lote->setAreaPreservada($object->j34_areapreservada);
        $lote->setCodigoLote($object->j34_idbql);
        $lote->setCodigoLogradouro($object->j14_codigo);
        $lote->setLogradouro($object->j14_nome);
        $lote->setCep($object->j29_cep);
        $lote->setValorTestadaLote($object->j36_testad);
        $lote->setCodigoLoteamento($object->j34_loteam);
        $lote->setDescricaoLoteamento($object->j34_descr);
        $lote->setCodigoTipoLogradouro($object->j88_codigo);
        $lote->setSiglaTipoLogradouro($object->j88_sigla);

        return $lote;
    }

    /**
     * @return Lote
     */
    public function find($idbql)
    {
        $sql = $this->dao->sql_query_file($idbql);
        $result = $this->dataBase->execute($sql);
        $object = $this->dataBase->fetchRow($result, 0);
        return $this->make($object);
    }

    /**
     * @return LoteCollection
     */
    public function findAll($where = '')
    {
        $sql = $this->dao->sql_query_file(null, "*", null, $where);

        return $this->findAllFromSQL($sql);
    }

    /**
     * @return LoteCollection
     */
    public function findAllFromSQL($sql)
    {
        $result = $this->dataBase->execute($sql);

        return new LoteCollection($result);
    }

    public function getConstrucoesLote($idbql, $codigosAreaIrregular = [])
    {
        $sql = $this->dao->sql_query_construcoesLote($idbql, $codigosAreaIrregular);
        $result = $this->dataBase->execute($sql);
        return $this->dataBase->getCollectionByRecord($result);
    }
}
