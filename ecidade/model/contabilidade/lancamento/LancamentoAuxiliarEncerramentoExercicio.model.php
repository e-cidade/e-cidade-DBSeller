<?php

/**
 * E-cidade Software Publico para Gestão Municipal
 *   Copyright (C) 2009 DBSeller Serviços de Informática Ltda
 *                          www.dbseller.com.br
 *                          e-cidade@dbseller.com.br
 *   Este programa é software livre; você pode redistribuí-lo e/ou
 *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *   publicada pela Free Software Foundation; tanto a versão 2 da
 *   Licença como (a seu critério) qualquer versão mais nova.
 *   Este programa e distribuído na expectativa de ser útil, mas SEM
 *   QUALQUER GARANTIA; sem mesmo a garantia implícita de
 *   COMERCIALIZAÇÃO ou de ADEQUAÇÃO A QUALQUER PROPÓSITO EM
 *   PARTICULAR. Consulte a Licença Pública Geral GNU para obter mais
 *   detalhes.
 *   Você deve ter recebido uma cópia da Licença Pública Geral GNU
 *   junto com este programa; se não, escreva para a Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307, USA.
 *   Cópia da licença no diretório licenca/licenca_en.txt
 *                                 licenca/licenca_pt.txt
 */

require_once(modification("interfaces/ILancamentoAuxiliar.interface.php"));
require_once(modification("model/contabilidade/lancamento/LancamentoAuxiliarBase.model.php"));

/**
 * Class LancamentoAuxiliarEncerramentoExercicio
 */
class LancamentoAuxiliarEncerramentoExercicio extends LancamentoAuxiliarBase implements ILancamentoAuxiliar
{
    /**
     * Complemento para o lançamento contábil
     * @var string
     */
    protected $sComplemento;

    /**
     * Dados da tabela conhist
     * @var integer
     */
    private $iHistorico;

    /**
     * Empenho com restos a liquidar
     * @var EmpenhoFinanceiro
     */
    private $oEmpenho;

    /**
     *  Conta que deve ser
     * @var MovimentacaoContabil
     */
    private $oMovimentacao;

    /**
     * Caracteristica peculiar
     * @var string
     */
    private $sCaracteristicaPeculiar;


    protected $inversaoContas = false;

    /**
     * Conta de Referencia para o encerramento do documento 1010
     */
    private $iContaReferencia;

    private $nValorTotal = 0;

    private $iCgm;

    /**
     * @var ReceitaContabil
     */
    private $oReceitaContabil;

    /**
     * @var integer
     */
    private $iOrdemLancamento;


    /**
     * Conta a debito
     * @var integer
     */
    private $contaDebito;

    /**
     * Conta a credito
     * @var integer
     */
    private $contaCredito;

    /**
     * Código do recurso orçamentário
     * @var integer
     */
    private $codigoRecurso;

    private $codigoDocumento;

    /**
     * @see ILancamentoAuxiliar::setHistorico()
     */
    public function setHistorico($iHistorico)
    {
        $this->iHistorico = $iHistorico;
    }

    /**
     * @see ILancamentoAuxiliar::getHistorico()
     */
    public function getHistorico()
    {
        return $this->iHistorico;
    }

    /**
     * @see ILancamentoAuxiliar::setValorTotal()
     */
    public function setValorTotal($nValorTotal)
    {
        $this->nValorTotal = $nValorTotal;
    }

    /**
     * @see ILancamentoAuxiliar::getValorTotal()
     */
    public function getValorTotal()
    {
        return $this->nValorTotal;
    }

