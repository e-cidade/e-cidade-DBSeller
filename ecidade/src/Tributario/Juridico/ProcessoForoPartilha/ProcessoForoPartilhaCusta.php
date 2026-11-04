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

namespace ECidade\Tributario\Juridico\ProcessoForoPartilha;

use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha;
use ECidade\Tributario\Juridico\Interfaces\PartilhaCusta;
use \Taxa;
use \Exception;

class ProcessoForoPartilhaCusta implements PartilhaCusta
{
    /**
     * @var int
     */
    private $iCodigo;

    /**
     * @var ProcessoForoPartilha;
     */
    private $oProcessoForoPartilha;

    /**
     * @var Taxa
     */
    private $oTaxa;

    /**
     * @var float
     */
    private $fValor;

    /**
     * @var integer
     */
    private $iNumnov;

    /**
     * @var bool
     */
    private $lDispensaLancamentoRecibo;

    /**
     * @var int
     */
    private $iCodigoProcessoForoPartilha;

    /**
     * @var int
     */
    private $iCodigoTaxa;

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
     * @return ProcessoForoPartilha
     */
    public function getProcessoForoPartilha()
    {
        return $this->oProcessoForoPartilha;
    }

    /**
     * @param ProcessoForoPartilha $oProcessoForoPartilha
     */
    public function setProcessoForoPartilha(ProcessoForoPartilha $oProcessoForoPartilha)
    {
        $this->oProcessoForoPartilha = $oProcessoForoPartilha;
    }

    /**
     * @return Taxa
     */
    public function getTaxa()
    {
        return $this->oTaxa;
    }

    /**
     * @param  Taxa $oTaxa
     * @throws Exception
     */
    public function setTaxa(Taxa $oTaxa)
    {
        $this->oTaxa = $oTaxa;
    }

    /**
     * @return float
     */
    public function getValor()
    {
        return $this->fValor;
    }

    /**
     * @param float $fValor
     */
    public function setValor($fValor)
    {
        $this->fValor = $fValor;
    }

    /**
     * @param float $fValor
     */
    public function addValor($fValor)
    {
        $this->fValor = $this->fValor + $fValor;
    }

    /**
     * @return int
     */
    public function getNumnov()
    {
        return $this->iNumnov;
    }

    /**
     * @param int $iNumnov
     */
    public function setNumnov($iNumnov)
    {
        $this->iNumnov = $iNumnov;
    }

    /**
     * @return bool
     */
    public function isDispensaLancamentoRecibo()
    {
        return $this->lDispensaLancamentoRecibo;
    }

    /**
     * @param bool $lDispensaLancamentoRecibo
     */
    public function setDispensaLancamentoRecibo($lDispensaLancamentoRecibo = false)
    {
        $this->lDispensaLancamentoRecibo = $lDispensaLancamentoRecibo;
    }

    /**
     * @return int
     */
    public function getCodigoProcessoForoPartilha()
    {
        return $this->iCodigoProcessoForoPartilha;
    }

    /**
     * @param int $iCodigoProcessoForoPartilha
     */
    public function setCodigoProcessoForoPartilha($iCodigoProcessoForoPartilha)
    {
        $this->iCodigoProcessoForoPartilha = $iCodigoProcessoForoPartilha;
    }

    /**
     * @return int
     */
    public function getCodigoTaxa()
    {
        return $this->iCodigoTaxa;
    }

    /**
     * @param int $iCodigoTaxa
     */
    public function setCodigoTaxa($iCodigoTaxa)
    {
        $this->iCodigoTaxa = $iCodigoTaxa;
    }
}
