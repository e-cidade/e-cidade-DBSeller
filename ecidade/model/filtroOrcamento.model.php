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

/**
 * controla filtros para relatorios de orcamento
 *
 */
class filtroOrcamento
{

    private $aInstituicoes = array();

    /**
     * @param array $aInstituicoes lista com as instituicoes
     */
    public function __construct($aInstituicoes = null)
    {
        $this->aInstituicoes = $aInstituicoes;
    }

    /**
     * Retorna os orgaos
     * @return array lista dos orgaos
     */
    public function getOrgaos($iAnoUsu)
    {
        $oDaoOrcOrgao = new cl_orcorgao;
        $sSqlOrgaos = $oDaoOrcOrgao->sql_query_file($iAnoUsu, null, "o40_orgao as orgao, o40_descr as descricao", "1");
        $rsOrgaos = $oDaoOrcOrgao->sql_record($sSqlOrgaos);

        return db_utils::getCollectionByRecord($rsOrgaos, false, false, true);
    }

    /**
     * Retorna as Unidades
     * @param integer $iAnoUsu ano base
     * @return array lista das unidades
     */
    public function getUnidades($iAnoUsu)
    {
        $oDaoOrcunidade = new cl_orcunidade;
        
        $sSqlUnidades = $oDaoOrcunidade->sql_query(
            $iAnoUsu,
            null,
            null,
            "o41_orgao as orgao,
                                                  o41_unidade as unidade,
                                                  o41_descr as descricao",
            "o41_orgao, o41_unidade"
        );
        $rsUnidades = $oDaoOrcunidade->sql_record($sSqlUnidades);
        
        return db_utils::getCollectionByRecord($rsUnidades, false, false, true);
    }

    /**
     * Retorna as funcoes
     *
     * @param integer $iAnoUsu ano corrente
     * @return array lista de funcoes
     */
    public function getFuncoes($iAnoUsu)
    {
        $oDaoOrcFuncao = new cl_orcfuncao;

        $sSqlFuncao = $oDaoOrcFuncao->sql_query_file(
            null,
            "o52_funcao as funcao,
                                                    o52_descr as descricao",
            "o52_funcao"
        );
        $rsFuncao = $oDaoOrcFuncao->sql_record($sSqlFuncao);

        return db_utils::getCollectionByRecord($rsFuncao, false, false, true);
    }

    /**
     * Retorna as subfuncoes
     *
     * @param integer $iAnoUsu ano corrente
     * @return array lista de subfuncoes
     */
    public function getSubFuncoes($iAnoUsu)
    {
        $oDaoOrcSubFuncao = new cl_orcsubfuncao;

        $sSqlSubFuncao = $oDaoOrcSubFuncao->sql_query_file(
            null,
            "o53_subfuncao as subfuncao,
                                                           o53_descr as descricao",
            "o53_subfuncao"
        );
        $rsSubFuncao = $oDaoOrcSubFuncao->sql_record($sSqlSubFuncao);

        return db_utils::getCollectionByRecord($rsSubFuncao, false, false, true);
    }

    /**
     * Retorna os programas
     *
     * @param integer $iAnousu ano acorrente
     * @return array lista de programas
     */
    public function getProgramas($iAnousu)
    {
        $oDaoOrcPrograma = new cl_orcprograma;

        $sSqlPrograma = $oDaoOrcPrograma->sql_query_file(
            $iAnousu,
            null,
            "o54_programa as programa, o54_descr as descricao",
            "o54_programa"
        );
        $rsPrograma = $oDaoOrcPrograma->sql_record($sSqlPrograma);

        return db_utils::getCollectionByRecord($rsPrograma, false, false, true);
    }

    /**
     * Retorna os projetos/atividades
     *
     * @param integer $iAnousu ano acorrente
     * @return array lista de projetos atividades
     */
    public function getProjAtiv($iAnousu)
    {
        $oDaoOrcProjAtiv = new cl_orcprojativ;

        $sSqlProjAtiv = $oDaoOrcProjAtiv->sql_query_file(
            $iAnousu,
            null,
            "o55_Projativ as projativ, o55_descr as descricao",
            "o55_projativ"
        );
        $rsProjAtiv = $oDaoOrcProjAtiv->sql_record($sSqlProjAtiv);

        return db_utils::getCollectionByRecord($rsProjAtiv, false, false, true);
    }

    public function getElementos($iAnoUsu)
    {
        $oDaoOrcElemento = new cl_orcelemento;

        $sWhere = "o58_anousu = {$iAnoUsu}";
        $sSqlElemento = $oDaoOrcElemento->sql_query_dotacao(
            null,
            "distinct o56_elemento as elemento, o56_descr as descricao",
            "o56_elemento",
            $sWhere
        );
        $rsElemento = $oDaoOrcElemento->sql_record($sSqlElemento);

        return db_utils::getCollectionByRecord($rsElemento, false, false, true);
    }

    /**
     * Retorna os recursos
     *
     * @return array lista de recursos
     */
    public function getRecursos($anoUsu, $apenasAtivos = true)
    {
        $oDaoOrcRecurso = new cl_orctiporec;

        $dataSessao = date('Y-m-d', db_getsession('DB_datausu'));
        $where = " exercicio = {$anoUsu} ";
        if ($apenasAtivos) {
            $where = " and (o15_datalimite is null or o15_datalimite > '{$dataSessao}')";
            $ordem = "o15_recurso, o15_complemento, gestao";
        } else {
            $ordem = "o15_datalimite desc, o15_recurso, o15_complemento, gestao";
        }

        $campos = "o15_codigo as recurso, ";
        $campos .= "o15_recurso as fonte, ";
        $campos .= "descricao, gestao, ";
        $campos .= "codigo_siconfi, ";
        $campos .= "o15_complemento as complemento, ";
        $campos .= "case ";
        $campos .= "  when (o15_datalimite is null or o15_datalimite > '{$dataSessao}') ";
        $campos .= "    then true ";
        $campos .= "  else false ";
        $campos .= "end as ativo ";
        
        $sSqlRecurso = $oDaoOrcRecurso->sqlNovaFonteRecurso(null, $campos, $ordem, $where);
        $rsRecurso = $oDaoOrcRecurso->sql_record($sSqlRecurso);
        return db_utils::getCollectionByRecord($rsRecurso, false, false, true);
    }
}
