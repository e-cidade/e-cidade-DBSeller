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

require_once(modification("model/pessoal/Rubrica.model.php"));

/**
 * Repositorio para Rubricas
 *
 * @abstract
 * @package Pessoal
 * @author Rafael Serpa Nery <rafael.nery@dbseller.com.br>
 * @author Jeferson Belmiro  <jeferson.belmiro@dbseller.com.br>
 */
abstract class RubricaRepository
{

    const MENSAGEM = "recursoshumanos.pessoal.RubricaRepository.";

    /**
     * Array com instancias de rubricas
     *
     * @static
     * @var Array
     * @access public
     */
    static $aInstanciasRubricas = array();

    /**
     * Adiciona uma rubrica ao array de rubricas
     *
     * @static
     * @param Rubrica $oRubrica
     * @access public
     * @return void
     */
    private static function adicionar(Rubrica $oRubrica, $iInstituicao)
    {

        RubricaRepository::$aInstanciasRubricas[$oRubrica->getCodigo()] [$iInstituicao] = $oRubrica;

    }

    /**
     * Retorna instancia da rubrica pelo codigo
     *
     * @static
     * @param string $sCodigo - codigo da rubrica
     * @access public
     * @return Rubrica
     * @throws BusinessException
     */
    public static function getInstanciaByCodigo($sCodigo, $iInstituicao = null)
    {

        if (empty($iInstituicao)) {
            $iInstituicao = db_getsession('DB_instit');
        }

        if (empty($sCodigo)) {
            throw new BusinessException("Número de Rubrica Inválida");
        }

        /**
         * Se não tiver rubrica no array de instancias, adiciona
         */
        if (empty(RubricaRepository::$aInstanciasRubricas[$sCodigo][$iInstituicao])) {
            RubricaRepository::adicionar(new Rubrica($sCodigo, $iInstituicao), $iInstituicao);
        }

        return RubricaRepository::$aInstanciasRubricas[$sCodigo][$iInstituicao];
    }

    /**
     * Retorna todas as rubricas da base
     *
     * @param Base $oBase
     * @return Rubricas[]
     * @throws BusinessException
     * @throws DBException
     */
    public static function getRubricasByBase(Base $oBase)
    {

        $oDaoBase = new cl_basesr();
        $sSqlRubricasBase = $oDaoBase->sql_rubricas_base($oBase->getCompetencia()->getAno(),
            $oBase->getCompetencia()->getMes(),
            $oBase->getCodigo(),
            null,
            $oBase->getInstituicao()->getCodigo(),
            "distinct r09_rubric");
        $rsRubricasBase = db_query($sSqlRubricasBase);

        if (!$rsRubricasBase) {
            throw new DBException(_M(self::MENSAGEM . "erro_pesquisar_rubricas_base"));
        }

        $aRubricasEncontradas = db_utils::getCollectionByRecord($rsRubricasBase);
        $aRubricas = array();

        foreach ($aRubricasEncontradas as $oRubrica) {
            $aRubricas[] = RubricaRepository::getInstanciaByCodigo($oRubrica->r09_rubric, $oBase->getInstituicao()->getCodigo());
        }

        return $aRubricas;
    }

    /**
     * Retorna todas as rubricas da base do servidor
     * OBS.: Rubricas da tabela "basesr" e "rhbasesreg"
     *
     * @param Base $oBase
     * @param Servidor $oServidor
     * @return Rubrica[]
     * @throws BusinessException
     * @throws DBException
     */
    public static function getRubricaByBaseServidor(Base $oBase, Servidor $oServidor)
    {

        $oDaoBaseServidor = new cl_rhbasesreg();
        $sSqlBaseServidor = $oDaoBaseServidor->sql_query_base_servidor($oBase, $oServidor);
        $rsBaseServidor = db_query($sSqlBaseServidor);

        if (!$rsBaseServidor) {
            throw new DBException(_M(self::MENSAGEM . "erro_pesquisar_rubricas_base_servidor"));
        }

        $aRubricasEncontradas = db_utils::getCollectionByRecord($rsBaseServidor);
        $aRubricas = array();

        foreach ($aRubricasEncontradas as $oRubrica) {
            $aRubricas[] = RubricaRepository::getInstanciaByCodigo($oRubrica->rh54_rubric, $oBase->getInstituicao()->getCodigo());
        }

        return $aRubricas;
    }

    /**
     * @param Servidor $servidor
     * @return Rubrica[]
     * @throws BusinessException
     */
    public static function buscaRubricasPontoServidor(Servidor $servidor)
    {
        $rubricas = array();

        /**
         * @var cl_rhrubricas $daoRubricas
         */
        $daoRubricas = new cl_rhrubricas;

        $sql = $daoRubricas->sql_query_rubricas_ponto_servidor(
            $servidor->getInstituicao()->getCodigo(),
            $servidor->getMatricula(),
            $servidor->getAnoCompetencia(),
            $servidor->getMesCompetencia()
        );

        $rs = $daoRubricas->sql_record($sql);

        if (!$rs && $daoRubricas->erro_sql == "Erro ao selecionar os registros.") {
            throw new Exception('Não foi possível buscar as rubricas do ponto do servidor.' . $daoRubricas->erro_msg);
        }

        while ($rubrica = pg_fetch_object($rs)) {
            $instanciaRubrica = clone RubricaRepository::getInstanciaByCodigo(
                $rubrica->rubrica,
                $servidor->getInstituicao()->getCodigo()
            );
            $instanciaRubrica->setValorAtualServidor($rubrica->valor);
            $instanciaRubrica->setQuantidadeAtualServidor($rubrica->quantidade);
            $instanciaRubrica->setTabelaServidor($rubrica->tabela);
            $rubricas[] = $instanciaRubrica;
        }

        return $rubricas;
    }

    /**
     * @param int $instituicao
     * @return rubrica[]
     * @throws Exception
     */
    public static function buscarRubricasAtivasPorInstituicao($instituicao = null)
    {
        $daoRubricas = new cl_rhrubricas();
        if (empty($instituicao)) {
            $instituicao = InstituicaoRepository::getInstituicaoSessao()->getCodigo();
        }

        $sql = $daoRubricas->sql_query_file('','','rh27_rubric','',"rh27_ativo = 't' and rh27_instit = {$instituicao}");
        if(!$sql) {
            throw new Exception('Não foi possível buscar as rubricas para processamento do eSocial.');
        }
        
        $rsRubricas   = $daoRubricas->sql_record($sql);
        $rubricas = array();
        for ($i = 0; $i < pg_num_rows($rsRubricas); $i++) {
            $rubricas[] = db_utils::fieldsMemory($rsRubricas, $i)->rh27_rubric;
        }

        return $rubricas;
    }
}
