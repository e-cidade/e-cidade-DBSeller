<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2014  DBSeller Servicos de Informatica
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

namespace ECidade\Tributario\Arrecadacao\CobrancaRegistrada;

use BusinessException;
use convenio;
use DateTime;
use db_utils;
use DBDate;
use DBException;
use ECidade\Tributario\Arrecadacao\CobrancaRegistrada\Arquivo\Header;
use ECidade\Tributario\Arrecadacao\CobrancaRegistrada\Webservice\BB\Arquivo\IncluiBoleto as IncluiBoletoBB;
use ECidade\Tributario\Arrecadacao\CobrancaRegistrada\Webservice\BB\Arquivo\Repository as RepositoryBB;
use ECidade\Tributario\Arrecadacao\CobrancaRegistrada\Webservice\BB\Manutencao as ManutencaoBB;
use ECidade\Tributario\Arrecadacao\CobrancaRegistrada\Webservice\CEF\Arquivo\IncluiBoleto as IncluiBoletoCEF;
use ECidade\Tributario\Arrecadacao\CobrancaRegistrada\Webservice\CEF\Arquivo\Repository as RepositoryCEF;
use ECidade\Tributario\Arrecadacao\CobrancaRegistrada\Webservice\CEF\Manutencao as ManutencaoCEF;
use App\Domain\Tributario\Caixa\Models\Recibobarpix;
use Exception;
use Recibo;

/**
 * Class CobrancaRegistrada
 * @package ECidade\Tributario\Arrecadacao\CobrancaRegistrada
 */
class CobrancaRegistrada
{
    /**
     * Verifica se o convênio passado por parâmetro é um convenio de cobrança valido
     *
     * @param $iCodigoConvenio
     * @return bool
     * @throws Exception
     */
    public static function validaConvenioCobranca($iCodigoConvenio)
    {
        $oDaoConvenio = new \cl_cadconvenio();
        $sSqConvenio  = $oDaoConvenio->sql_query_convenio_cobranca(
            $iCodigoConvenio,
            "ar12_sequencial, ar12_cadconveniomodalidade, ar13_carteira"
        );
        $rsConvenio   = \db_query($sSqConvenio);

        if (empty($rsConvenio) || !pg_num_rows($rsConvenio)) {
            throw new Exception("Erro ao buscar dados do convenio.");
        }

        $oDadosConvenio = db_utils::fieldsMemory($rsConvenio, 0);

        if ($oDadosConvenio->ar12_cadconveniomodalidade != convenio::MODALIDADE_COBRANCA) {
            return false;
        }

        /**
         * Quando o convenio for do Banco do Brasil, a acarteira deve ser a 17
         */
        if ($oDadosConvenio->ar12_sequencial == convenio::TIPO_CONVENIO_COMPENSACAO_BDL
                &&
            $oDadosConvenio->ar13_carteira == "17") {
            return true;
        }

        /**
         * Quando o convenio for da Caixa, a carteira deve ser a 14
         */
        if ($oDadosConvenio->ar12_sequencial == convenio::TIPO_CONVENIO_COMPENSACAO_SIGCB
                &&
            $oDadosConvenio->ar13_carteira == "14"
        ) {
            return true;
        }

        /**
         * Quando o convenio for da Banrisul
         */
        if ($oDadosConvenio->ar12_sequencial == convenio::TIPO_CONVENIO_COMPENSACAO_BDL
                &&
            $oDadosConvenio->ar13_carteira == "1"
        ) {
            return true;
        }

        /**
         * Quando o convenio for do Banco do Brasil em Niteroi
         */
        if ($oDadosConvenio->ar12_sequencial == convenio::TIPO_CONVENIO_COBRANCA_REGISTRADA
                &&
            $oDadosConvenio->ar13_carteira == "17"
        ) {
            return true;
        }

        return false;
    }

    /**
     * Adiciona um recibo na fila para geração dos arquivos de cobrança registrada
     *
     * @param Recibo $oRecibo
     * @param $iCodigoConvenio
     * @return bool
     * @throws Exception
     */
    public static function adicionarRecibo(Recibo $oRecibo, $iCodigoConvenio)
    {
        if (empty($iCodigoConvenio)) {
            throw new Exception("Código do convênio não informado.");
        }

        if ($oRecibo->getNumpreRecibo() == '') {
            throw new Exception("Recibo inválido.");
        }

        $oDaoReciboRegistra = new \cl_reciboregistra();
        $oDaoReciboRegistra->k146_numpre = $oRecibo->getNumpreRecibo();
        $oDaoReciboRegistra->k146_convenio = $iCodigoConvenio;
        $oDaoReciboRegistra->incluir();

        if ($oDaoReciboRegistra->erro_status == '0') {
            throw new Exception("Erro ao incluir recibo para cobrança registrada.");
        }

        return true;
    }


