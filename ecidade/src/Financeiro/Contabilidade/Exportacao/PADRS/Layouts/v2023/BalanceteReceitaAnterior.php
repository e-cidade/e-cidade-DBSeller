<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023;

use ECidade\Core\Mappers\ParseArray;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;

class BalanceteReceitaAnterior extends ParseArray implements LayoutPad
{
    protected $dePara = [
        "Codigo da Receita Orcamentaria - SG" => "fonte",
        "Codigo do orgão + Unidade Orcamentaria" => "orgaoUnidade",
        "Receita Orcada no Exercicio" => "saldoInicial",
        "Receita Realizada no Periodo" => "arrecadadoAcumulado",
        "Campo Obsoleto1" => "campoObsoleto1",
        "Especificacão da Natureza de Receita Orcamentaria - SG" => "nomeReceita",
        "Tipo de Nivel da Receita Orcamentaria" => "tipo",
        "Número do nivel da receita Orcamentaria" => "nivel",
        "Caracteristica Peculiar" => "cp",
        "Campo Obsoleto2" => "campoObsoleto2",
        "Codigo da Fonte de Recurso" => "fonteRecurso",
        "Codigo de Acompanhamento da Execucão Orcamentaria ? CO" => "complemento",
    ];

    protected $fonte;
    protected $orgaoUnidade;
    protected $saldoInicial;
    protected $arrecadadoAcumulado;
    protected $campoObsoleto1 = '0000';
    protected $nomeReceita;
    protected $tipo;
    protected $nivel;
    protected $cp;
    protected $previsaoAtualizada;
    protected $campoObsoleto2 = '0000';
    protected $fonteRecurso;
    protected $complemento;

    /**
     * @return string
     */
    public function getFonte()
    {
        return $this->fonte;
    }

    /**
     * @param string $fonte
     */
    public function setFonte($fonte)
    {
        $this->fonte = $fonte;
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
    public function getSaldoInicial()
    {
        return $this->saldoInicial;
    }

    /**
     * @param string $saldoInicial
     */
    public function setSaldoInicial($saldoInicial)
    {
        $this->saldoInicial = $saldoInicial;
    }

    /**
     * @return string
     */
    public function getArrecadadoAcumulado()
    {
        return $this->arrecadadoAcumulado;
    }

    /**
     * @param string $arrecadadoAcumulado
     */
    public function setArrecadadoAcumulado($arrecadadoAcumulado)
    {
        $this->arrecadadoAcumulado = $arrecadadoAcumulado;
    }

    /**
     * @return string
     */
    public function getCampoObsoleto1()
    {
        return $this->campoObsoleto1;
    }

    /**
     * @param string $campoObsoleto1
     */
    public function setCampoObsoleto1($campoObsoleto1)
    {
        $this->campoObsoleto1 = $campoObsoleto1;
    }

    /**
     * @return string
     */
    public function getNomeReceita()
    {
        return $this->nomeReceita;
    }

    /**
     * @param string $nomeReceita
     */
    public function setNomeReceita($nomeReceita)
    {
        $this->nomeReceita = $nomeReceita;
    }

    /**
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
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
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * @param string $cp
     */
    public function setCp($cp)
    {
        $this->cp = $cp;
    }

    /**
     * @return string
     */
    public function getCampoObsoleto2()
    {
        return $this->campoObsoleto2;
    }

    /**
     * @param string $campoObsoleto2
     */
    public function setCampoObsoleto2($campoObsoleto2)
    {
        $this->campoObsoleto2 = $campoObsoleto2;
    }

    /**
     * @return string
     */
    public function getFonteRecurso()
    {
        return $this->fonteRecurso;
    }

    /**
     * @param string $fonteRecurso
     */
    public function setFonteRecurso($fonteRecurso)
    {
        $this->fonteRecurso = $fonteRecurso;
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
            "fonte" => $this->getFonte(),
            "orgaoUnidade" => $this->getOrgaoUnidade(),
            "saldoInicial" => $this->getSaldoInicial(),
            "arrecadadoAcumulado" => $this->getArrecadadoAcumulado(),
            "campoObsoleto1" => $this->getCampoObsoleto1(),
            "nomeReceita" => $this->getNomeReceita(),
            "tipo" => $this->getTipo(),
            "nivel" => $this->getNivel(),
            "cp" => $this->getCp(),
            "campoObsoleto2" => $this->getCampoObsoleto2(),
            "fonteRecurso" => $this->getFonteRecurso(),
            "complemento" => $this->getComplemento(),
        ];
    }
}
