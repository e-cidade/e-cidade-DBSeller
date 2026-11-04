<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023;

use ECidade\Core\Mappers\ParseArray;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;

class Empenho extends ParseArray implements LayoutPad
{
    protected $dePara = [
        "Codigo do orgao" => "codigoOrgao",
        "Codigo da Unidade Orcamentária " => "codigoUnidade",
        "Codigo da Funcao " => "codigoFuncao",
        "Codigo da Subfuncao " => "codigoSubfuncao",
        "Codigo do Programa " => "codigoPrograma",
        "Campo Obsoleto1" => "campoObsoleto1",
        "Codigo do Projeto/Atividade/Op. Especial" => "codigoProjeto",
        "Codigo da Rubrica de Despesa - SG" => "codigoRubrica",
        "Codigo do Recurso Vinculado" => "codigoRecursoVinculado",
        "Contrapartida - Recurso " => "contrapartidaRecurso",
        "Numero do Empenho" => "numeroEmpenho",
        "Data do Empenho" => "dataEmpenho",
        "Valor do Empenho" => "valorEmpenho",
        "Sinal do Valor" => "sinalValor",
        "Codigo do Credor " => "codigoCredor",
        "Campo Obsoleto2" => "campoObsoleto2",
        "Caracteristica Peculiar" => "caracteristicaPeculiar",
        "Campo Obsoleto3" => "campoObsoleto3",
        "Registro de Precos " => "registroPrecos",
        "Campo obsoleto4" => "campoobsoleto4",
        "Numero da Licitacao " => "numeroLicitacao",
        "Ano da Licitacao " => "anoLicitacao",
        "Historico do Empenho " => "historicoEmpenho",
        "Modalidade da Licitacao/Forma de Contratacao " => "modalidadeLicitacao",
        "Base Legal da Contratacao " => "baseLegalContratacao",
        "Identificador de despesa com funcionário" => "identificadorDespesaFuncionario",
        "Licitacao Compartilhada" => "licitacaoCompartilhada",
        "CNPJ do orgao Gerenciador da Licitacao " => "cnpjOrgaoGerenciadorLicitacao",
        "Complemento do Recurso Vinculado" => "complementoVinculado",
        "Codigo da Fonte de Recurso " => "codigoFonteRecurso",
        "Codigo de Acompanhamento da Execucao Orcamentária CO" => "complemento",
    ];

    protected $codigoOrgao;
    protected $codigoUnidade;
    protected $codigoFuncao;
    protected $codigoSubfuncao;
    protected $codigoPrograma;
    protected $campoObsoleto1 = '000';
    protected $codigoProjeto;
    protected $codigoRubrica;
    protected $codigoRecursoVinculado;
    protected $contrapartidaRecurso;
    protected $numeroEmpenho;
    protected $dataEmpenho;
    protected $valorEmpenho;
    protected $sinalValor;
    protected $codigoCredor;
    protected $campoObsoleto2;
    protected $caracteristicaPeculiar;
    protected $campoObsoleto3;
    protected $registroPrecos;
    protected $campoobsoleto4;
    protected $numeroLicitacao;
    protected $anoLicitacao;
    protected $historicoEmpenho;
    protected $modalidadeLicitacao;
    protected $baseLegalContratacao;
    protected $identificadorDespesaFuncionario;
    protected $licitacaoCompartilhada;
    protected $cnpjOrgaoGerenciadorLicitacao;
    protected $complementoVinculado;
    protected $complemento;
    protected $codigoFonteRecurso;

    /**
     * @return mixed
     */
    public function getCodigoOrgao()
    {
        return $this->codigoOrgao;
    }

    /**
     * @param mixed $codigoOrgao
     */
    public function setCodigoOrgao($codigoOrgao)
    {
        $this->codigoOrgao = $codigoOrgao;
    }

    /**
     * @return mixed
     */
    public function getCodigoUnidade()
    {
        return $this->codigoUnidade;
    }

    /**
     * @param mixed $codigoUnidade
     */
    public function setCodigoUnidade($codigoUnidade)
    {
        $this->codigoUnidade = $codigoUnidade;
    }

    /**
     * @return mixed
     */
    public function getCodigoFuncao()
    {
        return $this->codigoFuncao;
    }

    /**
     * @param mixed $codigoFuncao
     */
    public function setCodigoFuncao($codigoFuncao)
    {
        $this->codigoFuncao = $codigoFuncao;
    }

    /**
     * @return mixed
     */
    public function getCodigoSubfuncao()
    {
        return $this->codigoSubfuncao;
    }

    /**
     * @param mixed $codigoSubfuncao
     */
    public function setCodigoSubfuncao($codigoSubfuncao)
    {
        $this->codigoSubfuncao = $codigoSubfuncao;
    }

    /**
     * @return mixed
     */
    public function getCodigoPrograma()
    {
        return $this->codigoPrograma;
    }

    /**
     * @param mixed $codigoPrograma
     */
    public function setCodigoPrograma($codigoPrograma)
    {
        $this->codigoPrograma = $codigoPrograma;
    }