    /**
     * Remove os recibos passados por parametro
     *
     * @param $aRecibos
     * @return bool
     * @throws Exception
     */
    public static function removerRecibos($aRecibos)
    {
        if (empty($aRecibos)) {
            return false;
        }

        $oDaoRecibos = new \cl_reciboregistra();

        /**
         * Remove os recibos da fila
         */
        $oDaoRecibos->excluir(null, "k146_numpre in (" . implode(',', $aRecibos) . ")");

        if ($oDaoRecibos->erro_status = "0") {
            throw new Exception("Erro ao remover os recibos da fila.");
        }

        return true;
    }

    /**
     * Função que carrega os dados necessários no model Header
     *
     * @param $iCodigoConvenio
     * @return Header
     * @throws Exception
     */
    public static function carregarHeader($iCodigoConvenio)
    {
        if (empty($iCodigoConvenio)) {
            throw new Exception("Código do convênio não informado.");
        }

        $oDaoReciboRegistra = new \cl_reciboregistra();
        $oDaoConveio        = new \cl_cadconvenio();

        /**
         * Buscamos os dados do convênnio através do código recebido nos parâmetros
         */
        $sCampos     = "ar11_cadtipoconvenio,
                        ar11_instit,
                        ar11_nome,
                        ar13_carteira,
                        ar13_convenio,
                        ar13_cedente,
                        ar13_variacao,
                        ar13_contabancaria,
                        ar13_responsavelnossonumero";
        $sSqlConveio = $oDaoConveio->sql_queryConvenioCobranca($iCodigoConvenio, $sCampos);
        $rsConveio   = db_query($sSqlConveio);

        if (empty($rsConveio)) {
            throw new Exception("Erro ao buscar os dados do convênio para o Header");
        }

        $oConvenio = db_utils::fieldsMemory($rsConveio, 0);

        $oDadosConvenio = new \stdClass();
        $oDadosConvenio->instituicao              = $oConvenio->ar11_instit;
        $oDadosConvenio->nome                     = $oConvenio->ar11_nome;
        $oDadosConvenio->tipo_convenio            = $oConvenio->ar11_cadtipoconvenio;
        $oDadosConvenio->numero_convenio          = $oConvenio->ar13_convenio;
        $oDadosConvenio->carteira                 = $oConvenio->ar13_carteira;
        $oDadosConvenio->cedente                  = $oConvenio->ar13_cedente;
        $oDadosConvenio->variacao                 = $oConvenio->ar13_variacao;
        $oDadosConvenio->responsavel_nosso_numero = $oConvenio->ar13_responsavelnossonumero;

        $oInstituicao   = new \Instituicao($oConvenio->ar11_instit);
        $oContaBancaria = new \ContaBancaria($oConvenio->ar13_contabancaria);

        /**
         * Buscamos o novo seguencial da remessa
         */
        $sCampo      = "coalesce(max(k147_sequencialremessa), 0) + 1 as sequencial_remessa";
        $oDaoRemessa = new \cl_remessacobrancaregistrada();
        $sSqlRemessa = $oDaoRemessa->sql_query_file(null, $sCampo);
        $rsRemessa   = db_query($sSqlRemessa);

        if (empty($rsRemessa)) {
            throw new Exception("Erro ao gerar o sequencial da Remessa");
        }

        $oRemessa = db_utils::fieldsMemory($rsRemessa, 0);

        /**
         * A partir de agora, colocamos os dados recolhidos acima no Header
         */
        $oHeader = new Header();
        $oHeader->setSequencial($oRemessa->sequencial_remessa);
        $oHeader->setLote(0000);
        $oHeader->setInstituicao($oInstituicao);
        $oHeader->setContaBancaria($oContaBancaria);
        $oHeader->setConvenio($oDadosConvenio);

        return $oHeader;
    }

