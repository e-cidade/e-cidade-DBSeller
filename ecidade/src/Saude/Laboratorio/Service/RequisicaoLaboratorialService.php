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

namespace ECidade\Saude\Laboratorio\Service;

use ECidade\Saude\Laboratorio\Repository\RequisicaoLaboratorialRepository;

/**
 * Class RequisicaoLaboratorialService
 *
 * @package ECidade\Saude\Laboratorio\Service
 */
class RequisicaoLaboratorialService
{
    /**
     * @var RequisicaoLaboratorialRepository
     */
    private $repository;

    /**
     * RequisicaoLaboratorialService constructor.
     * @param  RequisicaoLaboratorialRepository  $repository
     */
    public function __construct(RequisicaoLaboratorialRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param $id
     * @return bool|\RequisicaoLaboratorial|null
     * @throws \Exception
     */
    public function getRequisicaoLaboratorial($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param  $codigoRequisicao
     * @return array|bool
     */
    public function getMateriasPorRequisicao($codigoRequisicao)
    {
        return $this->repository->getMateriaisPorRequisicao($codigoRequisicao, new \cl_lab_requiitem());
    }

    /**
     * @param  $codigoRequisicao
     * @param  $codigoMaterial
     * @return array|bool
     */
    public function getExamesPorMaterialRequisicao(
        $codigoRequisicao,
        $codigoMaterial
    ) {
        return $this->repository->getExamesPorMaterialRequisicao(
            $codigoRequisicao,
            $codigoMaterial,
            new \cl_lab_requiitem()
        );
    }

    /**
     * @param  $codigoRequisicao
     * @return array|bool
     */
    public function getExamesPorRequisicao(
        $codigoRequisicao
    ) {
        return $this->repository->getExamesPorRequisicao(
            $codigoRequisicao,
            new \cl_lab_requiitem()
        );
    }

    /**
     * @param $codigoRequisicao
     * @return array|bool
     */
    public function getSolicitanteRequisicao($codigoRequisicao)
    {
        return $this->repository->getSolicitanteRequisicao($codigoRequisicao);
    }

    /**
     * @param $id
     * @return bool|\RequisicaoLaboratorial|null
     * @throws \Exception
     */
    public function getRequisicao($id)
    {
        return $this->repository->getRequisicao($id);
    }

    /**
     * @param  array  $idsItensRequisicao
     * @param  int  $limit
     * @param  int  $offset
     * @return array
     */
    public function getRelacaoRequisicaoExames($idsItensRequisicao, $limit, $offset)
    {
        return $this->repository->getRelacaoRequisicaoExames(
            implode(', ', $idsItensRequisicao),
            $limit,
            $offset
        );
    }

    /**
     * Recupera os exames vínculados a um grupo de liberação.
     * @param $grupoLiberacaoId
     * @return array|\stdClass[]
     */
    public function getExamesByGrupoLiberacao($grupoLiberacaoId)
    {
        if (empty($grupoLiberacaoId)) {
            return [];
        }
        return $this->repository->getExamesByGrupoLiberacao($grupoLiberacaoId);
    }
}
