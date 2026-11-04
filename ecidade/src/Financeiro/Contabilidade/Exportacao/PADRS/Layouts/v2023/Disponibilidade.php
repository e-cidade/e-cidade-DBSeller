<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023;

use ECidade\Core\Mappers\ParseArray;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;

class Disponibilidade extends ParseArray implements LayoutPad
{
    protected $dePara = [
        "Código da Conta Bal. Verificação - SG" => "estrutural",
        "Código do Órgão + Unid. Orçamentária" => "orgaoUnidade",
        "Código do Recurso Vinculado" => "subrecurso",
        "Código do Banco - SG" => "banco",
        "Código da Agência Banco - SG" => "agencia",
        "Número da Conta Corrente - SG" => "conta",
        "Tipo da Conta - SG" => "tipoConta",
        "Classificação das Contas Analíticas do Disponível" => "classificacaoConta",
        "Complemento do Recurso Vinculado" => "complementoAntigo",
        "Código da Fonte de Recurso" => "siconfi",
        "Código de Acompanhamento da Execução Orçamentária - CO" => "complemento",
    ];

    protected $estrutural;
    protected $orgaoUnidade;
    protected $subrecurso;
    protected $banco;
    protected $agencia;
    protected $conta;
    protected $tipoConta;
    protected $classificacaoConta;
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
     * @return Disponibilidade
     */
    public function setEstrutural($estrutural)
    {
        $this->estrutural = $estrutural;
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
     * @return Disponibilidade
     */
    public function setOrgaoUnidade($orgaoUnidade)
    {
        $this->orgaoUnidade = $orgaoUnidade;
        return $this;
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
     * @return Disponibilidade
     */
    public function setSubrecurso($subrecurso)
    {
        $this->subrecurso = $subrecurso;
        return $this;
    }

    /**
     * @return string
     */
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * @param string $banco
     * @return Disponibilidade
     */
    public function setBanco($banco)
    {
        $this->banco = $banco;
        return $this;
    }

    /**
     * @return string
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * @param string $agencia
     * @return Disponibilidade
     */
    public function setAgencia($agencia)
    {
        $this->agencia = $agencia;
        return $this;
    }

    /**
     * @return string
     */
    public function getConta()
    {
        return $this->conta;
    }

    /**
     * @param string $conta
     * @return Disponibilidade
     */
    public function setConta($conta)
    {
        $this->conta = $conta;
        return $this;
    }

    /**
     * @return string
     */
    public function getTipoConta()
    {
        return $this->tipoConta;
    }

    /**
     * @param string $tipoConta
     * @return Disponibilidade
     */
    public function setTipoConta($tipoConta)
    {
        $this->tipoConta = $tipoConta;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassificacaoConta()
    {
        return $this->classificacaoConta;
    }

    /**
     * @param string $classificacaoConta
     * @return Disponibilidade
     */
    public function setClassificacaoConta($classificacaoConta)
    {
        $this->classificacaoConta = $classificacaoConta;
        return $this;
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
     * @return Disponibilidade
     */
    public function setComplementoAntigo($complementoAntigo)
    {
        $this->complementoAntigo = $complementoAntigo;
        return $this;
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
     * @return Disponibilidade
     */
    public function setSiconfi($siconfi)
    {
        $this->siconfi = $siconfi;
        return $this;
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
     * @return Disponibilidade
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
        return $this;
    }


    public function toArray()
    {
        return [
            "estrutural" => $this->getEstrutural(),
            "orgaoUnidade" => $this->getOrgaoUnidade(),
            "subrecurso" => $this->getSubrecurso(),
            "banco" => $this->getBanco(),
            "agencia" => $this->getAgencia(),
            "conta" => $this->getConta(),
            "tipoConta" => $this->getTipoConta(),
            "classificacaoConta" => $this->getClassificacaoConta(),
            "complementoAntigo" => $this->getComplementoAntigo(),
            "siconfi" => $this->getSiconfi(),
            "complemento" => $this->getComplemento(),
        ];
    }
}
