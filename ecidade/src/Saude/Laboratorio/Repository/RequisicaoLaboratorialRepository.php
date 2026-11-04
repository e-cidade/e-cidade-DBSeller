<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\Saude\Laboratorio\Repository;

use db_utils;
use Exception;

class RequisicaoLaboratorialRepository
{
    /**
     * @var \cl_lab_requisicao
     */
    private $dao;

    /**
     * RequisicaoLaboratorialRepository constructor.
     * @param  \cl_lab_requisicao  $dao
     */
    public function __construct(\cl_lab_requisicao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param $id
     * @param  string  $columns
     * @return bool|\RequisicaoLaboratorial|null
     * @throws Exception
     */
    public function find($id, $columns = '*')
    {
        $sql = $this->dao->sql_query($id, $columns);
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar a Requisição.\nContate o suporte.");
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        $state = pg_fetch_array($rs);
        $requisicao = null;

        try {
            $requisicao = \RequisicaoLaboratorial::fromState($state);
        } catch (\BusinessException $e) {
            return null;
        }

        return $requisicao
            ->withRequisicaoExame();
    }

    /**
     * @param $codigoRequisicao
     * @param  \cl_lab_requiitem  $daoRequisicaoItem
     * @return array|bool
     */
    public function getMateriaisPorRequisicao($codigoRequisicao, \cl_lab_requiitem $daoRequisicaoItem)
    {
        $sql = $daoRequisicaoItem->sql_query_materiais_exame_requisicao(
            $codigoRequisicao,
            'la15_i_codigo, la15_c_descr',
            ''
        );

        $rs = db_query($sql);

        if (!$rs) {
            return false;
        }

        return pg_fetch_all($rs);
    }

    public function getExamesPorMaterialRequisicao(
        $codigoRequisicao,
        $codigoMaterial,
        \cl_lab_requiitem $daoRequisicaoItem
    ) {
        $sql = $daoRequisicaoItem->sql_query_materiais_exame_requisicao(
            $codigoRequisicao,
            'la08_i_codigo, la08_c_descr, la21_c_situacao, la21_i_codigo, la22_i_cgs, la21_observacao,
            la21_d_data, la21_d_entrega, la21_c_hora, la21_i_quantidade, la21_i_requisicao, la21_motivonovacoleta',
            "AND la15_i_codigo = {$codigoMaterial}"
        );

        $rs = db_query($sql);

        if (!$rs) {
            return false;
        }

        return pg_fetch_all($rs);
    }

    public function getExamesPorRequisicao(
        $codigoRequisicao,
        \cl_lab_requiitem $daoRequisicaoItem
    ) {
        $sql = $daoRequisicaoItem->sql_query_exame_requisicao(
            $codigoRequisicao,
            'la08_i_codigo, la08_c_descr, la21_c_situacao, la21_i_codigo, la22_i_cgs, la21_observacao,
            la21_d_data, la21_d_entrega, la21_c_hora, la21_i_quantidade, la21_i_requisicao, la21_motivonovacoleta'
        );

        $rs = db_query($sql);

        if (!$rs) {
            return false;
        }

        return pg_fetch_all($rs);
    }

    /**
     * @param $codigoRequisicao
     * @return object
     * @throws Exception
     */
    public function getSolicitanteRequisicao($codigoRequisicao)
    {
        $sql = $this->dao->sql_query_responsavel_requisicao($codigoRequisicao);
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar o solicitante da requisição {$codigoRequisicao}.");
        }

        if (pg_num_rows($rs) === 0) {
            throw new Exception("Solicitante não encontrado para a requisição {$codigoRequisicao}");
        }

        return pg_fetch_object($rs);
    }

    /**
     * @param $id
     * @param  string  $columns
     * @return bool|\RequisicaoLaboratorial|null
     * @throws \DomainException
     */
    public function getRequisicao($id, $columns = '*')
    {
        $sql = $this->dao->sql_query($id, $columns);
        $resultadoSql = db_query($sql);

        if (!$resultadoSql) {
            throw new \DomainException("Requisição não encontrada!");
        }

        if (pg_num_rows($resultadoSql) === 0) {
            return false;
        }

        $valores = pg_fetch_array($resultadoSql);
        $requisicao = null;

        try {
            $requisicao = \RequisicaoLaboratorial::fromState($valores);
        } catch (\Exception $e) {
            throw new \DomainException("Falha ao devolver requisição!");
        }

        return $requisicao;
    }

    /**
     * @param  string  $idsItensRequisicao
     * @param  int  $limit
     * @param  int  $offset
     * @return array
     */
    public function getRelacaoRequisicaoExames($idsItensRequisicao, $limit, $offset)
    {
        $sql = $this->dao->getRelacaoRequisicaoExames($idsItensRequisicao, $limit, $offset);
        $resultadoSql = db_query($sql);

        if (pg_num_rows($resultadoSql) === 0) {
            return [];
        }

        return db_utils::getCollectionByRecord($resultadoSql);
    }

    /**
     * Recupera os exames vínculados a um grupo de liberação.
     * @param  int  $grupoLiberacaoId
     * @return array|\stdClass[]
     */
    public function getExamesByGrupoLiberacao($grupoLiberacaoId)
    {
        $sql = $this->dao->getExamesByGrupoLiberacao($grupoLiberacaoId);
        $resultadoSql = db_query($sql);

        if (pg_num_rows($resultadoSql) === 0) {
            return [];
        }
        return db_utils::getCollectionByRecord($resultadoSql);
    }
}
