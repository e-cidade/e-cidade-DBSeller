<?php

use App\Domain\Financeiro\Orcamento\Models\FonteReceita;

require_once(modification("interfaces/ILancamentoAuxiliar.interface.php"));
require_once(modification("model/contabilidade/lancamento/LancamentoAuxiliarBase.model.php"));

class LancamentoAuxiliarLancamentoManual extends LancamentoAuxiliarBase implements ILancamentoAuxiliar
{

    /**
     * Dados da tabela conhist
     * @var integer
     */
    private $historico;
    /**
     * Empenho com restos a liquidar
     * @var EmpenhoFinanceiro
     */
    private $empenho;
    /**
     * @var Dotacao
     */
    private $dotacao;

    /**
     * @var ReceitaContabil
     */
    private $receita;
    /**
     * @var integer
     */
    private $cgm;

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
     * @var Recurso
     */
    private $recursoDebito;
    /**
     * Código do recurso orçamentário
     * @var Recurso
     */
    private $recursoCredito;
    /**
     * @var integer
     */
    private $codigoDocumento;
    /**
     * @var integer
     */
    private $exercicio;
    /**
     * @var integer
     */
    private $idLote;

    private $isDespesaReceita = false;

    /**
     * Criado propriedade para mapear dados essenciais para emissão da matriz quando não é possível criar um lançamento
     * manual, vinculando a um empenho / receita
     * @var array
     */
    private $dadosAjusteSaldo = [];

    /**
     * @throws BusinessException
     */
    public function executaLancamentoAuxiliar($codigoLancamento, $dtLancamento)
    {
        parent::setCodigoLancamento($codigoLancamento);
        parent::setDataLancamento($dtLancamento);

        if (!is_null($this->empenho)) {
            $this->salvarVinculoEmpenho();
            $this->salvarVinculoElemento();
        }

        if (!is_null($this->dotacao)) {
            $this->salvarVinculoDotacao();
        }

        if (!is_null($this->receita)) {
            $this->salvarVinculoReceita();
        }

        if (!is_null($this->cgm)) {
            $this->salvarVinculoCgm();
        }

        if (!empty($this->dadosAjusteSaldo)) {
            $this->salvarAjusteSaldo();
        }

        $this->salvarVinculoComplemento();

        $this->vincularLote();
        return true;
    }


    /**
     * Define o empenho
     * @param EmpenhoFinanceiro $empenho
     * @throws Exception
     */
    public function setEmpenho(EmpenhoFinanceiro $empenho)
    {
        $this->empenho = $empenho;
        $this->iNumeroEmpenho = $empenho->getNumero();
        $this->cgm = $empenho->getCgm()->getCodigo();
        $this->iCodigoElemento = $empenho->getDesdobramentoEmpenho();
        $this->dotacao = $empenho->getDotacao();

        $std = $empenho->getRecursoEmpenho();
        $recurso = \RecursoRepository::getRecursoPorCodigo($std->o15_codigo);
        $this->setRecursoCredito($recurso);
        $this->setRecursoDebito($recurso);

        $this->isDespesaReceita = true;
    }

    /**
     * Retorna o empenho
     * @return EmpenhoFinanceiro
     */
    public function getEmpenho()
    {
        return $this->empenho;
    }

    /**
     * @return Dotacao
     */
    public function getDotacao()
    {
        return $this->dotacao;
    }

    /**
     * @param Dotacao $dotacao
     * @return LancamentoAuxiliarLancamentoManual
     */
    public function setDotacao(Dotacao $dotacao)
    {
        $this->dotacao = $dotacao;
        $this->isDespesaReceita = true;

        $recurso = \RecursoRepository::getRecursoPorCodigo($dotacao->getRecurso());
        $this->setRecursoCredito($recurso);
        $this->setRecursoDebito($recurso);
        return $this;
    }

    /**
     * @return ReceitaContabil
     */
    public function getReceita()
    {
        return $this->receita;
    }

    /**
     * @param mixed $receita
     * @return LancamentoAuxiliarLancamentoManual
     */
    public function setReceita(ReceitaContabil $receita)
    {
        $this->receita = $receita;
        $this->recursoCredito = $receita->getRecurso();
        $this->recursoDebito = $receita->getRecurso();
        $this->isDespesaReceita = true;
        return $this;
    }

    /**
     * @return int
     */
    public function getCgm()
    {
        return $this->cgm;
    }

