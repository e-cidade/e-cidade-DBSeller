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

require_once(modification("interfaces/ILancamentoAuxiliar.interface.php"));
require_once(modification("model/contabilidade/lancamento/LancamentoAuxiliarBase.model.php"));

/**
 * model para os lançaamentos auxiliares de valores de disponibilidade para abertura de exercício
 * @author rafael.lopes <rafael.lopes@dbseller.com.br>
 * @package contabilidade
 * @subpackage lancamento
 * @version $Revision: 1.0 $
 */
class LancamentoAuxiliarRecursosExercicioAnteriorControles extends LancamentoAuxiliarBase implements ILancamentoAuxiliar
{
    public $iDocumento;
    protected $oRecurso;
    protected $codigoAbertura;
    /**
     * código do recurso
     * @var int
     */
    private $codigoRecurso;

    /**
     * Documento sendo executado
     * @var int
     */
    private $codigoDocumento;

    public function getCodigoAbertura()
    {
        return $this->codigoAbertura;
    }

    public function setCodigoAbertura($abertura)
    {
        $this->codigoAbertura = $abertura;
    }

    public function setDocumento($iDocumento)
    {
        $this->iDocumento = $iDocumento;
    }

    public function getDocumento()
    {
        return $this->iDocumento;
    }

    public function setRecurso(Recurso $oRecurso)
    {
        $this->oRecurso = $oRecurso;
        $this->setCodigoRecurso($oRecurso->getCodigo());
    }

    public function getRecurso()
    {
        return $this->oRecurso;
    }


    /**
     * Complemento para o lan?amento cont?bil
     * @var string
     */
    protected $sComplemento;

    private $contaDebito;

    /**
     * @var integer
     */
    private $contaCredito;

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
     * Dados da tabela conhist
     * @var integer
     */
    private $iHistorico;

    /**
     * Operacoesrealizadastef
     * @var Operacoesrealizadastef
     */
    private $oOperacoesrealizadastef;
    private $iOperacoesrealizadastef;
    /**
     * Vari?vel de controle para sabermos se o lan?amento ? um estorno
     * @var boolean
     */
    protected $lEstorno = false;

    /**
     * Executa os lan?amentos auxiliares dos Movimentos de uma liquidacao
     * @param integer $iCodigoLancamento - C?digo do Lancamento (conlancam)
     * @param string $dtLancamento - data do lancamento
     * @see ILancamentoAuxiliar::executaLancamentoAuxiliar()
     */
    public function executaLancamentoAuxiliar($iCodigoLancamento, $dtLancamento)
    {

        $this->setCodigoLancamento($iCodigoLancamento);
        $this->setDataLancamento($dtLancamento);
        parent::salvarVinculoComplemento();
        $this->criarEstruturasComplementoRecursos($iCodigoLancamento);
        $this->vincularConlancamAbertura($iCodigoLancamento);

        return true;
    }

    public function criarEstruturasComplementoRecursos($iLancamento)
    {

        $oDaoconlancamcomplementorecurso = new cl_conlancamcomplementorecurso;

        $oRecurso = $this->getRecurso();
        $complementoRecurso = $oRecurso->getComplemento();
        $codigoRecurso = $oRecurso->getCodigo();

        $oDaoconlancamcomplementorecurso->o201_codlan = $iLancamento;
        $oDaoconlancamcomplementorecurso->o201_complemento = $complementoRecurso;
        $oDaoconlancamcomplementorecurso->o201_orctiporec = $codigoRecurso;
        $oDaoconlancamcomplementorecurso->incluir(null);
        if ($oDaoconlancamcomplementorecurso->erro_status == "0") {
            throw new \Exception("Erro ao Vincular Complemento: " . $oDaoconlancamcomplementorecurso->erro_msg);
        }

        unset($oDaoconlancamcomplementorecurso);
    }

    protected function vincularConlancamAbertura($iCodigoLancamento)
    {
        $oDao = new \cl_conlancamaberturaexercicioorcamento;
        $oDao->c105_codlan = $iCodigoLancamento;
        $oDao->c105_aberturaexercicioorcamento = $this->getCodigoAbertura();
        $oDao->incluir(null);
        if ($oDao->erro_status == "0") {
            throw new \Exception("Erro ao Vincular Abertura com Lancamento: " . $oDao->erro_msg);
        }
        unset($oDao);
    }

    public function seDataLancamento($dtLancamento)
    {
        $this->dtLancamento = $dtLancamento;
    }

    public function getDataLancamento()
    {
        return $this->dtLancamento;
    }


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
     * Seta valor para o complemento do lan?amento cont?bil
     * @see LancamentoAuxiliarBase::setObservacaoHistorico()
     */
    public function setObservacaoHistorico($sObservacaoHistorico)
    {
        $this->sComplemento = $sObservacaoHistorico;
    }

    /**
     * Retorna o complemento do lan?amento cont?bil
     * @see LancamentoAuxiliarBase::getObservacaoHistorico()
     */
    public function getObservacaoHistorico()
    {
        return $this->sComplemento;
    }

    /**
     * Seta se o lan?amento ? um estorno
     * @param boolean $lEstorno
     */
    public function setEstorno($lEstorno)
    {

        $this->lEstorno = $lEstorno;
    }

    /**
     * Retorna se o lan?amento ? um estorno
     * @return boolean
     */
    public function isEstorno()
    {
        return $this->lEstorno;
    }

    /**
     * @return int
     */
    public function getCodigoDocumento()
    {
        return $this->codigoDocumento;
    }

    /**
     * @param int $codigoDocumento
     * @return LancamentoAuxiliarEncerramentoExercicio
     */
    public function setCodigoDocumento($codigoDocumento)
    {
        $this->codigoDocumento = $codigoDocumento;
        return $this;
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
}