    /**
     * Executa os lancamentos auxiliares
     * @param int $iCodigoLancamento
     * @param string $dtLancamento
     * @return bool
     * @throws BusinessException|Exception
     */
    public function executaLancamentoAuxiliar($iCodigoLancamento, $dtLancamento)
    {
        $this->setCodigoLancamento($iCodigoLancamento);
        $this->setDataLancamento($dtLancamento);
        if ($this->getEmpenho() instanceof EmpenhoFinanceiro && $this->getEmpenho()->getNumero() != "") {

            $this->setNumeroEmpenho($this->getEmpenho()->getNumero());
            $this->salvarVinculoEmpenho();
        }

        if (!empty($this->sObservacao)) {
            parent::salvarVinculoComplemento();
        }
        parent::salvarVinculoCgm();
        if (!empty($this->iCodigoElemento)) {
            parent::salvarVinculoElemento();
        }
        if (!empty($this->iCodigoDotacao)) {
            $this->salvarVinculoDotacao();
        }

        $this->salvarVinculoReceita();
        $this->salvarVinculoCaracteristicaPeculiar();
        return true;
    }

    protected function salvarVinculoDotacao()
    {

        $oDaoConLanCamDot = new cl_conlancamdot();
        $oDaoConLanCamDot->c73_codlan = $this->iCodigoLancamento;
        $oDaoConLanCamDot->c73_data = $this->dtLancamento;;
        $oDaoConLanCamDot->c73_anousu = db_getsession('DB_anousu');
        $oDaoConLanCamDot->c73_coddot = $this->iCodigoDotacao;
        $oDaoConLanCamDot->incluir($this->iCodigoLancamento);

        if ($oDaoConLanCamDot->erro_status == 0) {

            $sErroMsg = "Não foi possível vincular Lançamento e Dotacao.\n\n";
            $sErroMsg .= "Erro Técnico : {$oDaoConLanCamDot->erro_msg}";
            throw new BusinessException($sErroMsg);
        }

        unset($oDaoConLanCamDot);
        return true;
    }

    /**
     * Seta o codigo da dotacao
     * @param integer $iCodigoDotacao
     */
    public function setDotacao($iCodigoDotacao)
    {
        parent::setCodigoDotacao($iCodigoDotacao);
    }

    /**
     * Retorna o codigo da dotacao
     * @return integer parent::$iCodigoDotacao
     */
    public function getDotacao()
    {
        return parent::getCodigoDotacao();
    }

    /**
     * Vincula a caracteristica peculiar de um empenho
     * @return boolean true
     * @throws BusinessException
     */
    protected function salvarVinculoCaracteristicaPeculiar()
    {

        if ($this->sCaracteristicaPeculiar == '') {
            return false;
        }
        $oDaoLancamentoCaracteristica = db_utils::getDao("conlancamconcarpeculiar");
        $oDaoLancamentoCaracteristica->c08_sequencial = null;
        $oDaoLancamentoCaracteristica->c08_codlan = $this->iCodigoLancamento;
        $oDaoLancamentoCaracteristica->c08_concarpeculiar = $this->sCaracteristicaPeculiar;
        $oDaoLancamentoCaracteristica->incluir(null);
        if ($oDaoLancamentoCaracteristica->erro_status == "0") {
            throw new BusinessException("Não foi possível vincular a caracteristica peculiar do empenho.");
        }
        return true;
    }

    /**
     * Define o empenho
     * @param EmpenhoFinanceiro $oEmpenho
     */
    public function setEmpenho(EmpenhoFinanceiro $oEmpenho)
    {
        $this->oEmpenho = $oEmpenho;
    }

    /**
     * Retorna o empenho
     * @return EmpenhoFinanceiro
     */
    public function getEmpenho()
    {
        return $this->oEmpenho;
    }

    /**
     * Retorna a movimentacao Contabil
     * @return MovimentacaoContabil
     */
    public function getMovimentacaoContabil()
    {
        return $this->oMovimentacao;
    }

    /**
     * @param MovimentacaoContabil $oMovimentacao
     */
    public function setMovimentacaoContabil(MovimentacaoContabil $oMovimentacao)
    {
        $this->oMovimentacao = $oMovimentacao;
    }

    /**
     * @return mixed
     */
    public function getContaReferencia()
    {
        return $this->iContaReferencia;
    }

