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

use ECidade\RecursosHumanos\ESocial\Repository\Assentamento as AssentamentoESocial;
use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\ConcessaoCalculoLog;

/**
 * Class AssentamentoRepository
 *
 * @author Renan Melo <renan@dbseller.com.br>
 */
class AssentamentoRepository
{
    const MENSAGEM = 'recursoshumanos.pessoal.AssentamentoRepository.';

    /**
     * Representa o collection com os assentamentos;
     *
     * @var array Assentamentos
     */
    private $aAssentamentos = array();

    /**
     * Instancia da Classe
     *
     * @var Assentamento
     */
    private static $oInstance;

    /**
     * AssentamentoRepository constructor.
     */
    private function __construct()
    {
    }

    /**
     *
     */
    private function __clone()
    {
    }

    /**
     * Retorna a instância do Repository
     *
     * @return Assentamento|AssentamentoRepository
     */
    protected static function getInstance()
    {
        if (self::$oInstance === null) {
            self::$oInstance = new AssentamentoRepository();
        }

        return self::$oInstance;
    }

    /**
     * Monta o Objeto assentamento a partir do código informado por parâmetro.
     *
     * @param int $iCodigoAssentamento
     * @return Assentamento|AssentamentoRRA|AssentamentoSubstituicao
     * @throws Exception
     */
    public static function make($iCodigoAssentamento)
    {

        $oDaoAssenta = new cl_assenta();
        $sSqlAssenta = $oDaoAssenta->sql_query_file($iCodigoAssentamento);
        $rsAssenta = db_query($sSqlAssenta);

        if (!$rsAssenta) {
            throw new DBException(_M(self::MENSAGEM . 'erro_buscar_assentamento'));
        }

        if (pg_num_rows($rsAssenta) == 0) {
            throw new BusinessException(_M(self::MENSAGEM . 'nenhum_assentamento_encontrado'));
        }

        $oAssentamentoEncontrado = db_utils::FieldsMemory($rsAssenta, 0);

        $oAssentamento = AssentamentoFactory::getByCodigo($iCodigoAssentamento);

        $oAssentamento->setCodigo($oAssentamentoEncontrado->h16_codigo);
        $oAssentamento->setMatricula($oAssentamentoEncontrado->h16_regist);
        $oAssentamento->setTipoAssentamento($oAssentamentoEncontrado->h16_assent);
        $oAssentamento->setHistorico($oAssentamentoEncontrado->h16_histor);
        $oAssentamento->setCodigoPortaria($oAssentamentoEncontrado->h16_nrport);
        $oAssentamento->setDescricaoAto($oAssentamentoEncontrado->h16_atofic);
        $oAssentamento->setDias($oAssentamentoEncontrado->h16_quant);
        $oAssentamento->setPercentual($oAssentamentoEncontrado->h16_perc);
        $oAssentamento->setSegundoHistorico($oAssentamentoEncontrado->h16_hist2);
        $oAssentamento->setLoginUsuario($oAssentamentoEncontrado->h16_login);
        $oAssentamento->setDataLancamento($oAssentamentoEncontrado->h16_dtlanc);
        $oAssentamento->setConvertido($oAssentamentoEncontrado->h16_conver);
        $oAssentamento->setAnoPortaria($oAssentamentoEncontrado->h16_anoato);
        $oAssentamento->setHora($oAssentamentoEncontrado->h16_hora);

        if (!empty($oAssentamentoEncontrado->h16_dtconc)) {
            $oDataConcessao = new DBDate($oAssentamentoEncontrado->h16_dtconc);
            $oAssentamento->setDataConcessao($oDataConcessao);
        }

        if (!empty($oAssentamentoEncontrado->h16_dtterm)) {
            $oDataTermino = new DBDate($oAssentamentoEncontrado->h16_dtterm);
            $oAssentamento->setDataTermino($oDataTermino);
        }

        return $oAssentamento;
    }

    /**
     * Adiciona um objeto Assentamento ao collection de Assentamentos
     *
     * @param Assentamento $oAssentamento
     */
    public static function adicionar(Assentamento $oAssentamento)
    {
        self::getInstance()->aAssentamentos[$oAssentamento->getCodigo()] = $oAssentamento;
    }

    /**
     * Retorna a instância do Assentamento referente ao Código informado por parâmetro.
     *
     * @param int $iCodigo
     * @return Assentamento
     * @throws Exception
     */
    public static function getInstanceByCodigo($iCodigo)
    {
        if (!isset(self::getInstance()->aAssentamentos[$iCodigo])) {
            self::adicionar(self::make($iCodigo));
        }

        return self::getInstance()->aAssentamentos[$iCodigo];
    }

