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

use ECidade\Financeiro\Orcamento\Recurso\Origem;

require_once(modification("interfaces/ILancamentoAuxiliar.interface.php"));
require_once(modification("model/contabilidade/lancamento/LancamentoAuxiliarBase.model.php"));

/**
 * Model reponsavel por realizar os lancamentos auxiliares para um empenho
 * @author     Matheus Felini <matheus.felini@dbseller.com.br>
 * @package    contabilidade
 * @subpackage lancamento
 * @version    $Revision: 1.4 $
 */
class LancamentoAuxiliarApropriacaoRetencao extends LancamentoAuxiliarBase implements ILancamentoAuxiliar
{

    /**
     * Valor total do Lancamento
     * @var float
     */
    private $nValorTotal;

    /**
     * Codigo do historico
     * @var integer
     */
    private $iCodigoHistorico;

    /**
     * Observacao / Complemento
     * @var string
     */
    private $sObservacaoHistorico;

    /**
     * Conta Credito
     * @var integer
     */
    private $iContaCredito;

    /**
     * Conta Debito
     * @var integer
     */
    private $iContaDebito;

    /**
     * Informa que o empenho eh um empenho de prestacao de contas.
     * @var boolean
     */
    private $isPrestacaoContas = false;

    /**
     * Código da Retenção involvida
     * @var integer
     */
    private $retencao;

    /**
     * sequencial do código do empenho
     * @var integer
     */
    private $iEmpenho;

    /**
     * sequencial do numero do cgm
     * @var integer
     */
    private $iCgm;

    /**
     * Codigo do recurso
     * @var integer
     */
    private $iCodigoRecurso;

    /**
     * codigo do contrato
     * @var integer
     */
    private $iCodigoContrato;

    /**
     * objeto empenho financeiro
     * @var object empenhoFinanceiro
     */
    protected $oEmpenhoFinanceiro;

    /**
     * Código da Ordem de Pagamento
     * @var integer
     */
    private $iCodigoOrdemPagamento;

    private $tipoCalculoRetencao;

    private $estorno = false;

    /**
     * @var integer
     */
    protected $codigoReduzido;

    /**
     * @var string
     */
    private $caracteristicaPeculiar = '000';


    /**
     * Executa o Lancamento auxiliar
     * @param int $iCodigoLancamento Código do lancamento contabil
     * @param date $dtLancamento data do Lancamento
     * @return bool
     * @throws BusinessException
     */
    public function executaLancamentoAuxiliar($iCodigoLancamento, $dtLancamento)
    {

        parent::setCodigoLancamento($iCodigoLancamento);
        parent::setDataLancamento($dtLancamento);
        parent::salvarVinculoComplemento();
        parent::salvarVinculoCgm();
        parent::salvarVinculoEmpenho();
        if ($this->getElemento() != '') {
            parent::salvarVinculoElemento();
        }
        if ($this->getDotacao() != '') {
            parent::salvarVinculoDotacao();
        }

        if (!empty($this->iCodigoOrdemPagamento)) {
            $this->salvarVinculoOrdemDePagamento();
        }

        $this->salvarVinculoRetencao();
        return true;
    }

    public function setTipoCalculoRetencao($tipoCalculo)
    {
        $this->tipoCalculoRetencao = $tipoCalculo;
    }

    /**
     * @return mixed
     */
    public function getTipoCalculoRetencao()
    {
        return $this->tipoCalculoRetencao;
    }


