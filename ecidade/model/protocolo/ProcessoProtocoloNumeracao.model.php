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
 * Classe singleton para controle de numeração dos processos de procotolo
 * @package protocolo
 * @static
 */
class ProcessoProtocoloNumeracao
{

    const TIPOSEQUENCIAL = 1;
    const TIPOANO = 2;
    const TIPOORGAO = 3;
    const TIPOANO_COM_SIGLA = 4;
    const MAXNUMEROORGAO = 999999999999999999;

    private static $oInstance = null;

    private $iTipoControle = null;
    private $proximoNumero = null;
    private $orgao;
    private $tipoDocumentoProcesso;

    /**
     * classe construtora marcada como private
     */
    private function __construct()
    {
        /**
         * pesquisa a forma de numeracao do modulo protocolo.
         * os tipos disponiveis são 1 - Forma global - a a Numeracao é sequencial para todas as instituicoes
         *                          2 - Numeracao Anual Cada instituição tem seus proprios números de protocolo.
         *                              a numeracao é reiniciada a cada ano.
         */
        $oDaoParametrosGlobais = db_utils::getDao("protparamglobal");
        $where = ' p06_instituicao = ' . db_getsession('DB_instit');
        $sSqlDadosParametros = $oDaoParametrosGlobais->sql_query_file(null, '*', null, $where);
        $rsDadosParametros = $oDaoParametrosGlobais->sql_record($sSqlDadosParametros);

        if ($oDaoParametrosGlobais->numrows == 0) {
            throw new Exception('Parametros Globais do módulo protocolo não configurados.');
        }
        $this->iTipoControle = (int)(db_utils::fieldsMemory($rsDadosParametros, 0)->p06_tipo);
    }

    /**
     * marcamos a função clone como private, para não podermos ter um anova instancia atraves de clone
     *
     */
    private function __clone()
    {
    }

    /**
     * @var int
     */
    public function setOrgao($orgao)
    {
        $this->orgao = $orgao;
    }

    /**
     * @var int
     */
    public function setTipoDocumentoProcesso($tipoDocumentoProcesso)
    {
        $this->tipoDocumentoProcesso = $tipoDocumentoProcesso;
    }

    /**
     * retorna o proximo número de protocolo
     * @return integer
     * @throws Exception
     */
    public static function getProximoNumero(
        $orgao = 0,
        $tipoDocumentoProcesso = 0,
        $siglaDoDocumentoNaNumeracao = false
    ) {
        if (!db_utils::inTransaction()) {
            throw new Exception('Para utilização desse método é necessário uma transação com o Banco de Dados.');
        }

        $oInstancia = self::getInstance();
        $oInstancia->setOrgao($orgao);
        $oInstancia->setTipoDocumentoProcesso($tipoDocumentoProcesso);
        $oInstancia->bloqueiaControleNumeracao();
        $oInstancia->proximoNumero = $oInstancia->getNumeroUtilizar();

        if ($oInstancia->proximoNumero > self::MAXNUMEROORGAO) {
            throw new Exception(
                'Na configuração por Órgão, o próximo número não pode ser superior a ' . self::MAXNUMEROORGAO . '.'
            );
        }

        $oInstancia->updateNumeracao();

        return self::formataNumeracaoOrgao(
            $oInstancia->proximoNumero,
            $orgao,
            $tipoDocumentoProcesso,
            $siglaDoDocumentoNaNumeracao
        );
    }

    /**
     * Retorna o número que deve ser Utilizado
     *
     * @throws Exception
     */
    private function getNumeroUtilizar()
    {
        $oInstancia = self::getInstance();
        $sWhere = $oInstancia->getRegraNumeracao();
        $oDaoProtocoloNumeracao = db_utils::getDao("protprocessonumeracao");
        $sSqlNumeracao = $oDaoProtocoloNumeracao->sql_query_file(
            null,
            "max(p07_proximonumero) as numeroutilizar",
            null,
            $sWhere
        );
        $rsNumeracao = $oDaoProtocoloNumeracao->sql_record($sSqlNumeracao);
        return db_utils::fieldsMemory($rsNumeracao, 0)->numeroutilizar;
    }

    /**
     * retorna a instancia da classe
     * @return ProcessoProtocoloNumeracao
     */
    private static function getInstance()
    {
        if (self::$oInstance == null) {
            self::$oInstance = new ProcessoProtocoloNumeracao();
        }
        return self::$oInstance;
    }

