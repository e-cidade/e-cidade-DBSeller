<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2022;

use ECidade\Core\Mappers\ParseArray;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;

class BalanceteReceitaAnterior extends ParseArray implements LayoutPad
{
    protected $dePara = [
        "Código da Receita Orçamentária - SG" => "codigoReceita",
        "Código do Órgão + Unidade Orçamentária" => "orgaoUnidade",
        "Receita Orçada no Período Anterior" => "saldoInicial",
        "Receita Realizada no Período Anterior" => "saldoArrecadadoAcumulado",
        "Código do Recurso Vinculado" => "fonteRecurso",
        "Especificação da Natureza de Receita Orçamentária - SG" => "descricao",
        "Tipo de Nível da Receita Orçamentária" => "tipo",
        "Número do Nível da Receita Orçamentária" => "nivel",
        "Característica Peculiar" => "caracteristicaPeculiar",
        "Complemento do Recurso Vinculado" => "complemento",
        "Código da Fonte de Recurso" => "fonteRecursoSiconfi",
        "Codigo de Execucao Orcamentaria - CO" => "complementoSiconfi",
    ];

    public $codigoReceita;
    public $descricao;
    public $saldoInicial;
    public $saldoArrecadadoAcumulado;
    public $fonteRecurso = '0000';
    public $reduzido;
    public $codigoInstituicao;
    public $complemento;
    public $caracteristicaPeculiar = '000';
    public $orgaoUnidade = '0000';
    public $nivel;
    public $fonteRecursoSiconfi = '0000';
    public $complementoSiconfi = '0000';

    /**
     * Sintética / Analítica
     * @var string
     */
    public $tipo;

    /**
     * @return mixed
     */
    public function getCodigoReceita()
    {
        return $this->codigoReceita;
    }

    /**
     * @param mixed $codigoReceita
     * @return BalanceteReceitaAnterior
     */
    public function setCodigoReceita($codigoReceita)
    {
        $this->codigoReceita = $codigoReceita;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param mixed $descricao
     * @return BalanceteReceitaAnterior
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSaldoInicial()
    {
        return $this->saldoInicial;
    }

    /**
     * @param mixed $saldoInicial
     * @return BalanceteReceitaAnterior
     */
    public function setSaldoInicial($saldoInicial)
    {
        $this->saldoInicial = $saldoInicial;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSaldoArrecadadoAcumulado()
    {
        return $this->saldoArrecadadoAcumulado;
    }

    /**
     * @param mixed $saldoArrecadadoAcumulado
     * @return BalanceteReceitaAnterior
     */
    public function setSaldoArrecadadoAcumulado($saldoArrecadadoAcumulado)
    {
        $this->saldoArrecadadoAcumulado = $saldoArrecadadoAcumulado;
        return $this;
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
     * @return BalanceteReceitaAnterior
     */
    public function setFonteRecurso($fonteRecurso)
    {
        $this->fonteRecurso = $fonteRecurso;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReduzido()
    {
        return $this->reduzido;
    }

    /**
     * @param mixed $reduzido
     * @return BalanceteReceitaAnterior
     */
    public function setReduzido($reduzido)
    {
        $this->reduzido = $reduzido;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoInstituicao()
    {
        return $this->codigoInstituicao;
    }

    /**
     * @param mixed $codigoInstituicao
     * @return BalanceteReceitaAnterior
     */
    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * @param mixed $complemento
     * @return BalanceteReceitaAnterior
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
        return $this;
    }

    /**
     * @return string
     */
    public function getCaracteristicaPeculiar()
    {
        return $this->caracteristicaPeculiar;
    }

    /**
     * @param string $caracteristicaPeculiar
     * @return BalanceteReceitaAnterior
     */
    public function setCaracteristicaPeculiar($caracteristicaPeculiar)
    {
        $this->caracteristicaPeculiar = $caracteristicaPeculiar;
        return $this;
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
     * @return BalanceteReceitaAnterior
     */
    public function setOrgaoUnidade($orgaoUnidade)
    {
        $this->orgaoUnidade = $orgaoUnidade;
        return $this;
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
     * @return BalanceteReceitaAnterior
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;
        return $this;
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
     * @return BalanceteReceitaAnterior
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFonteRecursoSiconfi()
    {
        return $this->fonteRecursoSiconfi;
    }

    /**
     * @param mixed $fonteRecursoSiconfi
     * @return BalanceteReceitaAnterior
     */
    public function setFonteRecursoSiconfi($fonteRecursoSiconfi)
    {
        $this->fonteRecursoSiconfi = $fonteRecursoSiconfi;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComplementoSiconfi()
    {
        return $this->complementoSiconfi;
    }

    /**
     * @param mixed $complementoSiconfi
     * @return BalanceteReceitaAnterior
     */
    public function setComplementoSiconfi($complementoSiconfi)
    {
        $this->complementoSiconfi = $complementoSiconfi;
        return $this;
    }



    /**
     * @return array|string[]
     */
    public function toArray()
    {
        return [
            "codigoReceita" => $this->getCodigoReceita(),
            "orgaoUnidade" => $this->getOrgaoUnidade(),
            "saldoInicial" => $this->getSaldoInicial(),
            "saldoArrecadadoAcumulado" => $this->getSaldoArrecadadoAcumulado(),
            "fonteRecurso" => $this->getFonteRecurso(),
            "descricao" => $this->getDescricao(),
            "tipo" => $this->getTipo(),
            "nivel" => $this->getNivel(),
            "caracteristicaPeculiar" => $this->getCaracteristicaPeculiar(),
            "complemento" => $this->getComplemento(),
            "fonteRecursoSiconfi" => $this->getFonteRecursoSiconfi(),
            "complementoSiconfi" => $this->getComplementoSiconfi()
        ];
    }
}