    /**
     * @param Assentamento $oAssentamento
     * @return mixed
     * @throws Exception
     */
    public static function persist(Assentamento $oAssentamento)
    {
        $mResponsePeristAssentamento = $oAssentamento->persist();

        if (!$mResponsePeristAssentamento instanceof Assentamento) {
            throw new BusinessException(_M(self::MENSAGEM . "erro_persistir_assentamento") . "\n\n" . $mResponsePeristAssentamento);
        }

        return $mResponsePeristAssentamento;
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getServidoresAssentamentoSubstituicao()
    {
        $aListaServidores = array();
        $iNaturezaAssentamentoSubstituicao = AssentamentoSubstituicao::CODIGO_NATUREZA;
        $oCompetencia = DBPessoal::getCompetenciaFolha();
        $oDaoAssentamento = new cl_assenta();

        $sCamposAssentamentoSubstituicao = " h16_regist as servidor ";

        $sWhereAssentamentoSubstituicao = "    rh159_sequencial = {$iNaturezaAssentamentoSubstituicao}
                                          and rh155_ano = {$oCompetencia->getAno()}
                                          and rh155_mes = {$oCompetencia->getMes()}
                                           or rh160_assentamento is null";

        $sqlAssentamentoSubstituicao = $oDaoAssentamento->sql_query_servidores_com_assentamento_substituicao(
            null,
            $sCamposAssentamentoSubstituicao,
            "h16_regist",
            $sWhereAssentamentoSubstituicao
        );

        $rsAssentamentoSubstituicao = db_query($sqlAssentamentoSubstituicao);

        if (!$rsAssentamentoSubstituicao) {
            throw new BusinessException(_M(self::MENSAGEM . "erro_buscar_servidores_assentamento_substituicao"));
        } else {
            if (pg_num_rows($rsAssentamentoSubstituicao) > 0) {
                $aAssentamentos = db_utils::getCollectionByRecord($rsAssentamentoSubstituicao);

                foreach ($aAssentamentos as $oStdAssentamento) {
                    $oServidor = ServidorRepository::getInstanciaByCodigo(
                        $oStdAssentamento->servidor,
                        DBPessoal::getAnoFolha(),
                        DBPessoal::getMesFolha()
                    );
                    $aListaServidores[] = $oServidor;
                }
            }
        }

        return $aListaServidores;
    }

    /**
     * @param int $iMatricula
     * @param DBCompetencia $oCompetencia
     * @return array
     * @throws Exception
     */
    public static function getAssentamentosSubstituicaoServidor($iMatricula, DBCompetencia $oCompetencia = null)
    {
        if (is_null($oCompetencia)) {
            $oCompetencia = DBPessoal::getCompetenciaFolha();
        }

        $oServidor = ServidorRepository::getInstanciaByCodigo(
            $iMatricula,
            $oCompetencia->getAno(),
            $oCompetencia->getMes()
        );

        $aAssentamentos = $oServidor->getAssentamentosSubstituicao();
        $assentamentosServidor = array();

        foreach ($aAssentamentos as $oAssentamento) {
            $oStdAssentamento = new stdClass();
            $oStdAssentamento->codigo = $oAssentamento->getCodigo();
            $oStdAssentamento->dataConcessao = ($oAssentamento->getDataConcessao() instanceof DBDate ? $oAssentamento->getDataConcessao()->getDate(DBDate::DATA_PTBR) : $oAssentamento->getDataConcessao());
            $oStdAssentamento->dataTermino = ($oAssentamento->getDataTermino() instanceof DBDate ? $oAssentamento->getDataTermino()->getDate(DBDate::DATA_PTBR) : $oAssentamento->getDataTermino());
            $oStdAssentamento->dias = $oAssentamento->getDias();
            $oStdAssentamento->valor_substituicao = $oAssentamento->getValorCalculado();
            $oStdAssentamento->hasLote = false;
            $oStdAssentamento->isLoteFolhaFechada = false;

            if ($oAssentamento->hasLote() === false) {
                $assentamentosServidor[] = $oStdAssentamento;
            } else {
                if (DBPessoal::getCompetenciaFolha()->comparar($oAssentamento->hasLote()->getCompetencia())) {
                    $oStdAssentamento->hasLote = true;
                    $oFolhaPagamento = $oAssentamento->hasLote()->getFolhaPagamento();

                    if ($oFolhaPagamento === false) {
                        throw new BusinessException(_M(self::MENSAGEM . "erro_buscar_folha_pagamento_lote"));
                    } else {
                        if (!$oFolhaPagamento->isAberto()) {
                            $oStdAssentamento->isLoteFolhaFechada = true;
                        }
                    }

                    $assentamentosServidor[] = $oStdAssentamento;
                }
            }
        }

        return $assentamentosServidor;
    }

    /**
     * @param Assentamento $oAssentamento
     */
    public static function persistLancamento(Assentamento $oAssentamento)
    {
    }

    /**
     * Retorna todos os assentamentos do servidor
     *
     * @param Servidor $oServidor
     * @param int $iTipoAssentamento
     * @param DBDate $oDataMinima
     * @param string $sTipo
     * @param bool $lAssentamentoFuncional
     * @return array
     * @throws Exception
     */
    public static function getAssentamentosPorServidor(
        Servidor $oServidor,
        $iTipoAssentamento = null,
        DBDate $oDataMinima = null,
        $sTipo = null,
        $lAssentamentoFuncional = null
    ) {

        $sWhere = "h16_regist = {$oServidor->getMatricula()}";

        if ($lAssentamentoFuncional !== null) {
            $sWhereAssentamentoFuncional = " and rh193_assentamento_funcional is not null";
            if ($lAssentamentoFuncional === false) {
                $sWhereAssentamentoFuncional = " and rh193_assentamento_funcional is null";
            }

            $sWhere .= $sWhereAssentamentoFuncional;
        }

        if (!empty($iTipoAssentamento)) {
            if (is_array($iTipoAssentamento)) {
                $iTipoAssentamento = implode(",", $iTipoAssentamento);
            }
            $sWhere .= " and h16_assent in({$iTipoAssentamento})";
        }
        if ($oDataMinima) {
            $sWhere .= " and h16_dtconc >= '{$oDataMinima->getDate()}' ";
        }

        if ($sTipo) {
            $sWhere .= " and h12_tipo = '{$sTipo}'";
        }
        $oDaoAssentamento = new cl_assenta();
        $sSqlBusca = $oDaoAssentamento->sql_query_funcional(
            null,
            "h16_codigo",
            null,
            $sWhere
        );

        $rsAssentamentos = db_query($sSqlBusca);

        if (!$rsAssentamentos) {
            throw new DBException(_M(self::MENSAGEM . "erro_buscar_assentamentos_servidor"));
        }

        $assentamentos = array();

        foreach (db_utils::getCollectionByRecord($rsAssentamentos) as $oDados) {
            $assentamentos[] = AssentamentoFactory::getByCodigo($oDados->h16_codigo);
        }

        return $assentamentos;
    }

    /**
     * Exclui um assentamento
     *
     * @param Assentamento $oAssentamento
     * @throws Exception
     */
    public static function excluir(Assentamento $oAssentamento)
    {
        $oDaoAssentamento = new cl_assenta();
        $oDaoAssentamento->excluir($oAssentamento->getCodigo());

        if ($oDaoAssentamento->erro_status == 0) {
            throw new BusinessException("Erro ao excluir o assentamento.\nErro:{$oDaoAssentamento->erro_sql}");
        }
    }

    /**
     * Retorna todos os assentamentos do tipo afastamento do servidor
     *
     * @param Servidor $oServidor
     * @param int $iTipoAssentamento
     * @param DBDate $oDataMinima
     * @return array
     * @throws Exception
     */
    public static function getAssentamentosDeAfastamentoPorServidor(
        Servidor $oServidor,
        $iTipoAssentamento = null,
        DBDate $oDataMinima = null
    ) {

        $sWhere = "h16_regist = {$oServidor->getMatricula()}";

        if (!empty($iTipoAssentamento)) {
            if (is_array($iTipoAssentamento)) {
                $iTipoAssentamento = implode(",", $iTipoAssentamento);
            }
            $sWhere .= " and h16_assent in({$iTipoAssentamento})";
        }
        if ($oDataMinima) {
            $sWhere .= " and h16_dtconc >= '{$oDataMinima->getDate()}' ";
        }
        $sWhere .= " and h12_tipo = 'A'";
        $oDaoAssentamento = new cl_assenta();
        $sSqlBusca = $oDaoAssentamento->sql_query(
            null,
            "h16_codigo",
            null,
            $sWhere
        );

        $rsAssentamentos = db_query($sSqlBusca);

        if (!$rsAssentamentos) {
            throw new DBException(_M(self::MENSAGEM . "erro_buscar_assentamentos_servidor"));
        }

        $assentamentos = array();

        foreach (db_utils::getCollectionByRecord($rsAssentamentos) as $oDados) {
            $assentamentos[] = AssentamentoFactory::getByCodigo($oDados->h16_codigo);
        }

        return $assentamentos;
    }

    /**
     * Retorna assentamento de justificativa em determinado período
     *
     * @param int $codigoTipoAssentamento
     * @param int $matricula
     * @param DBDate $dataConcessao
     * @param DBDate $dataTermino
     * @return mixed|null
     * @throws Exception
     */
    public static function getAssentamentoJustificativaPorTipoServidorPeriodo(
        $codigoTipoAssentamento,
        $matricula,
        DBDate $dataConcessao,
        DBDate $dataTermino = null,
        $codigoAssentamento = null
    ) {

        $aWhere = array(
            "h12_natureza = " . Assentamento::NATUREZA_JUSTIFICATIVA,
            "h16_regist  = {$matricula}",
            "h16_assent  = {$codigoTipoAssentamento}"
        );

        if (!empty($codigoAssentamento)) {
            $aWhere[] = "h16_codigo != {$codigoAssentamento}";
        }

        $sWhereDatas = "(";
        $sWhereDatas .= "(h16_dtconc <= '{$dataConcessao->getDate()}'";
        $sWhereDatas .= " AND (h16_dtterm >= '{$dataConcessao->getDate()}' OR h16_dtterm is null))";

        if (empty($dataTermino)) {
            $sWhereDatas .= " OR  (h16_dtconc >= '{$dataConcessao->getDate()}')";
        }

        if (!empty($dataTermino)) {
            $sWhereDatas .= " OR  (h16_dtconc >= '{$dataConcessao->getDate()}' AND h16_dtterm is null)";
            $sWhereDatas .= " OR  (h16_dtconc >= '{$dataConcessao->getDate()}' AND h16_dtconc <= '{$dataTermino->getDate()}')";
        }
        $sWhereDatas .= " )";

        $aWhere[] = $sWhereDatas;

        $sWhere = implode(' AND ', $aWhere);

        $oDaoAssentamento = new cl_assenta();
        $sSqlBusca = $oDaoAssentamento->sql_query(null, "h16_codigo", null, $sWhere);
        $rsAssentamentos = db_query($sSqlBusca);

        if (!$rsAssentamentos) {
            throw new DBException(_M(self::MENSAGEM . "erro_buscar_assentamentos_servidor"));
        }

        if (pg_num_rows($rsAssentamentos) > 0) {
            return db_utils::makeFromRecord($rsAssentamentos, function ($retorno) {
                return AssentamentoFactory::getByCodigo($retorno->h16_codigo);
            });
        }

        return null;
    }

    /**
     * Retorna os assentamentos de um servidor por um tipo, natureza e data
     *
     * @param Servidor $servidor
     * @param $sequencialTipoAssentamento
     * @param DBDate $data
     * @param array|int $natureza
     * @param bool $lFuncional
     * @param string $tipoAssentamento
     * @return array|null
     * @throws Exception
     */
    public static function getAssentamentosServidorPorTipoENaturezaEPorSequencial(
        Servidor $servidor,
        $sequencialTipoAssentamento,
        DBDate $data,
        $natureza = null,
        $lFuncional = false,
        $tipoAssentamento = null
    ) {
        if (!($data instanceof DBDate)) {
            throw new ParameterException("Informe uma data válida para verificar se o servidor está afastado.");
        }

        $daoAssenta = new cl_assenta;

        $aWhereAssenta = array("h16_regist = {$servidor->getMatricula()}");
        if (!empty($tipoAssentamento)) {
            $aWhereAssenta[] = "h12_tipo = '{$tipoAssentamento}'";
        }

        $aWhereAssenta[] = " h12_codigo = {$sequencialTipoAssentamento} ";

        $aWhereAssenta[] = "(    (h16_dtterm is null AND h16_dtconc <= '{$data->getDate()}')
                          OR (h16_dtterm >= '{$data->getDate()}' AND h16_dtconc <= '{$data->getDate()}')
                        )";

        if ($lFuncional !== null) {
            if ($lFuncional) {
                $aWhereAssenta [] = " rh193_assentamento_funcional is not null ";
            }

            if ($lFuncional === false) {
                $aWhereAssenta [] = " rh193_assentamento_funcional is null ";
            }
        }

        if (!empty($natureza)) {
            if (is_array($natureza)) {
                $natureza = implode(', ', $natureza);
            }

            $aWhereAssenta[] = "h12_natureza IN ({$natureza})";
        }

        $whereAssenta = implode(' and ', $aWhereAssenta);
        $rsAssenta = db_query($sqlAssenta = $daoAssenta->sql_query_join_somente_assentamentofuncional_tipoasse(
            null,
            "*",
            "h16_dtconc, h16_codigo",
            $whereAssenta
        ));

        if (!$rsAssenta) {
            throw new DBException("Ocorreu um erro ao consultar os assentamentos de afastamento no módulo RH.\nContate o suporte.\n\n" . pg_last_error());
        }

        if (pg_num_rows($rsAssenta) > 0) {
            $assentamentoRepository = self::getInstance();

            return db_utils::makeCollectionFromRecord($rsAssenta, function ($retorno) {
                return AssentamentoFactory::getByCodigo($retorno->h16_codigo);
            });
        }

        return null;
    }