    /**
     * @return mixed
     */
    public function getCampoObsoleto1()
    {
        $this->campoObsoleto1 = '000';
        return $this->campoObsoleto1;
    }

    /**
     * @return mixed
     */
    public function getCodigoProjeto()
    {
        return $this->codigoProjeto;
    }

    /**
     * @param mixed $codigoProjeto
     */
    public function setCodigoProjeto($codigoProjeto)
    {
        $this->codigoProjeto = $codigoProjeto;
    }

    /**
     * @return mixed
     */
    public function getCodigoRubrica()
    {
        return $this->codigoRubrica;
    }

    /**
     * @param mixed $codigoRubrica
     */
    public function setCodigoRubrica($codigoRubrica)
    {
        $this->codigoRubrica = $codigoRubrica;
    }

    /**
     * @return mixed
     */
    public function getCodigoRecursoVinculado()
    {
        return $this->codigoRecursoVinculado;
    }

    /**
     * @param mixed $codigoRecursoVinculado
     */
    public function setCodigoRecursoVinculado($codigoRecursoVinculado)
    {
        $this->codigoRecursoVinculado = $codigoRecursoVinculado;
    }

    /**
     * @return mixed
     */
    public function getContrapartidaRecurso()
    {
        return $this->contrapartidaRecurso;
    }

    /**
     * @param mixed $contrapartidaRecurso
     */
    public function setContrapartidaRecurso($contrapartidaRecurso)
    {
        $this->contrapartidaRecurso = $contrapartidaRecurso;
    }

    /**
     * @return mixed
     */
    public function getNumeroEmpenho()
    {
        return $this->numeroEmpenho;
    }

    /**
     * @param mixed $numeroEmpenho
     */
    public function setNumeroEmpenho($numeroEmpenho)
    {
        $this->numeroEmpenho = $numeroEmpenho;
    }

    /**
     * @return mixed
     */
    public function getDataEmpenho()
    {
        return $this->dataEmpenho;
    }

    /**
     * @param mixed $dataEmpenho
     */
    public function setDataEmpenho($dataEmpenho)
    {
        $this->dataEmpenho = $dataEmpenho;
    }

    /**
     * @return mixed
     */
    public function getValorEmpenho()
    {
        return $this->valorEmpenho;
    }

    /**
     * @param mixed $valorEmpenho
     */
    public function setValorEmpenho($valorEmpenho)
    {
        $this->valorEmpenho = $valorEmpenho;
    }

    /**
     * @return mixed
     */
    public function getSinalValor()
    {
        return $this->sinalValor;
    }

    /**
     * @param mixed $sinalValor
     */
    public function setSinalValor($sinalValor)
    {
        $this->sinalValor = $sinalValor;
    }

    /**
     * @return mixed
     */
    public function getCodigoCredor()
    {
        return $this->codigoCredor;
    }

    /**
     * @param mixed $codigoCredor
     */
    public function setCodigoCredor($codigoCredor)
    {
        $this->codigoCredor = $codigoCredor;
    }

    /**
     * @return mixed
     */
    public function getCampoObsoleto2()
    {
        $this->campoObsoleto2 = str_repeat(' ', 165);
        return $this->campoObsoleto2;
    }

    /**
     * @return mixed
     */
    public function getCaracteristicaPeculiar()
    {
        return $this->caracteristicaPeculiar;
    }

    /**
     * @param mixed $caracteristicaPeculiar
     */
    public function setCaracteristicaPeculiar($caracteristicaPeculiar)
    {
        $this->caracteristicaPeculiar = $caracteristicaPeculiar;
    }

    /**
     * @return mixed
     */
    public function getCampoObsoleto3()
    {
        $this->campoObsoleto3 = '  ';
        return $this->campoObsoleto3;
    }

    /**
     * @return mixed
     */
    public function getRegistroPrecos()
    {
        return $this->registroPrecos;
    }

    /**
     * @param mixed $registroPrecos
     */
    public function setRegistroPrecos($registroPrecos)
    {
        $this->registroPrecos = $registroPrecos;
    }

    /**
     * @return mixed
     */
    public function getCampoobsoleto4()
    {
        $this->campoobsoleto4 = str_repeat(' ', 20);
        return $this->campoobsoleto4;
    }

    /**
     * @return mixed
     */
    public function getNumeroLicitacao()
    {
        return $this->numeroLicitacao;
    }

    /**
     * @param mixed $numeroLicitacao
     */
    public function setNumeroLicitacao($numeroLicitacao)
    {
        $this->numeroLicitacao = $numeroLicitacao;
    }

    /**
     * @return mixed
     */
    public function getAnoLicitacao()
    {
        return $this->anoLicitacao;
    }

    /**
     * @param mixed $anoLicitacao
     */
    public function setAnoLicitacao($anoLicitacao)
    {
        $this->anoLicitacao = $anoLicitacao;
    }

    /**
     * @return mixed
     */
    public function getHistoricoEmpenho()
    {
        return $this->historicoEmpenho;
    }

    /**
     * @param mixed $historicoEmpenho
     */
    public function setHistoricoEmpenho($historicoEmpenho)
    {
        $this->historicoEmpenho = $historicoEmpenho;
    }

