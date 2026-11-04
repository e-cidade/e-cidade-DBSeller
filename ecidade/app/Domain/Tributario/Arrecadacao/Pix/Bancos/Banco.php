<?php

namespace App\Domain\Tributario\Arrecadacao\Pix\Bancos;

use \App\Domain\Patrimonial\Protocolo\Model\Cgm;

use convenio;

abstract class Banco
{
    protected $convenio;

    protected $cgm;

    protected $valor;

    protected $vencimento;

    protected $codigoArrecadacao;

    protected $parcela;

    protected $chavePix;

    protected $cnpj_municipio = false;

    protected $cnpj;

    protected $senha;

    protected $usuario;

    protected $codigoBarras;

    public function getConvenio()
    {
        return $this->convenio;
    }

    public function setConvenio(convenio $convenio)
    {
        $this->convenio = $convenio;
    }

    public function getCgm()
    {
        return $this->cgm;
    }

    public function setCgm(Cgm $cgm)
    {
        $this->cgm = $cgm;
    }

    public function getValor()
    {
        return round($this->valor, 2);
    }

    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    public function getVencimento()
    {
        return $this->vencimento;
    }

    public function setVencimento($vencimento)
    {
        $this->vencimento = $vencimento;
    }

    public function getcodigoArrecadacao()
    {
        return $this->codigoArrecadacao;
    }

    public function setcodigoArrecadacao($codigoArrecadacao)
    {
        $this->codigoArrecadacao = $codigoArrecadacao;
    }

    public function getParcela()
    {
        return $this->parcela;
    }

    public function setParcela($parcela)
    {
        $this->parcela = $parcela;
    }

    public function getChavePix()
    {
        return $this->chavePix;
    }

    public function setChavePix($chavePix)
    {
        $this->chavePix = $chavePix;
    }


    /**
     * @return mixed
     */
    public function getCodigoBarras()
    {
        return $this->codigoBarras;
    }

    /**
     * @param mixed $codigoBarras
     * @return Banco
     */
    public function setCodigoBarras($codigoBarras)
    {
        $this->codigoBarras = $codigoBarras;
        return $this;
    }
    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function getCnpjMunicipio()
    {
        return $this->cnpj_municipio;
    }

    public function setCnpjMunicipio($cnpj_municipio)
    {
        $this->cnpj_municipio = $cnpj_municipio;
    }

    public function getCnpj()
    {
        return $this->cnpj;
    }

    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
    }
}