    /**
     * @deprecated a partir da tarefa M27557, centralizamos a persistência do Recurso na classe LancamentoContabil
     *             namespce ECidade\Financeiro\Contabilidade\LancamentoContabil
     */
    protected function salvarVinculoRecurso()
    {
        $oDaoConlancamrecurso = new cl_conlancamrecurso();
        $oDaoConlancamval = new cl_conlancamval();
        $iLancamento = $this->iCodigoLancamento;
        $iRecurso = $this->getEmpenhoFinanceiro()->getDotacao()->getDadosRecurso()->getCodigo();
        $iAnousu = db_getsession("DB_anousu");

        $recurso = Origem::getEmpenho($this->getEmpenhoFinanceiro()->getNumero(), $iAnousu);

        if ($recurso) {
            $iRecurso = $recurso->o206_recurso;
        }

        $sWhere = "c69_codlan = {$iLancamento}";
        $sql = $oDaoConlancamval->sql_query_file(null, "*", "c69_ordem", $sWhere);
        $rsLancamentos = $oDaoConlancamval->sql_record($sql);

        if ($oDaoConlancamval->numrows <= 0) {
            throw new BusinessException("Não foi possível encontrar conta débito e credito do lancamento.");
        }

        $aCreditos = array();
        $aDebitos = array();

        // prepara os dados necessario para conlancamrecurso
        for ($i = 0; $i < $oDaoConlancamval->numrows; $i++) {

            $oDados = db_utils::fieldsMemory($rsLancamentos, $i);
            $oDebito = new stdClass();
            $oCredito = new stdClass();

            $oDebito->lancamento = $iLancamento;
            $oDebito->recurso = $iRecurso;
            $oDebito->conta = $oDados->c69_debito;
            $oDebito->ano = $iAnousu;
            $oDebito->natureza = "D";

            $oCredito->lancamento = $iLancamento;
            $oCredito->recurso = $iRecurso;
            $oCredito->conta = $oDados->c69_credito;
            $oCredito->ano = $iAnousu;
            $oCredito->natureza = "C";

            $aCreditos[][] = $oCredito;
            $aDebitos[][] = $oDebito;
        }

        $iIncrementoRegistros = 0;
        foreach ($aCreditos as $aCredito) {

            $iOrdem = 0;
            foreach ($aCredito as $oCredito) {

                // inclui credito
                $oDaoConlancamrecurso->c130_conlancam = $oCredito->lancamento;
                $oDaoConlancamrecurso->c130_orctiporec = $oCredito->recurso;
                $oDaoConlancamrecurso->c130_conta = $oCredito->conta;
                $oDaoConlancamrecurso->c130_anousu = $oCredito->ano;
                $oDaoConlancamrecurso->c130_natureza = $oCredito->natureza;
                $oDaoConlancamrecurso->c130_sequencial = null;

                $oDaoConlancamrecurso->incluir(null);
                if ($oDaoConlancamrecurso->erro_status == "0") {

                    $msgm = "Erro ao Vincular o Lancamento (Credito) ao Recurso: " . $oDaoConlancamrecurso->erro_msg;
                    throw new DBException($msgm);
                }

                //inclui debito
                $oDebito = $aDebitos[$iIncrementoRegistros][$iOrdem];

                $oDaoConlancamrecurso->c130_conlancam = $oDebito->lancamento;
                $oDaoConlancamrecurso->c130_orctiporec = $oDebito->recurso;
                $oDaoConlancamrecurso->c130_conta = $oDebito->conta;
                $oDaoConlancamrecurso->c130_anousu = $oDebito->ano;
                $oDaoConlancamrecurso->c130_natureza = $oDebito->natureza;
                $oDaoConlancamrecurso->c130_sequencial = null;

                $oDaoConlancamrecurso->incluir(null);
                if ($oDaoConlancamrecurso->erro_status == "0") {

                    $msgm = "Erro ao Vincular o Lancamento (Debito)ao Recurso: " . $oDaoConlancamrecurso->erro_msg;
                    throw new DBException($msgm);
                }
                $iOrdem++;
            }
            $iIncrementoRegistros++;
        }
    }


    /**
     * Vincula a caracteristica peculiar de um empenho
     * @return boolean true
     * @throws BusinessException
     */
    protected function salvarVinculoRetencao()
    {

        $daoConlancamRetencao = new cl_conlancamretencao;
        $daoConlancamRetencao->c127_sequencial = null;
        $daoConlancamRetencao->c127_conlancam = $this->iCodigoLancamento;
        $daoConlancamRetencao->c127_retencaotiporec = $this->retencao;
        $daoConlancamRetencao->incluir(null);
        if ($daoConlancamRetencao->erro_status == "0") {
            throw new BusinessException("Não foi possível vincular a retenção do empenho ao lançamento");
        }
        return true;
    }

