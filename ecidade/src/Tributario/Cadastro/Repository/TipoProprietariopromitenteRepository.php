<?php

namespace ECidade\Tributario\Cadastro\Repository;

use ECidade\Tributario\Cadastro\Model\Tipoproprietariopromitente;

class TipoProprietariopromitenteRepository extends \BaseClassRepository
{

    public function getVinculoByProprietario($tipoProprietario)
    {
        $sql = "SELECT * FROM cadastro.tipoproprietariopromitente WHERE j165_tipoproprietario = {$tipoProprietario}";
        $result = db_query($sql);

        if (!$result) {
            throw new \DBException('Erro ao buscar os tipos de proprietarios.');
        }

        return $this->makeColletion(\db_utils::getColectionByRecord($result));
    }

    public function getVinculoByPromitente($tipoPromitente)
    {
        $sql = "SELECT * FROM cadastro.tipoproprietariopromitente WHERE j165_tipopromitente = {$tipoPromitente}";
        $result = db_query($sql);

        if (!$result) {
            throw new \DBException('Erro ao buscar os tipos de promitentes.');
        }

        return $this->makeColletion(\db_utils::getColectionByRecord($result));
    }

    public function make($item)
    {
        $entity = new Tipoproprietariopromitente();

        if (!empty($item->j165_tipoproprietario)) {
            $entity->setTipoProprietario($item->j165_tipoproprietario);
        }

        if (!empty($item->j165_tipopromitente)) {
            $entity->setTipopromitente($item->j165_tipopromitente);
        }

        return $entity;
    }

    public function makeColletion($collection)
    {
        $dados = array();

        foreach ($collection as $item) {
            $dados[] = $this->make($item);
        }

        return $dados;
    }

    public function persist(Tipoproprietariopromitente $entity)
    {
        $dao = new \cl_tipoproprietariopromitente();

        $dao->j165_tipoproprietario = $entity->getTipoproprietario();
        $dao->j165_tipopromitente = $entity->getTipopromitente();

        if (!empty($dao->j165_tipoproprietariopromitente)) {
            $dao->alterar($dao->j165_tipoproprietariopromitente);
        } else {
            $dao->incluir(null);
        }

        if ($dao->erro_status == 0) {
            throw new \Exception($dao->erro_msg);
        }

        return $entity;
    }

    public function delete(Tipoproprietariopromitente $entity)
    {
        $dao = new \cl_tipoproprietariopromitente();
        $dao->j165_tipoproprietario = $entity->getTipoproprietario();
        $dao->excluir("", "j165_tipoproprietario = {$dao->j165_tipoproprietario}");

        if ($dao->erro_status == 0) {
            throw new \Exception($dao->erro_msg);
        }

        return $entity;
    }
}