    /**
     * Função que verifica se há usuário do webservice configurado
     *
     * @throws DBException
     * @return boolean
     */
    public static function utilizaIntegracaoWebService($iConvenio)
    {
        $oBancoAgencia = self::getBancoConvenio($iConvenio);

        if (!$oBancoAgencia) {
            return false;
        }

        if (($oBancoAgencia->db89_db_bancos != ManutencaoCEF::CODIGO_BANCO)
                and
            ($oBancoAgencia->db89_db_bancos != ManutencaoBB::CODIGO_BANCO)
        ) {
            return false;
        }

        $oDaoParametrosCobrancaRegistrada = new \cl_parametroscobrancaregistrada();
        $sSqlParametrosCobrancaRegistrada = $oDaoParametrosCobrancaRegistrada->sql_query_file(
            null,
            'ar28_usuario',
            null,
            "ar28_codban = '{$oBancoAgencia->db89_db_bancos}'"
        );

        $rsParametrosCobrancaRegistrada = \db_query($sSqlParametrosCobrancaRegistrada);

        if (empty($rsParametrosCobrancaRegistrada)) {
            throw new DBException("Erro ao buscar dados de configuração.");
        }

        return pg_num_rows($rsParametrosCobrancaRegistrada) > 0;
    }

    /**
     * Função que realiza o processo de cobrança registrada no webservice
     *
     * @param $iNumpreRecibo
     * @param $iConvenio
     * @param $nValor
     * @param bool $lUsuarioExterno
     * @param array $aEmitirPor
     * @return bool
     * @throws BusinessException
     * @throws DBException
     */
    public static function registrarReciboWebservice(
        $iNumpreRecibo,
        $iConvenio,
        $nValor,
        $lUsuarioExterno = false,
        $aEmitirPor = array(),
        $emitePix = false,
        $numpar = null
    ) {
        $oDataSessao = new DateTime;
        $oDataSessao->setTimestamp(db_getsession('DB_datausu'));

        $fDiferenca = DBDate::calculaIntervaloEntreDatas(
            new DBDate($oDataSessao->format(DBDate::DATA_EN)),
            new DBDate(date(DBDate::DATA_EN)),
            'd'
        );

        /**
         * Não registra a cobrança em função de recibos que precisam ser baixados, devido a já terem
         * sido pagos por fora do sistema (exemplo: inicial do foro, depósito judicial ou penhora).
         */
        if ($fDiferenca != 0) {
            return false;
        }

        $oBancoAgencia = self::getBancoConvenio($iConvenio);

        switch ($oBancoAgencia->db89_db_bancos) {
            case ManutencaoBB::CODIGO_BANCO:
                self::processarBB(
                    $iNumpreRecibo,
                    $iConvenio,
                    $nValor,
                    $lUsuarioExterno,
                    $aEmitirPor
                );
                break;

            case ManutencaoCEF::CODIGO_BANCO:
                self::processarCEF(
                    $iNumpreRecibo,
                    $iConvenio,
                    $nValor,
                    $lUsuarioExterno,
                    $aEmitirPor,
                    $emitePix,
                    $numpar
                );
                break;

            default:
                throw new DBException("Erro ao selecionar banco para processar.");
                break;
        }

        $cl_recibocobrancawebservice = new \cl_recibocobrancawebservice();
        $cl_recibocobrancawebservice->k199_sequencial = null;
        $cl_recibocobrancawebservice->k199_numnov = $iNumpreRecibo;
        $cl_recibocobrancawebservice->k199_convenio = $iConvenio;

        $cl_recibocobrancawebservice->incluir(null);
    }