    /**
     * Vincula o lançamento com uma ordem de pagamento
     *
     * @return boolean - true em caso de sucesso
     * @throws BusinessException
     */
    protected function salvarVinculoOrdemDePagamento()
    {

        $oDaoLancamentoOrdemPagamento = db_utils::getDao('conlancamord');
        $oDaoLancamentoOrdemPagamento->c80_codlan = $this->iCodigoLancamento;
        $oDaoLancamentoOrdemPagamento->c80_codord = $this->iCodigoOrdemPagamento;
        $oDaoLancamentoOrdemPagamento->c80_data = $this->dtLancamento;
        $oDaoLancamentoOrdemPagamento->incluir($this->iCodigoLancamento);
        if ($oDaoLancamentoOrdemPagamento->erro_status == "0") {
            throw new BusinessException("Não foi possível vincular o lançamento com a ordem de pagamento.");
        }
        return true;
    }


    /**
     * Seta o valor total do evento
     * @param float $nValorTotal
     */
    public function setValorTotal($nValorTotal)
    {
        $this->nValorTotal = $nValorTotal;
    }

    /**
     * Retorna o valor total
     * @return float $nValorTotal
     */
    public function getValorTotal()
    {
        return $this->nValorTotal;
    }

    /**
     * Retorna o histórico da operação
     */
    public function getHistorico()
    {
        return $this->iCodigoHistorico;
    }

    /**
     * Seta o histórico da operação
     * @param integer $iHistorico
     */
    public function setHistorico($iHistorico)
    {
        $this->iCodigoHistorico = $iHistorico;
    }

    /**
     * Retorna a observação do histórico da operação
     */
    public function getObservacaoHistorico()
    {
        return $this->sObservacaoHistorico;
    }

    /**
     * Seta a observação do histórico da operação
     * @param string $sObservacaoHistorico
     */
    public function setObservacaoHistorico($sObservacaoHistorico)
    {
        $this->sObservacaoHistorico = $sObservacaoHistorico;
    }

    /**
     * Seta a conta credito
     * @param integer $iContaCredito
     */
    public function setContaCredito($iContaCredito)
    {
        $this->iContaCredito = $iContaCredito;
    }

    /**
     * Retorna a conta credito
     * @return integer
     */
    public function getContaCredito()
    {
        return $this->iContaCredito;
    }

    /**
     * Seta a conta debito
     * @param integer $iContaDebito
     */
    public function setContaDebito($iContaDebito)
    {
        $this->iContaDebito = $iContaDebito;
    }

    /**
     * Retorna a conta debito
     * @return integer
     */
    public function getContaDebito()
    {
        return $this->iContaDebito;
    }

    /**
     * Seta o tipo de empenho como prestacao de contas
     * @param boolean
     */
    public function setPrestacaoContas($lPrestacao)
    {

        $this->isPrestacaoContas = $lPrestacao;
    }

    /**
     * Seta o valor Empenho
     * @param integer $iEmpenho
     */
    public function setEmpenho($iEmpenho)
    {
        $this->iEmpenho = $iEmpenho;
    }