    /**
     * @return mixed
     */
    public function getModalidadeLicitacao()
    {
        return $this->modalidadeLicitacao;
    }

    /**
     * @param mixed $modalidadeLicitacao
     */
    public function setModalidadeLicitacao($modalidadeLicitacao)
    {
        $this->modalidadeLicitacao = $modalidadeLicitacao;
    }

    /**
     * @return mixed
     */
    public function getBaseLegalContratacao()
    {
        return $this->baseLegalContratacao;
    }

    /**
     * @param mixed $baseLegalContratacao
     */
    public function setBaseLegalContratacao($baseLegalContratacao)
    {
        $this->baseLegalContratacao = $baseLegalContratacao;
    }

    /**
     * @return mixed
     */
    public function getIdentificadorDespesaFuncionario()
    {
        return $this->identificadorDespesaFuncionario;
    }

    /**
     * @param mixed $identificadorDespesaFuncionario
     */
    public function setIdentificadorDespesaFuncionario($identificadorDespesaFuncionario)
    {
        $this->identificadorDespesaFuncionario = $identificadorDespesaFuncionario;
    }

    /**
     * @return mixed
     */
    public function getLicitacaoCompartilhada()
    {
        return $this->licitacaoCompartilhada;
    }

    /**
     * @param mixed $licitacaoCompartilhada
     */
    public function setLicitacaoCompartilhada($licitacaoCompartilhada)
    {
        $this->licitacaoCompartilhada = $licitacaoCompartilhada;
    }

    /**
     * @return mixed
     */
    public function getCnpjOrgaoGerenciadorLicitacao()
    {
        return $this->cnpjOrgaoGerenciadorLicitacao;
    }

    /**
     * @param mixed $cnpjOrgaoGerenciadorLicitacao
     */
    public function setCnpjOrgaoGerenciadorLicitacao($cnpjOrgaoGerenciadorLicitacao)
    {
        $this->cnpjOrgaoGerenciadorLicitacao = $cnpjOrgaoGerenciadorLicitacao;
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
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
    }

    /**
     * @return mixed
     */
    public function getCodigoFonteRecurso()
    {
        return $this->codigoFonteRecurso;
    }

    /**
     * @param mixed $codigoFonteRecurso
     */
    public function setCodigoFonteRecurso($codigoFonteRecurso)
    {
        $this->codigoFonteRecurso = $codigoFonteRecurso;
    }

    public function toArray()
    {
        return [
            "codigoOrgao" => $this->getCodigoOrgao(),
            "codigoUnidade" => $this->getCodigoUnidade(),
            "codigoFuncao" => $this->getCodigoFuncao(),
            "codigoSubfuncao" => $this->getCodigoSubfuncao(),
            "codigoPrograma" => $this->getCodigoPrograma(),
            "campoObsoleto1" => $this->getCampoObsoleto1(),
            "codigoProjeto" => $this->getCodigoProjeto(),
            "codigoRubrica" => $this->getCodigoRubrica(),
            "codigoRecursoVinculado" => $this->getCodigoRecursoVinculado(),
            "contrapartidaRecurso" => $this->getContrapartidaRecurso(),
            "numeroEmpenho" => $this->getNumeroEmpenho(),
            "dataEmpenho" => $this->getDataEmpenho(),
            "valorEmpenho" => $this->getValorEmpenho(),
            "sinalValor" => $this->getSinalValor(),
            "codigoCredor" => $this->getCodigoCredor(),
            "campoObsoleto2" => $this->getCampoObsoleto2(),
            "caracteristicaPeculiar" => $this->getCaracteristicaPeculiar(),
            "campoObsoleto3" => $this->getCampoObsoleto3(),
            "registroPrecos" => $this->getRegistroPrecos(),
            "campoobsoleto4" => $this->getCampoobsoleto4(),
            "numeroLicitacao" => $this->getNumeroLicitacao(),
            "anoLicitacao" => $this->getAnoLicitacao(),
            "historicoEmpenho" => $this->getHistoricoEmpenho(),
            "modalidadeLicitacao" => $this->getModalidadeLicitacao(),
            "baseLegalContratacao" => $this->getBaseLegalContratacao(),
            "identificadorDespesaFuncionario" => $this->getIdentificadorDespesaFuncionario(),
            "licitacaoCompartilhada" => $this->getLicitacaoCompartilhada(),
            "cnpjOrgaoGerenciadorLicitacao" => $this->getCnpjOrgaoGerenciadorLicitacao(),
            "complementoVinculado" => $this->getComplementoVinculado(),
            "codigoFonteRecurso" => $this->getCodigoFonteRecurso(),
            "complemento" => $this->getComplemento(),
        ];
    }

    /**
     * @return mixed
     */
    public function getComplementoVinculado()
    {
        return $this->complementoVinculado;
    }

    /**
     * @param mixed $complementoVinculado
     */
    public function setComplementoVinculado($complementoVinculado)
    {
        $this->complementoVinculado = $complementoVinculado;
    }
}
