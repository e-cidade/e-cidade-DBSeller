<?php

namespace Ecidade\Tributario\Cadastro\Repository;

use BaseClassRepository;
use cl_tipoproprietario;
use db_utils;
use Ecidade\Tributario\Cadastro\Model\Tipoproprietario;

class TipoProprietarioRepository extends BaseClassRepository
{
    public function getLista()
    {
        $dao = new cl_tipoproprietario();
        $sql = $dao->sql_query();
        $result = db_query($sql);

        if (!$result) {
            throw new \DBException('Erro ao buscar os tipos de proprietarios.');
        }

        return $this->makeColletion(db_utils::getColectionByRecord($result));
    }

    public function make($item)
    {
        $entity = new Tipoproprietario();

        if (!empty($item->j163_tipoproprietario)) {
            $entity->setTipoProprietario($item->j163_tipoproprietario);
        }

        if (!empty($item->j163_descricao)) {
            $entity->setDescricao($item->j163_descricao);
        }

        if (!empty($item->j163_abreviatura)) {
            $entity->setAbreviatura($item->j163_abreviatura);
        }

        if ($item->j163_pesfisjur != "") {
            $entity->setPesfisjur($item->j163_pesfisjur);
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

    public function persist(Tipoproprietario $entity)
    {
        if ($entity->getDescricao() === null) {
            throw new \Exception("Necessário informar uma descrição!");
        }

        $dao = new cl_tipoproprietario();

        $dao->j163_tipoproprietario = $entity->getTipoproprietario();
        $dao->j163_descricao = $entity->getDescricao();
        $dao->j163_abreviatura = $entity->getAbreviatura();
        $dao->j163_pesfisjur = $entity->getPesfisjur();

        if (!empty($dao->j163_tipoproprietario)) {
            $dao->alterar($dao->j163_tipoproprietario);
        } else {
            $dao->incluir(null);
            $entity->setTipoproprietario($dao->j163_tipoproprietario);
        }

        if ($dao->erro_status == 0) {
            throw new \Exception($dao->erro_msg);
        }

        return $entity;
    }

    public function delete(Tipoproprietario $entity)
    {
        $dao = new cl_tipoproprietario();
        $dao->j163_tipoproprietario = $entity->getTipoproprietario();
        $dao->excluir($dao->j163_tipoproprietario);

        if ($dao->erro_status == 0) {
            throw new \Exception($dao->erro_msg);
        }

        return $entity;
    }
}