    /**
     * Atualiza os dados da numeração, conforme regra configurada
     */
    private function updateNumeracao()
    {
        $oInstancia = self::getInstance();
        $sWhere = $oInstancia->getRegraNumeracao();
        $oDaoProtocoloNumeracao = db_utils::getDao("protprocessonumeracao");

        /**
         * Atualizamos a numeração  conforme regra
         */
        $sSqlNumeracao = $oDaoProtocoloNumeracao->sql_query_file(
            null,
            "p07_sequencial",
            null,
            $sWhere
        );
        $rsNumeracao = $oDaoProtocoloNumeracao->sql_record($sSqlNumeracao);
        $aNumeracoes = db_utils::getCollectionByRecord($rsNumeracao);
        foreach ($aNumeracoes as $oNumeracao) {
            $oDaoProtocoloNumeracao->p07_sequencial = $oNumeracao->p07_sequencial;
            $oDaoProtocoloNumeracao->p07_proximonumero = self::getInstance()->proximoNumero + 1;
            $oDaoProtocoloNumeracao->alterar($oNumeracao->p07_sequencial);
            if ($oDaoProtocoloNumeracao->erro_status == 0) {
                throw new Exception('Erro ao atualizar numeração do Protocolo!');
            }
        }
    }

    /**
     * Cria um Lock nas numerações conforme a Regra do controle
     *
     */
    private function bloqueiaControleNumeracao()
    {
        $sWhere = self::getInstance()->getRegraNumeracao();
        $oDaoProtocoloNumeracao = db_utils::getDao("protprocessonumeracao");
        $sSqlNumeracao = $oDaoProtocoloNumeracao->sql_query_file(
            null,
            "p07_proximonumero",
            null,
            $sWhere
        );
        $sSqlNumeracao .= " for update";
        $oDaoProtocoloNumeracao->sql_record($sSqlNumeracao);
    }

    /**
     * Retorna a regra de numeracao conforme Configuração
     *
     * @return string clausula Where
     */
    private function getRegraNumeracao()
    {
        $sWhere = " p07_ano = " . db_getsession("DB_anousu");
        $oInstancia = self::getInstance();

        switch ($oInstancia->iTipoControle) {
            case self::TIPOSEQUENCIAL:
                break;
            case self::TIPOANO:
            case self::TIPOANO_COM_SIGLA:
                $sWhere .= " and p07_instit = " . db_getsession("DB_instit");
                break;
            case self::TIPOORGAO:
                $sWhere .= " and p07_orgao = {$oInstancia->orgao} ";
                $sWhere .= " and p07_prottipodocumentoprocesso = {$oInstancia->tipoDocumentoProcesso} ";
                $sWhere .= " and p07_instit = " . db_getsession("DB_instit");
                // Validamos se ja existe a configuracao para o orgao e documento, caso nao exista, criamos ela
                $sql = "select * from protocolo.protprocessonumeracao where {$sWhere}";
                $rs = db_query($sql);
                if (pg_num_rows($rs) == 0) {
                    throw new Exception("Órgão não possui configuração de númeração para lançamento do processo.");
                }
                break;
        }

        return $sWhere;
    }


    /**
     * @return string
     * @throws Exception
     */
    public static function formataNumeracaoOrgao(
        $numeroProcesso,
        $idOrgao = 0,
        $idTipoDocumentoProcesso = 0,
        $siglaDoDocumentoNaNumeracao = false
    ) {
        $oInstancia = self::getInstance();
        switch ($oInstancia->iTipoControle) {
            case self::TIPOORGAO:
                $numeroProcesso = str_pad($numeroProcesso, 5, '0', STR_PAD_LEFT);
                $orgao = str_pad($idOrgao, 2, '0', STR_PAD_LEFT);
                $sigla = ProcessoProtocoloNumeracao::getSigla($idTipoDocumentoProcesso);
                $numeroProcesso = "{$sigla}{$orgao}{$numeroProcesso}";
                break;
            case self::TIPOANO_COM_SIGLA:
                $numeroProcesso = str_pad($numeroProcesso, 5, '0', STR_PAD_LEFT);
                $sigla = ProcessoProtocoloNumeracao::getSigla($idTipoDocumentoProcesso);
                $numeroProcesso = "{$sigla}{$numeroProcesso}";
                break;
            default:
                if ($siglaDoDocumentoNaNumeracao) {
                    $numeroProcesso = str_pad($numeroProcesso, 5, '0', STR_PAD_LEFT);
                    $sigla = ProcessoProtocoloNumeracao::getSigla($idTipoDocumentoProcesso);
                    $numeroProcesso = "{$sigla}{$numeroProcesso}";
                }
                break;
        }

        return $numeroProcesso;
    }


    public static function getSigla($idTipoDocumentoProcesso)
    {
        if (empty($idTipoDocumentoProcesso)) {
            return null;
        }
        $dao = new cl_prottipodocumentoprocesso;
        $sql = $dao->sql_query_file($idTipoDocumentoProcesso, 'p91_sigla');
        $pgTipodocumento = db_query($sql);

        if (pg_num_rows($pgTipodocumento) > 0) {
            $rsSigla = pg_fetch_assoc($pgTipodocumento);
        } else {
            throw new \Exception("Erro ao incluir processo.\nPor favor, contante o suporte.");
        }

        return $rsSigla['p91_sigla'];
    }

    /**
     * retorna o tipo de configuracao da instituicao
     * @return integer
     */
    public static function getTipoConfiguracao()
    {
        if (self::$oInstance == null) {
            self::$oInstance = new ProcessoProtocoloNumeracao();
        }
        return self::$oInstance->iTipoControle;
    }
}
