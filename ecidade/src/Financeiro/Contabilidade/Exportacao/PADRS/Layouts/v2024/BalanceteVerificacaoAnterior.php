<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2024;

use ECidade\Core\Mappers\ParseArray;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;

class BalanceteVerificacaoAnterior extends ParseArray implements LayoutPad
{
    protected $dePara = [
        "Código da Conta do Bal. Verificação - SG" => "estrutural",
        "Código do Órgão + Unidade Orçamentária" => "orgao_unidade",
        "Saldo Anterior - Conta Devedora" => "saldo_anterior_debito",
        "Saldo Anterior - Conta Credora" => "saldo_anterior_credito",
        "Movimentação - Conta Débito" => "saldo_debito",
        "Movimentação - Conta Crédito" => "saldo_credito",
        "Saldo Atual - Conta Devedora" => "saldo_final_debito",
        "Saldo Atual - Conta Credora" => "saldo_final_credito",
        "Especificação Conta do Bal. Verificação - SG" => "nome",
        "Tipo de Nível da Conta" => "tipo",
        "Número do Nível da Conta" => "nivel",
        "Campo Obsoleto" => "obsoleto1",
        "Escrituração" => "escrituracao",
        "Natureza da Informação" => "natureza_informacao",
        "Indicador de Superávit Financeiro" => "indicador_superavit"
    ];

    protected $estrutural;
    protected $orgaoUnidade;
    protected $saldoAnteriorDebito;
    protected $saldoAnteriorCredito;
    protected $saldoDebito;
    protected $saldoCredito;
    protected $saldoFinalDebito;
    protected $saldoFinalCredito;
    protected $nome;
    protected $tipo;
    protected $nivel;
    protected $obsoleto1 = ' ';
    protected $escrituracao;
    protected $naturezaInformacao;
    protected $indicadorSuperavit;

    /**
     * @return mixed
     */
    public function getEstrutural()
    {
        return $this->estrutural;
    }

    /**
     * @param mixed $estrutural
     */
    public function setEstrutural($estrutural)
    {
        $this->estrutural = $estrutural;
    }

    /**
     * @return mixed
     */
    public function getOrgaoUnidade()
    {
        return $this->orgaoUnidade;
    }

    /**
     * @param mixed $orgaoUnidade
     */
    public function setOrgaoUnidade($orgaoUnidade)
    {
        $this->orgaoUnidade = $orgaoUnidade;
    }

    /**
     * @return mixed
     */
    public function getSaldoAnteriorDebito()
    {
        return $this->saldoAnteriorDebito;
    }

    /**
     * @param mixed $saldoAnteriorDebito
     */
    public function setSaldoAnteriorDebito($saldoAnteriorDebito)
    {
        $this->saldoAnteriorDebito = $saldoAnteriorDebito;
    }

    /**
     * @return mixed
     */
    public function getSaldoAnteriorCredito()
    {
        return $this->saldoAnteriorCredito;
    }

    /**
     * @param mixed $saldoAnteriorCredito
     */
    public function setSaldoAnteriorCredito($saldoAnteriorCredito)
    {
        $this->saldoAnteriorCredito = $saldoAnteriorCredito;
    }

    /**
     * @return mixed
     */
    public function getSaldoDebito()
    {
        return $this->saldoDebito;
    }

    /**
     * @param mixed $saldoDebito
     */
    public function setSaldoDebito($saldoDebito)
    {
        $this->saldoDebito = $saldoDebito;
    }

    /**
     * @return mixed
     */
    public function getSaldoCredito()
    {
        return $this->saldoCredito;
    }

    /**
     * @param mixed $saldoCredito
     */
    public function setSaldoCredito($saldoCredito)
    {
        $this->saldoCredito = $saldoCredito;
    }

    /**
     * @return mixed
     */
    public function getSaldoFinalDebito()
    {
        return $this->saldoFinalDebito;
    }

    /**
     * @param mixed $saldoFinalDebito
     */
    public function setSaldoFinalDebito($saldoFinalDebito)
    {
        $this->saldoFinalDebito = $saldoFinalDebito;
    }

    /**
     * @return mixed
     */
    public function getSaldoFinalCredito()
    {
        return $this->saldoFinalCredito;
    }

    /**
     * @param mixed $saldoFinalCredito
     */
    public function setSaldoFinalCredito($saldoFinalCredito)
    {
        $this->saldoFinalCredito = $saldoFinalCredito;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Tipo: S - Sintética | A - Analítica
     * Escrituração: S - Sim, conta escriturável | N - Não, conta não escriturável
     *
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        $this->escrituracao = $tipo === 'S' ? 'N' : 'S';
    }

    /**
     * @return mixed
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * @param mixed $nivel
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;
    }

    /**
     * @return string
     */
    public function getObsoleto1()
    {
        return $this->obsoleto1;
    }

    /**
     * @param string $obsoleto1
     */
    public function setObsoleto1($obsoleto1)
    {
        $this->obsoleto1 = $obsoleto1;
    }

    /**
     * @return mixed
     */
    public function getEscrituracao()
    {
        return $this->escrituracao;
    }

    /**
     * @param mixed $escrituracao
     */
    public function setEscrituracao($escrituracao)
    {
        $this->escrituracao = $escrituracao;
    }

    /**
     * @return mixed
     */
    public function getNaturezaInformacao()
    {
        return $this->naturezaInformacao;
    }

    /**
     * @param mixed $naturezaInformacao
     */
    public function setNaturezaInformacao($naturezaInformacao)
    {
        $this->naturezaInformacao = $naturezaInformacao;
    }

    /**
     * @return mixed
     */
    public function getIndicadorSuperavit()
    {
        return $this->indicadorSuperavit;
    }

    /**
     * @param mixed $indicadorSuperavit
     */
    public function setIndicadorSuperavit($indicadorSuperavit)
    {
        $this->indicadorSuperavit = $indicadorSuperavit;
    }

    public function toArray()
    {
        return [
            "estrutural" => $this->getEstrutural(),
            "orgao_unidade" => $this->getOrgaoUnidade(),
            "saldo_anterior_debito" => $this->getSaldoAnteriorDebito(),
            "saldo_anterior_credito" => $this->getSaldoAnteriorCredito(),
            "saldo_debito" => $this->getSaldoDebito(),
            "saldo_credito" => $this->getSaldoCredito(),
            "saldo_final_debito" => $this->getSaldoFinalDebito(),
            "saldo_final_credito" => $this->getSaldoFinalCredito(),
            "nome" => $this->getNome(),
            "tipo" => $this->getTipo(),
            "nivel" => $this->getNivel(),
            "obsoleto1" => $this->getObsoleto1(),
            "escrituracao" => $this->getEscrituracao(),
            "natureza_informacao" => $this->getNaturezaInformacao(),
            "indicador_superavit" => $this->getIndicadorSuperavit(),
        ];
    }
}
