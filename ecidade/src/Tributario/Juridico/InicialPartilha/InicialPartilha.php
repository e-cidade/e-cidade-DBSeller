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

namespace ECidade\Tributario\Juridico\InicialPartilha;

use ECidade\Tributario\Juridico\Interfaces\Partilha;
use ECidade\Tributario\Juridico\Interfaces\PartilhaCusta;
use \inicial as InicialModel;
use DateTime;

class InicialPartilha implements Partilha
{
    /**
     * @var int
     */
    private $iCodigo;

    /**
     * @var InicialModel;
     */
    private $oInicial;

    /**
     * @var int
     */
    private $iTipoLancamento;

    /**
     * @var DateTime
     */
    private $oDataPagamento;

    /**
     * @var string
     */
    private $sObservacao;

    /**
     * @var float
     */
    private $nValorPartilha = 0;

    /**
     * @var DateTime
     */
    private $oDataPartilha;

    /**
     * Custas da partilha
     * @var InicialPartilhaCustas[]
     */
    private $aCustas = array();

    /**
     * Sequencial da Inicial
     * @var int
     */
    private $iCodigoInicial;

    /**
     * @var string
     */
    private $sJustificativa = '';

    /**
     * @var bool
     */
    private $lPartilhaPagaMigracao;
    
    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->iCodigo;
    }

    /**
     * @param int $iCodigo
     */
    public function setCodigo($iCodigo)
    {
        $this->iCodigo = $iCodigo;
    }

    /**
     * @return InicialModel
     */
    public function getInicial()
    {
        return $this->oInicial;
    }

    /**
     * @param InicialModel $oInicial
     */
    public function setInicial(InicialModel $oInicial)
    {
        $this->iCodigoInicial = $oInicial->getCodigoInicial();
        $this->oInicial = $oInicial;
    }

    /**
     * @return int
     */
    public function getTipoLancamento()
    {
        return $this->iTipoLancamento;
    }

    /**
     * @param int $iTipoLancamento
     */
    public function setTipoLancamento($iTipoLancamento)
    {
        $this->iTipoLancamento = $iTipoLancamento;
    }

    /**
     * @return DateTime
     */
    public function getDataPagamento()
    {
        return $this->oDataPagamento;
    }

    /**
     * @param DateTime $oDataPagamento
     */
    public function setDataPagamento(DateTime $oDataPagamento)
    {
        $this->oDataPagamento = $oDataPagamento;
    }

    /**
     * @return string
     */
    public function getObservacao()
    {
        return $this->sObservacao;
    }

    /**
     * @param string $sObservacao
     */
    public function setObservacao($sObservacao)
    {
        $this->sObservacao = $sObservacao;
    }

    /**
     * @return float
     */
    public function getValorPartilha()
    {
        return $this->nValorPartilha;
    }

    /**
     * @param float $nValorPartilha
     */
    public function setValorPartilha($nValorPartilha)
    {
        $this->nValorPartilha = $nValorPartilha;
    }

    /**
     * @return DateTime
     */
    public function getDataPartilha()
    {
        return $this->oDataPartilha;
    }

    /**
     * @param DateTime $oDataPartilha
     */
    public function setDataPartilha(DateTime $oDataPartilha)
    {
        $this->oDataPartilha = $oDataPartilha;
    }

    /**
     * @return int
     */
    public function getCodigoInicial()
    {
        return $this->iCodigoInicial;
    }

    /**
     * @param int $iCodigoInicial
     */
    public function setCodigoInicial($iCodigoInicial)
    {
        $this->iCodigoInicial = $iCodigoInicial;
    }

    /**
     * @param string $sJustificativa
     */
    public function setJustificativa($sJustificativa)
    {
        $this->sJustificativa = $sJustificativa;
    }

    /**
     * @return string
     */
    public function getJustificativa()
    {
        return $this->sJustificativa;
    }

    /**
     * @return InicialPartilhaCustas[] $aCustas
     */
    public function getCustas()
    {
        if (empty($this->aCustas)) {
            return array();
        }
        return $this->aCustas;
    }

    /**
     * @param InicialPartilhaCustas $oCustas
     */
    public function addCustas(PartilhaCusta $oCustas)
    {
        $this->aCustas[] = $oCustas;
    }

    /**
     * Limpa as custas da partilha
     */
    public function resetCustas()
    {
        $this->aCustas = array();
    }

    /**
     * Reseta a data de pagamento
     */
    public function resetDataPagamento()
    {
        $this->oDataPagamento = null;
    }

    /**
     * @param bool
     */
    public function setPartilhaPagaMigracao($lPartilhaPagaMigracao)
    {
        $this->lPartilhaPagaMigracao = $lPartilhaPagaMigracao;
    }

    /**
     * @return bool
     */
    public function getPartilhaPagaMigracao()
    {
        return $this->lPartilhaPagaMigracao;
    }
}
