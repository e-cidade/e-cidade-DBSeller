<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023;

use ECidade\Core\Mappers\ParseArray;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;

class BalanceteVerificacao extends ParseArray implements LayoutPad
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
        "Indicador de Superávit Financeiro" => "indicador_superavit",
        "Código do Recurso Vinculado" => "subrecurso",
        "Complemento do Recurso Vinculado" => "complementoAntigo",
        "Código da Fonte de Recurso" => "siconfi",
        "Código de Acompanhamento da Execução Orçamentária - CO" => "complemento",
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
    protected $subrecurso;
    protected $complementoAntigo;
    protected $siconfi;
    protected $complemento;

    /**
     * @return string
     */
    public function getEstrutural()
    {
        return $this->estrutural;
    }

    /**
     * @param string $estrutural
     */
    public function setEstrutural($estrutural)
    {
        $this->estrutural = $estrutural;
    }

    /**
     * @return string
     */
    public function getOrgaoUnidade()
    {
        return $this->orgaoUnidade;
    }

    /**
     * @param string $orgaoUnidade
     */
    public function setOrgaoUnidade($orgaoUnidade)
    {
        $this->orgaoUnidade = $orgaoUnidade;
    }

    /**
     * @return string
     */
    public function getSaldoAnteriorDebito()
    {
        return $this->saldoAnteriorDebito;
    }

    /**
     * @param string $saldoAnteriorDebito
     */
    public function setSaldoAnteriorDebito($saldoAnteriorDebito)
    {
        $this->saldoAnteriorDebito = $saldoAnteriorDebito;
    }

    /**
     * @return string
     */
    public function getSaldoAnteriorCredito()
    {
        return $this->saldoAnteriorCredito;
    }

    /**
     * @param string $saldoAnteriorCredito
     */
    public function setSaldoAnteriorCredito($saldoAnteriorCredito)
    {
        $this->saldoAnteriorCredito = $saldoAnteriorCredito;
    }

    /**
     * @return string
     */
    public function getSaldoDebito()
    {
        return $this->saldoDebito;
    }

    /**
     * @param string $saldoDebito
     */
    public function setSaldoDebito($saldoDebito)
    {
        $this->saldoDebito = $saldoDebito;
    }

    /**
     * @return string
     */
    public function getSaldoCredito()
    {
        return $this->saldoCredito;
    }

    /**
     * @param string $saldoCredito
     */
    public function setSaldoCredito($saldoCredito)
    {
        $this->saldoCredito = $saldoCredito;
    }

    /**
     * @return string
     */
    public function getSaldoFinalDebito()
    {
        return $this->saldoFinalDebito;
    }

    /**
     * @param string $saldoFinalDebito
     */
    public function setSaldoFinalDebito($saldoFinalDebito)
    {
        $this->saldoFinalDebito = $saldoFinalDebito;
    }

    /**
     * @return string
     */
    public function getSaldoFinalCredito()
    {
        return $this->saldoFinalCredito;
    }

    /**
     * @param string $saldoFinalCredito
     */
    public function setSaldoFinalCredito($saldoFinalCredito)
    {
        $this->saldoFinalCredito = $saldoFinalCredito;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
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
     * @return string
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * @param string $nivel
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
     * @return string
     */
    public function getEscrituracao()
    {
        return $this->escrituracao;
    }

    /**
     * @return string
     */
    public function getNaturezaInformacao()
    {
        return $this->naturezaInformacao;
    }

    /**
     * @param string $naturezaInformacao
     */
    public function setNaturezaInformacao($naturezaInformacao)
    {
        $this->naturezaInformacao = $naturezaInformacao;
    }

    /**
     * @return string
     */
    public function getIndicadorSuperavit()
    {
        return $this->indicadorSuperavit;
    }

    /**
     * @param string $indicadorSuperavit
     */
    public function setIndicadorSuperavit($indicadorSuperavit)
    {
        $this->indicadorSuperavit = $indicadorSuperavit;
    }

    /**
     * @return string
     */
    public function getSubrecurso()
    {
        return $this->subrecurso;
    }

    /**
     * @param string $subrecurso
     */
    public function setSubrecurso($subrecurso)
    {
        $this->subrecurso = $subrecurso;
    }

    /**
     * @return string
     */
    public function getComplementoAntigo()
    {
        return $this->complementoAntigo;
    }

    /**
     * @param string $complementoAntigo
     */
    public function setComplementoAntigo($complementoAntigo)
    {
        $this->complementoAntigo = $complementoAntigo;
    }

    /**
     * @return string
     */
    public function getSiconfi()
    {
        return $this->siconfi;
    }

    /**
     * @param string $siconfi
     */
    public function setSiconfi($siconfi)
    {
        $this->siconfi = $siconfi;
    }

    /**
     * @return string
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * @param string $complemento
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
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
            "subrecurso" => $this->getSubrecurso(),
            "complementoAntigo" => $this->getComplementoAntigo(),
            "siconfi" => $this->getSiconfi(),
            "complemento" => $this->getComplemento()
        ];
    }
}