    /**
     * @param int $cgm
     * @return LancamentoAuxiliarLancamentoManual
     */
    public function setCgm($cgm)
    {
        $this->cgm = $cgm;
        return $this;
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
     * @return LancamentoAuxiliarLancamentoManual
     */
    public function setContaDebito($contaDebito)
    {
        $this->contaDebito = $contaDebito;
        return $this;
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
     * @return LancamentoAuxiliarLancamentoManual
     */
    public function setContaCredito($contaCredito)
    {
        $this->contaCredito = $contaCredito;
        return $this;
    }

    /**
     * @return Recurso
     */
    public function getRecursoDebito()
    {
        return $this->recursoDebito;
    }

    /**
     * @param int $codigoRecursoDebito
     * @return LancamentoAuxiliarLancamentoManual
     */
    public function setRecursoDebito($codigoRecursoDebito)
    {
        $this->recursoDebito = $codigoRecursoDebito;
        return $this;
    }

    /**
     * @return Recurso
     */
    public function getRecursoCredito()
    {
        return $this->recursoCredito;
    }

    /**
     * @param mixed $codigoRecursoCredito
     * @return LancamentoAuxiliarLancamentoManual
     */
    public function setRecursoCredito($codigoRecursoCredito)
    {
        $this->recursoCredito = $codigoRecursoCredito;
        return $this;
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
     * @return LancamentoAuxiliarLancamentoManual
     */
    public function setCodigoDocumento($codigoDocumento)
    {
        $this->codigoDocumento = $codigoDocumento;
        return $this;
    }

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
        return $this->historico;
    }

    /**
     * Seta o histórico da operação
     * @param integer $iHistorico
     */
    public function setHistorico($iHistorico)
    {
        $this->historico = $iHistorico;
    }

    /**
     * Retorna a observação do histórico da operação
     */
    public function getObservacaoHistorico()
    {
        return $this->observacao;
    }

    /**
     * Seta a observação do histórico da operação
     * @param string $sObservacaoHistorico
     */
    public function setObservacaoHistorico($sObservacaoHistorico)
    {
        $this->observacao = $sObservacaoHistorico;
    }

    public function isDespesaReceita()
    {
        return $this->isDespesaReceita;
    }

    public function setCodigoLote($idLote)
    {
        $this->idLote = $idLote;
    }

    public function setExercício($exercicio)
    {
        $this->exercicio = $exercicio;
    }

    protected function salvarVinculoDotacao()
    {
        if (empty($this->dotacao)) {
            return false;
        }
        $oDaoConLanCamDot = new cl_conlancamdot();
        $oDaoConLanCamDot->c73_codlan = $this->iCodigoLancamento;
        $oDaoConLanCamDot->c73_data = $this->dtLancamento;
        $oDaoConLanCamDot->c73_anousu = $this->dotacao->getAno();
        $oDaoConLanCamDot->c73_coddot = $this->dotacao->getCodigo();
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
     * @return bool
     * @throws Exception
     */
    private function salvarVinculoReceita()
    {
        $daoConlancamRec = new cl_conlancamrec();
        $daoConlancamRec->c74_codlan = $this->getCodigoLancamento();
        $daoConlancamRec->c74_anousu = $this->receita->getAno();
        $daoConlancamRec->c74_codrec = $this->receita->getCodigo();
        $daoConlancamRec->c74_data = $this->getDataLancamento();
        $daoConlancamRec->incluir($daoConlancamRec->c74_codlan);
        if ($daoConlancamRec->erro_status === "0") {
            throw new Exception("Não foi possível vincular a receita com o lançamento contábil.");
        }
        return true;
    }

    protected function salvarVinculoCgm()
    {
        $oDaoConLanCamCGM = new cl_conlancamcgm();
        $oDaoConLanCamCGM->c76_codlan = $this->getCodigoLancamento();
        $oDaoConLanCamCGM->c76_numcgm = $this->cgm;
        $oDaoConLanCamCGM->c76_data = $this->dtLancamento;
        $oDaoConLanCamCGM->incluir($this->iCodigoLancamento);

        if ($oDaoConLanCamCGM->erro_status == 0) {
            $sErroMsg = "Não foi possível incluir o CGM do lançamento.\n\n";
            $sErroMsg .= "Erro Técnico : {$oDaoConLanCamCGM->erro_msg}";
            throw new BusinessException($sErroMsg);
        }

        unset($oDaoConLanCamCGM);
    }

    private function vincularLote()
    {
        $dao = new cl_lotelancamentoconlancam();
        $dao->c161_conlancam = $this->getCodigoLancamento();
        $dao->c161_lotelancamento = $this->idLote;
        $dao->incluir(null);

        if ($dao->erro_status == 0) {
            $msg = "Erro ao vincular lançamento ao Lote.\n{$dao->erro_status}";
            throw new \Exception($msg);
        }
    }

    public function setDadosAjusteSaldo(array $dadosAjusteSaldo)
    {
        $this->dadosAjusteSaldo = $dadosAjusteSaldo;
    }

    private function salvarAjusteSaldo()
    {
        $funcao = $this->dadosAjusteSaldo['funcao'];
        $subfuncao = $this->dadosAjusteSaldo['subfuncao'];
        $elemento = $this->dadosAjusteSaldo['elemento'];
        $ai = $this->dadosAjusteSaldo['ai'];
        $nr = $this->dadosAjusteSaldo['nr'];

        $codele = null;
        if (!empty($elemento)) {
            $elemento = \App\Domain\Financeiro\Orcamento\Models\Elemento::query()
                ->where('o56_anousu', $this->exercicio)
                ->where('o56_elemento', $elemento)
                ->first();
            $codele = $elemento->o56_codele;
        }

        if (!empty($nr)) {
            $fonte = FonteReceita::query()
                ->select('o57_codfon')
                ->where("o57_anousu", $this->exercicio)
                ->where("o57_fonte", $nr)
                ->first();
            $nr = $fonte->o57_codfon;
        }

        $dao = new cl_conlancamajustesaldoconta();
        $dao->c155_conlancam = $this->getCodigoLancamento();
        $dao->c155_exercicio = $this->exercicio;
        $dao->c155_funcao = $funcao;
        $dao->c155_subfuncao = $subfuncao;
        $dao->c155_elemento = $codele;
        $dao->c155_ai = $ai;
        $dao->c155_receita = $nr;

        $dao->incluir(null);
    }
}