    /**
     * Retorna os assentamentos de um servidor por um tipo, natureza e data
     *
     * @param Servidor $servidor
     * @param string $tipoAssentamento
     * @param DBDate $data
     * @param array|int $natureza
     * @param bool $lFuncional
     * @return array|null
     * @throws Exception
     */
    public static function getAssentamentosServidorPorTipoENatureza(
        Servidor $servidor,
        $tipoAssentamento = 'S',
        DBDate $data,
        $natureza = null,
        $lFuncional = false,
        $codigoAssentamento = null
    ) {
        if (!($data instanceof DBDate)) {
            throw new ParameterException("Informe uma data válida para verificar se o servidor está afastado.");
        }

        $daoAssenta = new cl_assenta;

        $aWhereAssenta = array("h16_regist = {$servidor->getMatricula()}");
        $aWhereAssenta[] = "h12_tipo = '{$tipoAssentamento}'";
        $aWhereAssenta[] = "(    (h16_dtterm is null AND h16_dtconc <= '{$data->getDate()}')
                          OR (h16_dtterm >= '{$data->getDate()}' AND h16_dtconc <= '{$data->getDate()}')
                        )";
        if (!empty($codigoAssentamento)) {
            $aWhereAssenta[] = "h16_codigo != {$codigoAssentamento}";
        }

