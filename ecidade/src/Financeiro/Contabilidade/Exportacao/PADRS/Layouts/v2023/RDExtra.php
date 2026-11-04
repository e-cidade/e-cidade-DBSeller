<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023;

use ECidade\Core\Mappers\ParseArray;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;

class RDExtra extends ParseArray implements LayoutPad
{
    protected $dePara = [
        "Código da Conta do Bal. Verificação -SG" => "estrutural",
        "Código do Órgão + Unidade Orçamentária" => "orgaoUnidade",
        "Valor Movimentação - Exercício Atual" => "valor",
        "Identificador Ingressos/Dispêndios" => "identidicador",
        "Classificação" => "classificacao",
        "Campo Obsoleto" => "obsoleto",
    ];

    protected $estrutural;
    protected $orgaoUnidade;
    protected $valor;
    protected $identidicador;
    protected $classificacao;
    protected $obsoleto = '0000';

    /**
     * @return string
     */
    public function getEstrutural()
    {
        return $this->estrutural;
    }

    /**
     * @param string $estrutural
     * @return RDExtra
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
     * @return RDExtra
     */
    public function setOrgaoUnidade($orgaoUnidade)
    {
        $this->orgaoUnidade = $orgaoUnidade;
        return $this;
    }

    /**
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param string $valor
     * @return RDExtra
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentidicador()
    {
        return $this->identidicador;
    }

    /**
     * @param string $identidicador
     * @return RDExtra
     */
    public function setIdentidicador($identidicador)
    {
        $this->identidicador = $identidicador;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassificacao()
    {
        return $this->classificacao;
    }

    /**
     * @param string $classificacao
     * @return RDExtra
     */
    public function setClassificacao($classificacao)
    {
        $this->classificacao = $classificacao;
        return $this;
    }

    /**
     * @return string
     */
    public function getObsoleto()
    {
        return $this->obsoleto;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "estrutural" => $this->getEstrutural(),
            "orgaoUnidade" => $this->getOrgaoUnidade(),
            "valor" => $this->getValor(),
            "identidicador" => $this->getIdentidicador(),
            "classificacao" => $this->getClassificacao(),
            "obsoleto" => $this->getObsoleto(),
        ];
    }
}
