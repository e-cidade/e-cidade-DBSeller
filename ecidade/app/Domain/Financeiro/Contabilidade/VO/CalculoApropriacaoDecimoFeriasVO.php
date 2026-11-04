<?php

namespace App\Domain\Financeiro\Contabilidade\VO;

class CalculoApropriacaoDecimoFeriasVO
{
    public $lancamento;
    public $documento;
    public $documentoDescricao;
    public $historico;
    public $naturezaCredito;
    public $contaCredito;
    public $codgoRecursoCredito;
    public $codigoContaCredito;
    public $estruturalContaCredito;
    public $descricaoContaCredito;
    public $contaDebito;
    public $codgoContaDebito;
    public $codgoRecursoDebito;

    public $estruturalContaDebito;
    public $descricaoContaDebito;
    public $codigoTabelaPrevidencia;
    public $tabelaPrevidencia;
    public $valorBalanceteVerificacao = 0;
    public $naturezaSaldoBalancete = 0;
    /**
     * @var float
     */
    public $valorLancar = 0;
    /**
     * @var float
     */
    public $valorFolha = 0;
    /**
     * @var float
     */
    public $saldo = 0;

    /**
     * Quando o saldo da conta ficar negativa, deve-se usar o documento de reversão
     *
     * @var bool
     */
    public $usouContaReversao = false;

    public function usarContaReversao(\stdClass $contaRevercao)
    {
        $this->documento = $contaRevercao->c45_coddoc;
        $this->documentoDescricao = $contaRevercao->c53_descr;
        $this->historico = $contaRevercao->c46_codhist;
        $this->naturezaCredito = $contaRevercao->credito_natureza_saldo;
        $this->contaCredito = $contaRevercao->credito_reduzido;
        $this->codigoContaCredito = $contaRevercao->credito_conta;
        $this->codgoRecursoCredito = $contaRevercao->credito_recurso;
        $this->estruturalContaCredito = $contaRevercao->credito_estrutural;
        $this->descricaoContaCredito = $contaRevercao->credito_descricao;
        $this->contaDebito = $contaRevercao->debito_reduzido;
        $this->codgoContaDebito = $contaRevercao->debito_conta;
        $this->codgoRecursoDebito = $contaRevercao->debito_recurso;
        $this->estruturalContaDebito = $contaRevercao->debito_estrutural;
        $this->descricaoContaDebito = $contaRevercao->debito_descricao;

        $this->usouContaReversao = true;
    }
}
