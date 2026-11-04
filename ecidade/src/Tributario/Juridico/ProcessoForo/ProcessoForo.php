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

namespace ECidade\Tributario\Juridico\ProcessoForo;

use ECidade\Tributario\Arrecadacao\Custas\Interfaces;

use DateTime;
use ECidade\Tributario\Juridico\Inicial\Inicial as InicialEntity;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha as ProcessoForoPartilhaEntity;

class ProcessoForo implements Interfaces\ParcelamentoHonorario
{
    /**
     * @var int
     */
    private $iCodigo;

    /**
     * @var int
     */
    private $iCodigoForo;

    /**
     * @var int
     */
    private $iCodigoProcessoForoMov;

    /**
     * @var int
     */
    private $iCodigoUsuario;

    /**
     * @var int
     */
    private $iCodigoVara;

    /**
     * @var DateTime
     */
    private $oData;

    /**
     * @var float
     */
    private $nValorInicial = 0;

    /**
     * @var string
     */
    private $sObservacao;

    /**
     * @var boolean
     */
    private $lAnulado = false;

    /**
     * @var int
     */
    private $iInstit;

    /**
     * @var int
     */
    private $iCodigoCartorio;

    /** @var InicialEntity[] */
    private $iniciais;

    /** @var ProcessoForoPartilhaEntity[] */
    private $processoForoPartilhas = array();


    private $parcelasHonorarios;


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
     * @return int
     */
    public function getCodigoForo()
    {
        return $this->iCodigoForo;
    }

    /**
     * @param int $iCodigoForo
     */
    public function setCodigoForo($iCodigoForo)
    {
        $this->iCodigoForo = $iCodigoForo;
    }

    /**
     * @return int
     */
    public function getCodigoProcessoForoMov()
    {
        return $this->iCodigoProcessoForoMov;
    }

    /**
     * @param int $iCodigoProcessoForoMov
     */
    public function setCodigoProcessoForoMov($iCodigoProcessoForoMov)
    {
        $this->iCodigoProcessoForoMov = $iCodigoProcessoForoMov;
    }

    /**
     * @return int
     */
    public function getCodigoUsuario()
    {
        return $this->iCodigoUsuario;
    }

    /**
     * @param int $iCodigoUsuario
     */
    public function setCodigoUsuario($iCodigoUsuario)
    {
        $this->iCodigoUsuario = $iCodigoUsuario;
    }

    /**
     * @return int
     */
    public function getCodigoVara()
    {
        return $this->iCodigoVara;
    }

    /**
     * @param int $iCodigoVara
     */
    public function setCodigoVara($iCodigoVara)
    {
        $this->iCodigoVara = $iCodigoVara;
    }

    /**
     * @return DateTime
     */
    public function getData()
    {
        return $this->oData;
    }

    /**
     * @param DateTime $oData
     */
    public function setData($oData)
    {
        $this->oData = $oData;
    }

    /**
     * @return float
     */
    public function getValorInicial()
    {
        return $this->nValorInicial;
    }

    /**
     * @param float $nValorInicial
     */
    public function setValorInicial($nValorInicial)
    {
        $this->nValorInicial = $nValorInicial;
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
     * @return boolean
     */
    public function getAnulado()
    {
        return $this->lAnulado;
    }

    /**
     * @param boolean $lAnulado
     */
    public function setAnulado($lAnulado)
    {
        $this->lAnulado = $lAnulado;
    }

    /**
     * @return int
     */
    public function getInstit()
    {
        return $this->iInstit;
    }

    /**
     * @param int $iInstit
     */
    public function setInstit($iInstit)
    {
        $this->iInstit = $iInstit;
    }

    /**
     * @return int
     */
    public function getCodigoCartorio()
    {
        return $this->iCodigoCartorio;
    }

    /**
     * @param int $iCodigoCartorio
     */
    public function setCodigoCartorio($iCodigoCartorio)
    {
        $this->iCodigoCartorio = $iCodigoCartorio;
    }

    /**
     * @return InicialEntity[]
     */
    public function getIniciais()
    {
        return $this->iniciais;
    }

    /**
     * @param InicialEntity[] $iniciais
     * @return ProcessoForo
     */
    public function setIniciais($iniciais)
    {
        $this->iniciais = $iniciais;
        return $this;
    }

    /**
     * @param InicialEntity $inicial
     * @return ProcessoForo
     */
    public function addInicial($inicial)
    {
        $this->iniciais[] = $inicial;
        return $this;
    }

    /**
     * @return ProcessoForoPartilhaEntity[]
     */
    public function getProcessoForoPartilhas()
    {
        return $this->processoForoPartilhas;
    }

    /**
     * @param ProcessoForoPartilhaEntity[] $processoForoPartilhas
     * @return ProcessoForo
     */
    public function setProcessoForoPartilhas($processoForoPartilhas)
    {
        $this->processoForoPartilhas = $processoForoPartilhas;
        return $this;
    }

    /**
     * @param ProcessoForoPartilhaEntity $processoForoPartilha
     * @return ProcessoForo
     */
    public function addProcessoForoPartilha($processoForoPartilha)
    {
        $this->processoForoPartilhas[] = $processoForoPartilha;
        return $this;
    }

    /**
     * @return ProcessoForoPartilhaEntity|null
     */
    public function getFirstProcessoForoPartilha()
    {
        if (!empty($this->processoForoPartilhas)) {
            return reset($this->processoForoPartilhas);
        }

        return null;
    }

    public function __toString()
    {
        return $this->iCodigo;
    }

    /**
     * @return int
     */
    public function getParcelasHonorarios()
    {
        return $this->parcelasHonorarios;
    }

    /**
     * @param int $parcelasHonorarios
     */
    public function setParcelasHonorarios($parcelasHonorarios)
    {
        $this->parcelasHonorarios = $parcelasHonorarios;
    }
}
