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

namespace ECidade\RecursosHumanos\RH\PontoEletronico\Marcacao;

use ECidade\RecursosHumanos\RH\PontoEletronico\Marcacao\MarcacaoPonto;

/**
 * Classe que representa uma marcação de saída do horário ponto
 * Class MarcacaoPontoSaida
 * @package ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Model
 * @author Renan Silva <renan.silva@dbseller.com.br>
 */
class MarcacaoPontoSaida extends MarcacaoPonto
{

    /**
     * @var \DateTime $oMarcacaoEntrada
     */
    private $oMarcacaoEntrada;

    public function __clone()
    {
        parent::__clone();
        $this->oMarcacaoEntrada = $this->oMarcacaoEntrada ? clone $this->oMarcacaoEntrada : null;
    }

    /**
     * Define a hora da marcação
     *
     * @param \DateTime $oMarcacaoEntrada
     */
    public function setMarcacaoEntrada($oMarcacaoEntrada)
    {
        $this->oMarcacaoEntrada = $oMarcacaoEntrada;
    }

    /**
     * Retorna a hora da marcação
     *
     * @return \DateTime $oMarcacaoEntrada
     */
    public function getMarcacaoEntrada()
    {
        return $this->oMarcacaoEntrada;
    }

    /**
     * Retorna o horário trabalhado
     *
     * @return \DateInterval
     */
    public function getHorarioTrabalhado()
    {
        $retorno = null;

        if (!empty($this->oMarcacaoEntrada) && !is_null($this->oMarcacao)) {
            $marcacao = $this->oMarcacaoEntrada;
            /**
             * Valida se a Marcacao de Entrada e instancia de MarcacaoPonto
             * Em alguns casos foi constatado que a marcacao nao era Datetime, gerando erro no processamento
             * Solucao encontrada: criar variavel para nao gerar impactos e setar o Datetime na variavel
             *
             */
            if ($marcacao instanceof MarcacaoPonto) {
                $marcacao = $marcacao->getMarcacao();
            }
            $retorno = $marcacao->diff($this->oMarcacao);
        }
        return $retorno;
    }

    public function isMarcacaoSaida()
    {
        return true;
    }

    public function retornarClassePai()
    {

        $classePai = new MarcacaoPonto();
        $classePai->setMarcacao($this->getMarcacao());
        $classePai->setTipo($this->getTipo());
        $classePai->setCodigo($this->getCodigo());
        $classePai->setManual($this->isManual());
        $classePai->setData($this->getData());

        if ($this->getJustificativa() != null) {
            $classePai->setJustificativa($this->getJustificativa());
        }

        return $classePai;
    }
}
