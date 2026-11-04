<?php

namespace Ecidade\Tributario\Cadastro\Repository;

use BaseClassRepository;
use cl_tipopromitente;
use db_utils;
use ECidade\Tributario\Cadastro\Model\Tipopromitente;

class TipoPromitenteRepository extends BaseClassRepository
{
    public function getByVinculo($matricula)
    {
        $matricula = (!empty($matricula) ? $matricula : 0);

        $sql = "SELECT
                    DISTINCT tipopromitente.*
                FROM
                    tipoproprietariopromitente
                    INNER JOIN tipopromitente 
                    ON tipoproprietariopromitente.j165_tipopromitente = tipopromitente.j164_tipopromitente
                WHERE
                    j165_tipoproprietario IN (
                        SELECT
                            j01_tipoproprietario
                        FROM
                            IPTUBASE
                        where
                            j01_matric = $matricula 
                    )
                    or j165_tipoproprietario IN (
                        SELECT
                            j42_tipoproprietario
                        FROM
                            PROPRI
                        where
                            j42_matric = $matricula 
                    )
                ORDER BY
                    j164_tipopromitente ASC";

        $result = db_query($sql);

        //        if (pg_fetch_all($result) == false) {
//            $sql = "SELECT
//                    *
//                FROM
//                    tipopromitente
//                    INNER JOIN tipoproprietariopromitente ON j164_tipopromitente = j165_tipopromitente
//                    INNER JOIN tipoproprietario ON j163_tipoproprietario = j165_tipoproprietario
//                WHERE
//                    j163_descricao = 'Proprietário';
//            ";
//            $result = db_query($sql);
//        }

        if (!$result) {
            throw new \DBException('Erro ao buscar os tipos de promitentes vinculados.');
        }

        return $this->makeColletion(db_utils::getColectionByRecord($result));
    }

    public function getExisteSigla($siglaSugestao)
    {
        $dao = new cl_tipopromitente();
        $sql = $dao->sql_query("", "", "", "j164_promitipo = '{$siglaSugestao}'");
        $result = db_query($sql);

        if (!$result) {
            throw new \DBException('Erro ao buscar o tipo de promitente.');
        }

        if (pg_numrows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getByTipoPromitente($sequencial)
    {
        $dao = new cl_tipopromitente();
        $sql = $dao->sql_query("", "", "", "j164_tipopromitente = '{$sequencial}'");
        $result = db_query($sql);

        if (!$result) {
            throw new \DBException('Erro ao buscar o tipo de promitente.');
        }

        return $this->make(db_utils::fieldsMemory($result, 0));
    }

    public function getLista($sWhere = "")
    {
        $dao = new cl_tipopromitente();
        $sql = $dao->sql_query("", "", "", $sWhere);
        $result = db_query($sql);

        if (!$result) {
            throw new \DBException('Erro ao buscar os tipos de promitentes.');
        }

        return $this->makeColletion(db_utils::getColectionByRecord($result));
    }

    public function make($item)
    {
        $entity = new Tipopromitente();

        if (!empty($item->j164_tipopromitente)) {
            $entity->setTipopromitente($item->j164_tipopromitente);
        }

        if (!empty($item->j164_descricao)) {
            $entity->setDescricao($item->j164_descricao);
        }

        if (!empty($item->j164_promitipo)) {
            $entity->setPromitipo($item->j164_promitipo);
        }

        if (!empty($item->j164_abreviatura)) {
            $entity->setAbreviatura($item->j164_abreviatura);
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

    public function persist(Tipopromitente $entity)
    {
        if ($entity->getDescricao() === null && $entity->getAbreviatura() === null) {
            throw new \Exception("Necessário informar uma descrição e uma abreviatura!");
        }

        $dao = new cl_tipopromitente();

        $dao->j164_tipopromitente = $entity->getTipopromitente();
        $dao->j164_descricao      = $entity->getDescricao();
        $dao->j164_promitipo      = $entity->getPromitipo();
        $dao->j164_abreviatura    = $entity->getAbreviatura();

        if (!empty($dao->j164_tipopromitente)) {
            $dao->alterar($dao->j164_tipopromitente);
        } else {
            $dao->incluir(null);
        }

        if ($dao->erro_status == 0) {
            throw new \Exception($dao->erro_msg);
        }

        return $entity;
    }

    public function delete(Tipopromitente $entity)
    {
        $dao = new cl_tipopromitente();
        $dao->j164_tipopromitente = $entity->getTipopromitente();
        $dao->excluir($dao->j164_tipopromitente);

        if ($dao->erro_status == 0) {
            throw new \Exception($dao->erro_msg);
        }

        return $entity;
    }
}
