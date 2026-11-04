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
namespace ECidade\Tributario\Juridico\ProcessoEletronico;

use ECidade\Tributario\Juridico\ProcessoEletronico\Parser\EnvioRemessaParser;
use ECidade\Tributario\Juridico\ProcessoEletronico\Webservice\RJ\Entrega;
use ECidade\Tributario\Juridico\ProcessoEletronico\Webservice\RJ\Servico;
use ECidade\Tributario\Juridico\ProcessoEletronico\Webservice\Usuario;

/**
 * Class Remessa
 * @package ECidade\Tributario\Juridico\ProcessoEletronico
 */
class Remessa
{


    /**
     * Código da Instituição
     * @var integer
     */
    private $iInstituicao = 1;


    /**
     * @var Configuracao;
     */
    private $configuracao;

    /**
     * @param \ECidade\Tributario\Juridico\ProcessoEletronico\Configuracao $oConfiguracao
     */
    public function __construct(Configuracao $oConfiguracao)
    {
        $this->configuracao = $oConfiguracao;
    }

    /**
     * @return Configuracao
     */
    public function getConfiguracao()
    {
        return $this->configuracao;
    }

    /**
     * @param Configuracao $configuracao
     */
    public function setConfiguracao($configuracao)
    {
        $this->configuracao = $configuracao;
    }


    /**
     * Realiza o envio dos dados para o TJ o processo é sincrono.
     * @param $dados
     * @return Webservice\RJ\TipoEntregarManifestacaoProcessualResposta
     * @throws \BusinessException
     * @throws \DBException
     */
    public function enviar($dados)
    {
        $entrega = new Entrega();
        $usuario = new Usuario();
        $usuario->setSenha($this->configuracao->getSenha());
        $usuario->setUsuario($this->configuracao->getUsuario());
        $entrega->setUsuario($usuario);
        $oRemessa = $entrega->entregarManifestacaoProcessual($dados);

        try {
            $oSoap    = new Servico($this->getConfiguracao()->getUrlParaRequisicao());
            $oRetorno = $oSoap->entregarManifestacaoProcessual($oRemessa);

            $oRetornoParsed = EnvioRemessaParser::parse($oRetorno);

            if ($oRetornoParsed->getStatus() == false) {
                throw new \BusinessException($oRetornoParsed->getMensagem());
            }

            return $oRetornoParsed;
        } catch (\Exception $oErro) {
            throw new \BusinessException("Erro de comunicação com o Serviço do TJ/RJ: ".$oErro->getMessage());
        }
    }


    /**
     * Alteramos o código da instituição da remessa
     * @param integer $iInstituicao
     */
    public function setInstituicao($iInstituicao)
    {
        $this->iInstituicao = $iInstituicao;
    }

    /**
     * Buscamos o código da instituição da remessa
     * @return integer $iInstituicao
     */
    public function getInstituicao()
    {
        return $this->iInstituicao;
    }
}
