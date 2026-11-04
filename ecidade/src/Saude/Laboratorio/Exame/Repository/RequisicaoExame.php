<?php
/**
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

namespace ECidade\Saude\Laboratorio\Exame\Repository;

use cl_lab_requisicao;
use cl_lab_resultado;
use db_utils;
use DBDate;
use ECidade\Saude\Laboratorio\Repository\RequisicaoLaboratorialRepository;
use ECidade\Saude\Laboratorio\Service\RequisicaoLaboratorialService;
use Exception;
use RequisicaoExame as RequisicaoExameModel;

/**
 * Class RequisicaoExame
 * @package ECidade\Saude\Laboratorio\Exame
 */
class RequisicaoExame extends \BaseClassRepository
{
    /**
     * @var RequisicaoExameModel[]
     */
    private $requisicoesExame = array();

    /**
     * @var array
     */
    private $condicoesWhere = array();

    private $listaExames = array();

    /**
     * @return RequisicaoExameModel[]|\stdClass[]
     * @throws Exception
     */
    public function getRequisicoesExameComResultado($apenasIds = false)
    {
        $dao = new cl_lab_resultado();

        $where = !empty($this->condicoesWhere) ? implode(' AND ', $this->condicoesWhere) : '';

        if (!empty($this->listaExames)) {
            $where .= ' and la09_i_exame in ('.implode(', ', $this->listaExames).')';
        }

        $sql = $dao->sql_query_exames(null, 'la22_i_codigo, la21_i_codigo', null, $where);
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception('Erro ao buscar os exames das requisições');
        }

        $totalRequisicoes = pg_num_rows($rs);

        if (pg_num_rows($rs) === 0) {
            throw new Exception('Nenhuma requisição de exame encontrada.');
        }

        if ($apenasIds) {
            return db_utils::getCollectionByRecord($rs);
        }

        for ($contador = 0; $contador < $totalRequisicoes; $contador++) {
            $this->getRequisicaoExameByCodigo(db_utils::fieldsMemory($rs, $contador)->la21_i_codigo);
        }

        $this->condicoesWhere = array();

        return self::getInstance()->requisicoesExame;
    }

    /**
     * @param $codigo
     * @return RequisicaoExameModel
     */
    public function getRequisicaoExameByCodigo($codigo)
    {
        $instance = self::getInstance();

        if (!array_key_exists($codigo, $instance->requisicoesExame)) {
            $instance->requisicoesExame[$codigo] = new RequisicaoExameModel($codigo);
        }

        return $instance->requisicoesExame[$codigo];
    }

    /**
     * @param $codigoLaboratorio
     * @throws Exception
     */
    public function setLaboratorio($codigoLaboratorio)
    {
        $this->validaParametroInteiro($codigoLaboratorio);
        $this->condicoesWhere[] = "la24_i_laboratorio = {$codigoLaboratorio}";
    }

    /**
     * @param $parametro
     * @throws Exception
     */
    private function validaParametroInteiro($parametro)
    {
        if (!filter_var($parametro, FILTER_VALIDATE_INT)) {
            throw new Exception('Parâmetro inválido para busca de exames.');
        }
    }

    /**
     * @param $codigoSetor
     * @throws Exception
     */
    public function setSetor($codigoSetor)
    {
        //$this->validaParametroInteiro($codigoSetor);
        //$this->validaParametroInteiro($codigoSetor);
        $this->condicoesWhere[] = "la24_i_setor = {$codigoSetor}";
    }

    /**
     * @param $codigoExame
     * @throws Exception
     */
    public function setExame($codigoExame)
    {
        $this->validaParametroInteiro($codigoExame);
        $this->listaExames[] = $codigoExame;
    }

    /**
     * @param $situacao
     * @throws Exception
     */
    public function setSituacao($situacao)
    {
        if (!RequisicaoExameModel::getSituacoes($situacao)) {
            throw new Exception('Situação inválida.');
        }

        $this->condicoesWhere[] = "la21_c_situacao = '{$situacao}'";
    }

    /**
     * @param  DBDate  $dataInicio
     * @param  DBDate  $dataFim
     * @throws Exception
     */
    public function setPeriodo(DBDate $dataInicio, DBDate $dataFim)
    {
        $dateInterval = DBDate::getIntervaloEntreDatas($dataInicio, $dataFim);

        if ((bool)$dateInterval->invert === true) {
            throw new Exception('Data inicial é maior que a data final.');
        }

        $this->condicoesWhere[] = "la52_d_data between '{$dataInicio->getDate()}' and '{$dataFim->getDate()}'";
    }

    /**
     * Recupera os exames vínculados a um grupo de liberação.
     * @param  int  $grupoLiberacaoId
     * @return void
     * @throws Exception
     */
    public function setExamesByGrupoLiberacao($grupoLiberacaoId)
    {
        $exames = (new RequisicaoLaboratorialService(new RequisicaoLaboratorialRepository(new cl_lab_requisicao())))
            ->getExamesByGrupoLiberacao($grupoLiberacaoId);

        foreach ($exames as $exame) {
            $this->validaParametroInteiro($exame->la08_i_codigo);
            $this->listaExames[] = $exame->la08_i_codigo;
        }
    }
}