    /**
     * Função para processar a cobrança registrada pro webservice da Caixa
     * @param integer iNumpreRecibo
     * @param integer iConvenio
     * @param float nValor
     * @param boolean lUsuarioExterno
     * @param array aEmitirPor
     * @return void
     */
    private static function processarCEF(
        $iNumpreRecibo,
        $iConvenio,
        $nValor,
        $lUsuarioExterno,
        $aEmitirPor,
        $emitePix = false,
        $numpar = null
    ) {
        $oRepository = new RepositoryCEF();
        $oRegistro   = $oRepository->getDadosIncluiBoleto($iNumpreRecibo, $iConvenio, $nValor, $aEmitirPor);

        $oIncluiBoleto = new IncluiBoletoCEF($oRegistro);
        $oManutencao   = new ManutencaoCEF($oIncluiBoleto);

        try {
            $oManutencao->processarRequisicao($emitePix);
            $oResposta = $oManutencao->getResposta();
        } catch (\SoapFault $erroSoap) {
            throw new BusinessException($erroSoap->getMessage());
        }

        $iCodigoRetorno = $oResposta->getCodigoRetorno();
        if ($emitePix) {
            $txid = "N{$iNumpreRecibo}";
            $txid .= "P{$numpar}";
            $txid .= "R".substr(time(), -4);
            $conciliacaosolicitante = str_pad($txid, 35, "0", STR_PAD_LEFT);
            $oDadosQrCode = $oResposta->getDadosQrCode();
            $estadosolicitacao = 'ATIVA';
            $codban = '104'; //Código do banco CEF
            $oReciboBarPix = new Recibobarpix();
            $oReciboBarPix->k00_numpre = $iNumpreRecibo;
            $oReciboBarPix->k00_numpar = $numpar;
            $oReciboBarPix->k00_codbar = $oDadosQrCode['codBarras'];
            $oReciboBarPix->k00_criacaosolicitacao = date('Y-m-d H:i:s');
            $oReciboBarPix->k00_estadosolicitacao = $estadosolicitacao;
            $oReciboBarPix->k00_conciliacaosolicitante = $conciliacaosolicitante;
            $oReciboBarPix->k00_qrcode = $oDadosQrCode['qrCode'];
            $oReciboBarPix->k00_linkqrcode = $oDadosQrCode['urlQrCode'];
            $oReciboBarPix->k00_numeroversaosolicitacaopagamento = 0;
            $oReciboBarPix->k00_codban = $codban;
            $oReciboBarPix->save();
        }

        if (!empty($iCodigoRetorno)) {
            throw new BusinessException($oResposta->getMensagemRetorno($lUsuarioExterno));
        }
    }

    /**
     * Função para processar a cobrança registrada pro webservice do Banco do Brasil
     * @param integer iNumpreRecibo
     * @param integer iConvenio
     * @param float nValor
     * @param boolean lUsuarioExterno
     * @param array aEmitirPor
     * @return void
     */
    private static function processarBB(
        $iNumpreRecibo,
        $iConvenio,
        $nValor,
        $lUsuarioExterno,
        $aEmitirPor
    ) {
        $oRepository = new RepositoryBB();
        $oRegistro   = $oRepository->getDadosIncluiBoleto($iNumpreRecibo, $iConvenio, $nValor, $aEmitirPor);

        $oIncluiBoleto = new IncluiBoletoBB($oRegistro);
        $oManutencao   = new ManutencaoBB($oIncluiBoleto);

        $oManutencao->processarRequisicao();
        $oResposta = $oManutencao->getResposta();

        $lErro = $oResposta->getErro();

        if ($lErro) {
            throw new BusinessException($oResposta->getMensagemRetorno($lUsuarioExterno));
        }
    }

    /**
     * Função para buscar o banco pelo convênio
     * @param integer iConvenio
     * @return boolean
     * @return object
    */
    public static function getBancoConvenio($iConvenio)
    {
        $oConvenioCobranca = self::getConvenio($iConvenio);

        $oDaoBancoAgencia = new \cl_bancoagencia();
        $sSqlBancoAgencia = $oDaoBancoAgencia->sql_query_file($oConvenioCobranca->ar13_bancoagencia, "db89_db_bancos");
        $rsBancoAgencia   = $oDaoBancoAgencia->sql_record($sSqlBancoAgencia);

        if (!$rsBancoAgencia) {
            throw new DBException("Erro ao buscar dados da agência do convênio de cobrança.");
        }

        return db_utils::fieldsMemory($rsBancoAgencia, 0);
    }

    public static function getConvenio($iConvenio)
    {
        $oDaoCadConvenio = new \cl_cadconvenio();
        $sSqlCadConvenio = $oDaoCadConvenio->sql_queryConvenioCobranca($iConvenio);
        $rsCadConvenio   = db_query($sSqlCadConvenio);

        if (!$rsCadConvenio) {
            throw new DBException("Erro ao buscar dados do convênio de cobrança.");
        }

        /**
         * Se não vier registro na consulta, o convênio não é de cobrança,
         * portanto não deve utilizar a integração com o webservice
         */
        if (pg_num_rows($rsCadConvenio) == 0) {
            return false;
        }

        return db_utils::fieldsMemory($rsCadConvenio, 0);
    }
}