    /**
     * Retorna o $iEmpenho
     * @return integer $iEmpenho
     */
    public function getEmpenho()
    {
        return $this->iEmpenho;
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
     * Seta o recurso do lancamento
     * Utilizado para realizar a criacao da conta-corrente
     * @param integer $iRecurso
     */
    public function setCodigoRecurso($iRecurso)
    {
        $this->iCodigoRecurso = $iRecurso;
    }

    /**
     * Retorna o Recurso
     * @return integer codigo do recurso
     */
    public function getCodigoRecurso()
    {
        return $this->iCodigoRecurso;
    }


    /**
     * Define o codigo do Contrato
     * @param integer $iAcordo Codigo do contrato
     */
    public function setAcordo($iAcordo)
    {
        $this->iCodigoContrato = $iAcordo;
    }

    /**
     * Retorna codigo do Contrato
     *
     * @param integer $iAcordo Código do contrato
     * @return int
     */
    public function getAcordo()
    {
        return $this->iCodigoContrato;
    }

    /**
     * Seta o empenho financeiro
     * @param EmpenhoFinanceiro $oEmpenhoFinanceiro
     */
    public function setEmpenhoFinanceiro(EmpenhoFinanceiro $oEmpenhoFinanceiro)
    {
        $this->oEmpenhoFinanceiro = $oEmpenhoFinanceiro;
    }

    /**
     * Retorna o empenho financeiro
     * @return EmpenhoFinanceiro
     * @throws BusinessException
     */
    public function getEmpenhoFinanceiro()
    {

        if (!empty($this->iNumeroEmpenho)) {
            $this->oEmpenhoFinanceiro = new EmpenhoFinanceiro($this->iNumeroEmpenho);
        }
        return $this->oEmpenhoFinanceiro;
    }

    /**
     * Seta o código da Ordem de Pagamento
     * @param integer $iCodigoOrdemPagamento Código da Ordem
     */
    public function setCodigoOrdemPagamento($iCodigoOrdemPagamento)
    {
        $this->iCodigoOrdemPagamento = $iCodigoOrdemPagamento;
    }

    /**
     * @return int
     */
    public function getRetencao()
    {
        return $this->retencao;
    }

    /**
     * @param int $retencao
     */
    public function setRetencao($retencao)
    {
        $this->retencao = $retencao;
    }


    /**
     * Função da classe que constroi uma instância de LancamentoAuxiliarEmpenho,
     * de acordo com código do lançamento, passado como parâmetro
     *
     * @param integer $iCodigoLancamento
     * @return LancamentoAuxiliarApropriacaoRetencao
     * @throws Exception
     * @throws BusinessException
     */
    public static function getInstance($iCodigoLancamento)
    {

        $oDaoConlancamEmp = db_utils::getDao("conlancamemp");
        $sSql = $oDaoConlancamEmp->sql_query_dadoslancamento(null, "*", null, "c70_codlan = {$iCodigoLancamento}");
        $rsEmpenho = $oDaoConlancamEmp->sql_record($sSql);

        if ($oDaoConlancamEmp->numrows == 0) {
            throw new BusinessException("Vinculo do lançamento {$iCodigoLancamento} com o suprimento não encontrado.");
        }

        $oDadosEmpenho = db_utils::fieldsMemory($rsEmpenho, 0);

        $oLancamentoAuxiliar = new LancamentoAuxiliarApropriacaoRetencao();
        $oLancamentoAuxiliar->setCodigoLancamento($iCodigoLancamento);
        $oLancamentoAuxiliar->setDataLancamento($oDadosEmpenho->c70_data);


        $sObservacao = $oDadosEmpenho->c72_complem;

        if (empty($oDadosEmpenho->c72_complem)) {
            $sObservacao = 'Lançamento de reprocessamento';
        }

        $oEmpenho = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($oDadosEmpenho->e60_numemp);

        $oLancamentoAuxiliar->setObservacaoHistorico($sObservacao);

        $oLancamentoAuxiliar->setFavorecido($oDadosEmpenho->c76_numcgm);
        $oLancamentoAuxiliar->setCaracteristicaPeculiar($oDadosEmpenho->e60_concarpeculiar);
        $oLancamentoAuxiliar->setNumeroEmpenho($oDadosEmpenho->e60_numemp);
        $oLancamentoAuxiliar->setValorTotal($oDadosEmpenho->c70_valor);
        $oLancamentoAuxiliar->setElemento($oDadosEmpenho->c67_codele);
        $oLancamentoAuxiliar->setEmpenhoFinanceiro($oEmpenho);

        /**
         * Dados para conta corrente credor
         */
        $oContaCorrenteDetalhe = new ContaCorrenteDetalhe();
        $oContaCorrenteDetalhe->setCredor(CgmFactory::getInstanceByCgm($oDadosEmpenho->c76_numcgm));
        $oContaCorrenteDetalhe->setEmpenho($oEmpenho);
        $oContaCorrenteDetalhe->setDotacao($oEmpenho->getDotacao());
        $oContaCorrenteDetalhe->setRecurso($oEmpenho->getDotacao()->getDadosRecurso());
        $oLancamentoAuxiliar->setContaCorrenteDetalhe($oContaCorrenteDetalhe);

        return $oLancamentoAuxiliar;
    }

    /**
     * @return bool
     */
    public function isEstorno()
    {
        return $this->estorno;
    }

    /**
     * @param bool $estorno
     */
    public function setEstorno($estorno)
    {
        $this->estorno = $estorno;
    }

    private function setCaracteristicaPeculiar($concarpeculiar)
    {
        $this->caracteristicaPeculiar = $concarpeculiar;
    }

    /**
     * @return int
     */
    public function getCodigoReduzido()
    {
        return $this->codigoReduzido;
    }

    /**
     * @param int $codigoReduzido
     */
    public function setCodigoReduzido($codigoReduzido)
    {
        $this->codigoReduzido = $codigoReduzido;
    }


}
