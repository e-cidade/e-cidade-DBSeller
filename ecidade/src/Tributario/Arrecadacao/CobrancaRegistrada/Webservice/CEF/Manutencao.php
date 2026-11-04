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

namespace ECidade\Tributario\Arrecadacao\CobrancaRegistrada\Webservice\CEF;

use ECidade\Tributario\Arrecadacao\CobrancaRegistrada\Webservice\CEF\Arquivo\RequisicaoInterface;
use ECidade\V3\Extension\Logger;
use ECidade\V3\Extension\Registry;
use SoapClient;

class Manutencao extends ConexaoSoap
{
  /**
   * Constante com o wsdl do webservice desejado
   */
    const WSDL_HOMOLOGACAO= "https://des.barramento.caixa.gov.br/sibar/ManutencaoCobrancaBancaria/Boleto/Externo?wsdl";
    const WSDL_PRODUCAO   = "https://barramento.caixa.gov.br/sibar/ManutencaoCobrancaBancaria/Boleto/Externo?wsdl";
    const USUARIO_HOMOLOGACAO = "SGCBS01D";

  /**
   * Código do banco que utiliza este webservice
   */
    const CODIGO_BANCO = "104";

  /**
   * Construtor da classe
   *
   * @param RequisicaoInterface $oRequisicao
   */
    public function __construct(RequisicaoInterface $oRequisicao)
    {
        ini_set("default_socket_timeout", 30);

        $oContext = stream_context_create(array(
        'ssl' => array(
            'ciphers' => 'DEFAULT:!DH',
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
        ));

        $aOpcoes = array("soap_version"   => SOAP_1_1,
                     "stream_context" => $oContext,
                     "cache_wsdl"     => WSDL_CACHE_NONE,
                     "trace"          => true,
                     "exceptions"     => true
                    );

        $oRegistro = $oRequisicao->getRegistro();

        $sWsdl = self::WSDL_PRODUCAO;

        if (trim($oRegistro->usuarioServico) == self::USUARIO_HOMOLOGACAO) {
            $sWsdl = self::WSDL_HOMOLOGACAO;
        }

        $iTentativas = 5;
        $iSegundosEspera = 2;

        while ($iTentativas-- > 0) {
            try {
                $this->oSoapClient = new SoapClient($sWsdl, $aOpcoes);
                $this->oRequisicao = $oRequisicao;
                break;
            } catch (\Exception $error) {
                if ($iTentativas == 0) {
                    throw new \Exception('Erro ao se conectar com a instituição financeira.');
                }
                sleep($iSegundosEspera);
            }
        }
    }

  /**
   * Processamos a requisição conforme informações disponibilizadas
   *
   * @return \stdClass
   */
    public function processarRequisicao($emitePix = false)
    {
        $debugWebservicecaixa = null;

        if (Registry::get('app.container')->has('app.webservice_caixa.debug')) {
          //  $debugWebservicecaixa =  Registry::get('app.container')->get('app.webservice_caixa.debug');
        }

        if (!empty($debugWebservicecaixa) && !empty($debugWebservicecaixa->debug) && $debugWebservicecaixa->debug) {
            $domDocument = $this->oRequisicao->getRequestXml($emitePix);
            $xml         = $domDocument->saveXML();

            $registro      = $this->oRequisicao->getRegistro();
            $identificacao = (object)array(
            'cpfcnpj'         => $registro->cpfcnpj,
            'nome'            => $registro->nome,
            'numeroDocumento' => $registro->numeroDocumento,
            'valor'           => $registro->valor
            );

            $path   = ECIDADE_PATH . 'tmp/.log/webservice_caixa/';

            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }

            $path   .= $identificacao->cpfcnpj . '_' . $identificacao->numeroDocumento . '_' . date('Ymd');
            $logger  = new Logger(($path . '.log'), Logger::DEBUG);

            $cpfcnpj = $identificacao->cpfcnpj;

            if (preg_match('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', $cpfcnpj)) {
                $replacement = "$1.$2.$3-$4";
                $cpfcnpj = preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', $replacement, $cpfcnpj);
            } elseif (preg_match('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', $cpfcnpj)) {
                $replacement = "$1.$2.$3/$4-$5";
                $cpfcnpj = preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', $replacement, $cpfcnpj);
            }

            $logger->debug('-------- Gerando XML para registro no webservice da Caixa  --------');
            $origem = (!empty($debugWebservicecaixa->origem) && $debugWebservicecaixa->origem ?
                              $debugWebservicecaixa->origem : 'NAO INFORMADO');
            $logger->debug('-- Origem Emissao........: ' . $origem);
            $logger->debug('-- Contribuinte..........: ' . $identificacao->cpfcnpj);
            $logger->debug('-- CNPJ/CPF..............: ' . $identificacao->nome);
            $logger->debug('-- Valor.................: ' . $identificacao->valor);
            $logger->debug('-- Nosso Numero..........: ' . $identificacao->numeroDocumento);

            $fileXML = new Logger(($path . '.xml'));
            $fileXML->writeln($xml);
        }

        parent::processarRequisicao($emitePix);
    }
}