        if (!is_null($lFuncional)) {
            $sNot = '';
            if ($lFuncional) {
                $sNot = 'not';
            }

            $aWhereAssenta [] = " rh193_assentamento_funcional is {$sNot} null ";
        }

        if (!empty($natureza)) {
            if (is_array($natureza)) {
                $natureza = implode(', ', $natureza);
            }

            $aWhereAssenta[] = "h12_natureza IN ({$natureza})";
        }

        $whereAssenta = implode(' and ', $aWhereAssenta);
        $sqlAssenta = $daoAssenta->sql_query_join_somente_assentamentofuncional_tipoasse(
            null,
            "h16_codigo",
            "h16_dtconc, h16_codigo",
            $whereAssenta
        );

        $rsAssenta = db_query($sqlAssenta);

        if (!$rsAssenta) {
            $sMsg = "Ocorreu um erro ao consultar os assentamentos de afastamento no módulo RH.\n";
            $sMsg .= "Contate o suporte.\n\n";
            throw new DBException($sMsg . $daoAssenta->erro_msg);
        }

        if (pg_num_rows($rsAssenta) > 0) {
            $assentamentoRepository = self::getInstance();

            return db_utils::makeCollectionFromRecord($rsAssenta, function ($retorno) {
                return AssentamentoFactory::getByCodigo($retorno->h16_codigo);
            });
        }