    /**
     * @param mixed $iContaReferencia
     */
    public function setContaReferencia($iContaReferencia)
    {
        $this->iContaReferencia = $iContaReferencia;
    }

    /**
     * Seta valor para a caracteristica peculiar do empenho
     * @param string $sCaracteristicaPeculiar
     */
    public function setCaracteristicaPeculiar($sCaracteristicaPeculiar)
    {
        $this->sCaracteristicaPeculiar = $sCaracteristicaPeculiar;
    }

    /**
     * Seta o Cgm
     * @param integer $iCgm
     */
    public function setCgm($iCgm)
    {
        $this->iCgm = $iCgm;
    }

    /**
     * Retorna o codigo do cgm
     * @return integer $iCgm
     */
    public function getCgm()
    {
        return $this->iCgm;
    }

    /**
     * Seta codigo do elemento
     * @param integer $iCodigoElemento
     */
    public function setElemento($iCodigoElemento)
    {
        parent::setCodigoElemento($iCodigoElemento);
    }

    /**
     * Retorna o codigo do elemento
     * @return integer $iCodigoElemento
     */
    public function getElemento()
    {
        return parent::getCodigoElemento();
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function salvarVinculoReceita()
    {

        if (empty($this->oReceitaContabil)) {
            return false;
        }

        $daoConlancamRec = new cl_conlancamrec();
        $daoConlancamRec->c74_codlan = $this->iCodigoLancamento;
        $daoConlancamRec->c74_anousu = $this->oReceitaContabil->getAno();
        $daoConlancamRec->c74_codrec = $this->oReceitaContabil->getCodigo();
        $daoConlancamRec->c74_data = $this->getDataLancamento();
        $daoConlancamRec->incluir($daoConlancamRec->c74_codlan);
        if ($daoConlancamRec->erro_status === "0") {
            throw new Exception("Não foi possível vincular a receita com o lançamento contábil.");
        }
        return true;
    }

    /**
     * @return int
     */
    public function getContaDebito()
    {
        return $this->contaDebito;
    }

    /**
     * @param int $contaDebito
     */
    public function setContaDebito($contaDebito)
    {
        $this->contaDebito = $contaDebito;
    }

    /**
     * @return int
     */
    public function getContaCredito()
    {
        return $this->contaCredito;
    }

    /**
     * @param int $contaCredito
     */
    public function setContaCredito($contaCredito)
    {
        $this->contaCredito = $contaCredito;
    }

    /**
     * @param integer $iOrdemLancamento
     */
    public function setOrdemLancamento($iOrdemLancamento)
    {
        $this->iOrdemLancamento = $iOrdemLancamento;
    }

    /**
     * @return integer
     */
    public function getOrdemLancamento()
    {
        return $this->iOrdemLancamento;
    }


    public function setReceitaContabil(ReceitaContabil $oReceitaContabil)
    {
        $this->oReceitaContabil = $oReceitaContabil;
    }

    /**
     * @return bool
     */
    public function isInversaoContas()
    {
        return $this->inversaoContas;
    }

    /**
     * @param bool $inversaoContas
     */
    public function setInversaoContas($inversaoContas)
    {
        $this->inversaoContas = $inversaoContas;
    }

    /**
     * @param integer $codigoRecurso
     */
    public function setCodigoRecurso($codigoRecurso)
    {
        $this->codigoRecurso = $codigoRecurso;
    }

    /**
     * @return int
     */
    public function getCodigoRecurso()
    {
        return $this->codigoRecurso;
    }

    /**
     * @return bool|Recurso
     */
    public function getRecurso()
    {
        if (!empty($this->codigoRecurso)) {
            return RecursoRepository::getRecursoPorCodigo($this->codigoRecurso);
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getCodigoDocumento()
    {
        return $this->codigoDocumento;
    }

    /**
     * @param mixed $codigoDocumento
     * @return LancamentoAuxiliarEncerramentoExercicio
     */
    public function setCodigoDocumento($codigoDocumento)
    {
        $this->codigoDocumento = $codigoDocumento;
        return $this;
    }
}