        return null;
    }

    /**
     * @param Servidor $oServidor
     * @param array $aTipoAssentamento
     * @param DBDate $oData
     * @return Assentamento[]
     * @throws Exception
     */
    public static function getAssentamentosServidorDia(
        Servidor $oServidor,
        $aTipoAssentamento = array(),
        DBDate $oData,
        $lFuncional = null
    ) {
        $sWhere = "     h16_regist = {$oServidor->getMatricula()}";
        $sWhere .= " AND '{$oData->getDate()}' >= h16_dtconc";
        $sWhere .= " AND (h16_dtterm is null OR (h16_dtterm is not null AND '{$oData->getDate()}' <= h16_dtterm))";

        if (count($aTipoAssentamento) > 0) {
            $sWhere .= " AND h12_assent in(" . implode(', ', $aTipoAssentamento) . ")";
        }

        if (!is_null($lFuncional)) {
            $sNot = '';
            if ($lFuncional) {
                $sNot = 'not';
            }

            $sWhere .= " and rh193_assentamento_funcional is {$sNot} null ";
        }

        $oDaoAssenta = new cl_assenta();
        $sSqlAssenta = $oDaoAssenta->sql_query_tipo(null, 'h16_codigo', null, $sWhere);
        $rsAssenta = $oDaoAssenta->sql_record($sSqlAssenta);

        if (!$rsAssenta) {
            throw new DBException('Erro ao buscar os assentamentos lançados para o servidor.');
        }

        return db_utils::makeCollectionFromRecord($rsAssenta, function ($oRetorno) {
            return AssentamentoFactory::getByCodigo($oRetorno->h16_codigo);
        });
    }

    /**
     * Lógica desacoplada do fonte do frontend
     *
     * @param Assentamento $assentamento
     * @param bool $verificaEsocial
     * @return bool
     * @throws Exception
     */
    public static function excluiAssentamentoEfetividade(Assentamento $assentamento, $verificaEsocial = false)
    {
        $daoAssentamento = new cl_assenta;
        $daoAssentamentoFuncional = new cl_assentamentofuncional;
        $daoAssentamentoJustificativa = new cl_assentamentojustificativaperiodo;
        $daoAssentamentoHoraExtraManual = new cl_assentamentohoraextra;

        /**
         * Verifica se o assentamento já possuí protocolo no eSocial
         */
        if ($verificaEsocial) {
            $assentamentoEsocial = ECidade\RecursosHumanos\RH\Assentamento\Repository\Assentamento::getInstance();
            if ($assentamentoEsocial->possuiConfiguracaoApi()) {
                if ($assentamentoEsocial->possuiProtocoloByAfastamento($assentamento->getCodigo())) {
                    throw new Exception('Não é possível excluir o assentamento pois o mesmo possui protocolo no eSocial.');
                }
            }
        }
        /**
         * Verificamos se o assentamento é de rra, e se já foi realizado o lançamento do mesmo
         * no ponto.
         */
        /**
         * Valida datas para assentamento de substituição
         */

        $tipoAssentamento = $assentamento->getInstanciaTipoAssentamento();
        if ($tipoAssentamento->getNatureza() == AssentamentoRRA::CODIGO_NATUREZA) {
            /**
             * Verificamos se o assentamento já não esta vinculado com um lote de registros de ponto
             * se estiver, não permite a exclusão.
             */
            $daoAssentaLoteRegistroPonto = new cl_assentaloteregistroponto();
            $sqlAssentaLoteRegistroPonto = $daoAssentaLoteRegistroPonto->sql_query_file(
                null,
                "rh160_sequencial",
                null,
                "rh160_assentamento = {$assentamento->getCodigo()}"
            );

            $rsAssentaLoteRegistroPonto = db_query($sqlAssentaLoteRegistroPonto);

            if (pg_num_rows($rsAssentaLoteRegistroPonto) > 0) {
                throw new BusinessException("Assentamento já possuí evento financeiro, exclusão não permitida.");
            }
        }

        $periodoAquisitivoAssentamento = PeriodoAquisitivoAssentamento::getPeriodoAquisitivoAssentamento($assentamento);

        if ($periodoAquisitivoAssentamento) {
            $periodoAquisitivoAssentamento->excluir();
        }

        /**
         * Tratamento para exclusão de assentamentos de substituição
         */
        $daoAssentamentoSubstituicao = new cl_assentamentosubstituicao();
        $rsAssentamentoSubstituicao = $daoAssentamentoSubstituicao->sql_record($daoAssentamentoSubstituicao->sql_query_file($assentamento->getCodigo()));

        if ($rsAssentamentoSubstituicao && $daoAssentamentoSubstituicao->numrows > 0) {
            $daoAssentamentoSubstituicao->excluir($assentamento->getCodigo());
        }

        /**
         * Tratamento para exclusão de assentamentos com atributos dinamicos
         */
        // Exclui os valores dos atributos dinâmicos vinculados ao assentamentoill("aqui5");
        $whereAtributoDinamicoValor = " db110_cadattdinamicovalorgrupo in (select h80_db_cadattdinamicovalorgrupo";
        $whereAtributoDinamicoValor .= "                                      from assentadb_cadattdinamicovalorgrupo";
        $whereAtributoDinamicoValor .= "                                     where  h80_assenta = {$assentamento->getCodigo()})";
        $daoAtributoDinamicoValor = new cl_db_cadattdinamicoatributosvalor();
        $daoAtributoDinamicoValor->excluir(null, $whereAtributoDinamicoValor);

        if ($daoAtributoDinamicoValor->erro_status == '0') {
            throw new DBException("Ocorreu um erro ao excluir os valores dos atributos dinâmicos vinculados ao assentamento.");
        }

        // Exclui vínculo entre o valor dos atributos dinâmicos com o assentamento
        $daoAssentaAtributos = new cl_assentadb_cadattdinamicovalorgrupo();
        $daoAssentaAtributos->excluir(null, null, "h80_assenta = {$assentamento->getCodigo()}");

        if ($daoAssentaAtributos->erro_status == '0') {
            throw new DBException("Ocorreu um erro ao excluir o vínculo entre os valores dos atributos dinâmicos e o assentamento.");
        }

        /**
         * Verificamos a configuração se há tipo de assentamentos do RH que geram afastamentos do pessoal
         * se houver excluímos o afastamento vinculado
         */
        $listaInformacoesExternas = InformacoesExternasTipoAssentamento::getTipoAssentamentoConfiguradosPorCompetencia(DBPessoal::getCompetenciaFolha());

        if (is_array($listaInformacoesExternas)) {
            $tiposAssentamentoConfigurados = array();
            foreach ($listaInformacoesExternas as $informacoesExternas) {
                $tiposAssentamentoConfigurados[] = $informacoesExternas->getTipoAssentamento()->getSequencial();
            }

            if (in_array($assentamento->getTipoAssentamento(), $tiposAssentamentoConfigurados)) {
                $afastamentosAssentamentos = AfastaAssentaRepository::getAfastamentosPorAssentamento($assentamento);

                if (!is_array($afastamentosAssentamentos)) {
                    throw new BusinessException("Não foi possível buscar o vínculo entre assentamento e afastamento.");
                }

                foreach ($afastamentosAssentamentos as $afastamento) {
                    $afastamentoAssentamento = new AfastaAssenta($assentamento, $afastamento);

                    /**
                     * Excluímos o vínculo entre assentamentos e afastamentos
                     */
                    if (!$afastamentoAssentamento->remove()) {
                        throw new BusinessException("Erro ao excluir o vínculo entre o assentamento e afastamento.");
                    }
                    /**
                     * Excluímos o afastamento que foi originado a partir do assentamento
                     */
                    if (!AfastamentoRepository::remove($afastamento)) {
                        throw new BusinessException("Erro ao excluir o afastamento.");
                    }
                }
            }
        }
        /**
         * Tratamento para exclusão de assentamentos de RRA
         */
        $daoAssentamentoRRA = new cl_assentamentorra();
        $rsAssentamentoRRA = $daoAssentamentoRRA->sql_record($daoAssentamentoRRA->sql_query_file(
            null,
            "*",
            null,
            " h83_assenta=" . $assentamento->getCodigo()
        ));

        if ($rsAssentamentoRRA && $daoAssentamentoRRA->numrows > 0) {
            RRARepository::excluirLancamentosPorCodigoAssentamento($assentamento->getCodigo());
            $daoAssentamentoRRA->excluir(null, " h83_assenta=" . $assentamento->getCodigo());
        }
        /**
         * Tratamento para exclusão de assentamentos de justificativa
         */
        $rsAssentamentoJustificativa = $daoAssentamentoJustificativa->sql_record($daoAssentamentoJustificativa->sql_query_file($assentamento->getCodigo()));
        if ($rsAssentamentoJustificativa && $daoAssentamentoJustificativa->numrows > 0) {
            $daoAssentamentoJustificativa->excluir(null, null, "rh206_codigo = {$assentamento->getCodigo()}");
            if ($daoAssentamentoJustificativa->erro_status == '0') {
                throw new DBException($daoAssentamentoJustificativa->erro_msg);
            }
        }

        /**
         * Verificamos se o assentamento foi gerado a partir de um processamento de férias, caso seja e as
         * férias já foram pagas, o assentamento não pode ser excluido.
         */
        $daoRhFeriasPeriodosAssentamento = new cl_rhferiasperiodoassentamento();
        $whereRhFeriasPeriodosAssentamento = "rh169_assenta = {$assentamento->getCodigo()} and rh110_situacao <> 0";
        $sqlRhFeriasPeriodosAssentamento = $daoRhFeriasPeriodosAssentamento->sql_query(
            null,
            "*",
            null,
            $whereRhFeriasPeriodosAssentamento
        );
        $rsRhFeriasPeriodosAssentamento = db_query($sqlRhFeriasPeriodosAssentamento);

        if (!$rsRhFeriasPeriodosAssentamento) {
            throw new DBException("Ocorreu um erro ao verificar os periodos de assentamentos.");
        }
        if (pg_num_rows($rsRhFeriasPeriodosAssentamento) > 0) {
            throw new BusinessException("Assentamento vinculado a férias já processadas no pessoal.");
        }
        /**
         * Alteramos as datas em que o ponto é utilizado.
         */
        $daoPontoEletronicoData = new cl_pontoeletronicoarquivodata();
        $SqlDadosPonto = $daoPontoEletronicoData->sql_query_file(
            null,
            "*",
            null,
            "rh197_afastamento = {$assentamento->getCodigo()}"
        );
        $rsDadosPonto = db_query($SqlDadosPonto);
        if (!$rsDadosPonto) {
            throw new BusinessException("Erro ao pesquiser batidas do ponto eletrônico.");
        }
        db_utils::makeCollectionFromRecord($rsDadosPonto, function ($dados) use ($daoPontoEletronicoData) {
            $daoPontoEletronicoData->rh197_sequencial = $dados->rh197_sequencial;
            $daoPontoEletronicoData->rh197_pontoeletronicoarquivo = $dados->rh197_pontoeletronicoarquivo;
            $daoPontoEletronicoData->rh197_data = $dados->rh197_data;
            $daoPontoEletronicoData->rh197_matricula = $dados->rh197_matricula;
            $daoPontoEletronicoData->rh197_horas_falta = $dados->rh197_horas_falta;
            $daoPontoEletronicoData->rh197_horas_trabalhadas = $dados->rh197_horas_trabalhadas;
            $daoPontoEletronicoData->rh197_horas_extras_50_d = $dados->rh197_horas_extras_50_d;
            $daoPontoEletronicoData->rh197_horas_extras_75_d = $dados->rh197_horas_extras_75_d;
            $daoPontoEletronicoData->rh197_horas_extras_100_d = $dados->rh197_horas_extras_100_d;
            $daoPontoEletronicoData->rh197_horas_adicinal_noturno = $dados->rh197_horas_adicinal_noturno;
            $daoPontoEletronicoData->rh197_pis = $dados->rh197_pis;
            $daoPontoEletronicoData->rh197_horas_extras_50_n = $dados->rh197_horas_extras_50_n;
            $daoPontoEletronicoData->rh197_horas_extras_75_n = $dados->rh197_horas_extras_75_n;
            $daoPontoEletronicoData->rh197_horas_extras_100_n = $dados->rh197_horas_extras_100_n;
            $daoPontoEletronicoData->rh197_afastamento = 'null';
            $daoPontoEletronicoData->alterar($dados->rh197_sequencial);
            if ($daoPontoEletronicoData->erro_status == 0) {
                throw new BusinessException("Erro ao salvar dados do dia de trabalho.");
            }
        });
        $daoRhFeriasPeriodosAssentamento->excluir(null, "rh169_assenta = {$assentamento->getCodigo()}");
        if ($tipoAssentamento->getNatureza() == AssentamentoRRA::NATUREZA_HE_MANUAL) {
            if (!$daoAssentamentoHoraExtraManual->excluir(null, "h17_assenta = {$assentamento->getCodigo()}")) {
                throw new DBException($daoAssentamentoHoraExtraManual->erro_msg);
            }
        }
        if ($tipoAssentamento->getNatureza() == Assentamento::NATUREZA_ABONO_FALTA) {
            $daoAssentamentoAbonoFalta = new cl_assentamentoabonofalta();
            if (!$daoAssentamentoAbonoFalta->excluir(null, "rh213_codigo = {$assentamento->getCodigo()}")) {
                throw new DBException($daoAssentamentoAbonoFalta->erro_msg);
            }
        }

        $daoConfiguracoesDatasEfetividade = new cl_configuracoesdatasefetividade();
        $whereConfiguracoesDatasEfetividade = "rh186_processado is true AND rh186_instituicao = " . db_getsession("DB_instit");

        $assentamentoDataTermino = $assentamento->getDataTermino() instanceof DBDate
            ? $assentamento->getDataTermino()->getDate()
            : null;

        $assentamentoDataConcessao = $assentamento->getDataConcessao()->getDate();

        if (empty($assentamentoDataTermino)) {
            $whereConfiguracoesDatasEfetividade .= " AND '{$assentamentoDataConcessao}' BETWEEN rh186_datainicioefetividade AND rh186_datafechamentoefetividade";
        } else {
            $whereConfiguracoesDatasEfetividade .= " AND (('{$assentamentoDataConcessao}' BETWEEN rh186_datainicioefetividade AND rh186_datafechamentoefetividade) OR ('{$assentamentoDataTermino}' BETWEEN rh186_datainicioefetividade AND rh186_datafechamentoefetividade))";
        }

        AssentamentoESocial::excluirFormulario($assentamento);

        $sqlConfiguracoesDatasEfetividade = $daoConfiguracoesDatasEfetividade->sql_query_file(
            null,
            "*",
            null,
            $whereConfiguracoesDatasEfetividade
        );
        $rsConfiguracoesDatasEfetividade = db_query($sqlConfiguracoesDatasEfetividade);

        if (pg_num_rows($rsConfiguracoesDatasEfetividade) > 0) {
            $dadosEfetividade = db_utils::fieldsMemory($rsConfiguracoesDatasEfetividade, 0);
            $periodo = trim(db_formatar(
                $dadosEfetividade->rh186_datainicioefetividade,
                'd'
            )) . " a " . trim(db_formatar($dadosEfetividade->rh186_datafechamentoefetividade, 'd'));
            $mensagem = "O período de efetividade {$periodo} já foi processado.\\nPara realizar manutenções em assentamentos nesse período, ";
            $mensagem .= "reabra o período de efetividade em Procedimentos > Efetividade > Reabrir Período.";
            $daoAssentamento->erro_msg = $mensagem;
            $daoAssentamento->erro_status = '1';

            throw new BusinessException($mensagem);
        }

        /**
         * Exclui da tabela concessaocalculolog
         */
        ConcessaoCalculoLog::where(
            'rh507_assent',
            $assentamento->getCodigo()
        )->delete();

        /**
         * Exclui da tabela assenta.
         */
        $daoAssentamentoFuncional->excluir($assentamento->getCodigo());
        if ($daoAssentamentoFuncional->erro_status == '0') {
            throw new DBException($daoAssentamentoFuncional->erro_msg);
        }
        /**
         * Exclui da tabela de vinculo com o lote
         */
        $daoLoteAssentamento = new cl_loteassentamento();
        $daoLoteAssentamento->excluir(null, "h24_assenta = {$assentamento->getCodigo()}");
        if ($daoLoteAssentamento->erro_status == '0') {
            throw new DBException('Não foi possível remover o assentamento do lote.');
        }

        $daoAssentamento->excluir($assentamento->getCodigo());
        if ($daoAssentamento->erro_status == '0') {
            throw new DBException($daoAssentamento->erro_msg);
        }

        $assentamento->invalidarCachePontoEletronico();
        return true;
    }

    public static function servidorValidaData($servidor, $dataObrigatoriedade)
    {
        $assenta = new \cl_assenta;
        $sqlAssenta = $assenta->sql_dados_afastamento_esocial(
            $servidor->getMatricula(),
        'a.h16_dtconc, db110_valor',
        "(a.h16_dtterm is null or a.h16_dtterm >= '".$dataObrigatoriedade->getDate()."')" .
        " and a.h16_dtconc <= '".$dataObrigatoriedade->getDate()."'",
            'a.h16_dtconc'
        );
        $resultado = $assenta->sql_record($sqlAssenta);
        if($resultado) {
            return \db_utils::fieldsMemory($resultado, 0);
        }
        return false;
    }
}
