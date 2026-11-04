<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023;

use ECidade\Core\Mappers\ParseArray;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;

class Credor extends ParseArray implements LayoutPad
{
    protected $dePara = [
        "Código do Credor" => "cgm",
        "Nome do Credor" => "nome",
        "CNPJ ou CPF do Credor" => "cnp",
        "Inscrição Estadual" => "inscricaoEstadual",
        "Inscrição Municipal" => "inscricaoMunicipal",
        "Endereço" => "endereco",
        "Cidade" => "cidade",
        "Unidade da Federação (UF)" => "uf",
        "CEP" => "cep",
        "Fone" => "fone",
        "Fax" => "fax",
        "Tipo de Credor" => "tipoCredor",
        "Tipo de Pessoa" => "tipoPessoa",
    ];
    /**
     * @var string
     */
    protected $cgm;
    /**
     * @var string
     */
    protected $nome;
    /**
     * @var string
     */
    protected $cnp;
    /**
     * @var string
     */
    protected $inscricaoEstadual;
    /**
     * @var string
     */
    protected $inscricaoMunicipal;
    /**
     * @var string
     */
    protected $endereco;
    /**
     * @var string
     */
    protected $cidade;
    /**
     * @var string
     */
    protected $uf;
    /**
     * @var string
     */
    protected $cep;
    /**
     * @var string
     */
    protected $fone;
    /**
     * @var string
     */
    protected $fax;
    /**
     * @var string
     */
    protected $tipoCredor = '01';
    /**
     * @var string
     */
    protected $tipoPessoa;

    /**
     * @return string
     */
    public function getCgm()
    {
        return $this->cgm;
    }

    /**
     * @param string $cgm
     * @return Credor
     */
    public function setCgm($cgm)
    {
        $this->cgm = $cgm;
        return $this;
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
     * @return Credor
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getCnp()
    {
        return $this->cnp;
    }

    /**
     * @param string $cnp
     * @return Credor
     */
    public function setCnp($cnp)
    {
        $this->cnp = $cnp;
        return $this;
    }

    /**
     * @return string
     */
    public function getInscricaoEstadual()
    {
        return $this->inscricaoEstadual;
    }

    /**
     * @param string $inscricaoEstadual
     * @return Credor
     */
    public function setInscricaoEstadual($inscricaoEstadual)
    {
        $this->inscricaoEstadual = $inscricaoEstadual;
        return $this;
    }

    /**
     * @return string
     */
    public function getInscricaoMunicipal()
    {
        return $this->inscricaoMunicipal;
    }

    /**
     * @param string $inscricaoMunicipal
     * @return Credor
     */
    public function setInscricaoMunicipal($inscricaoMunicipal)
    {
        $this->inscricaoMunicipal = $inscricaoMunicipal;
        return $this;
    }

    /**
     * @return string
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * @param string $endereco
     * @return Credor
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
        return $this;
    }

    /**
     * @return string
     */
    public function getCidade()
    {
        return $this->cidade;
    }

    /**
     * @param string $cidade
     * @return Credor
     */
    public function setCidade($cidade)
    {
        $this->cidade = $cidade;
        return $this;
    }

    /**
     * @return string
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * @param string $uf
     * @return Credor
     */
    public function setUf($uf)
    {
        $this->uf = $uf;
        return $this;
    }

    /**
     * @return string
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * @param string $cep
     * @return Credor
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
        return $this;
    }

    /**
     * @return string
     */
    public function getFone()
    {
        return $this->fone;
    }

    /**
     * @param string $fone
     * @return Credor
     */
    public function setFone($fone)
    {
        $this->fone = $fone;
        return $this;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     * @return Credor
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getTipoCredor()
    {
        return $this->tipoCredor;
    }

    /**
     * @return string
     */
    public function getTipoPessoa()
    {
        return $this->tipoPessoa;
    }

    /**
     * @param string $tipoPessoa
     * @return Credor
     */
    public function setTipoPessoa($tipoPessoa)
    {
        $this->tipoPessoa = $tipoPessoa;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "cgm" => $this->getCgm(),
            "nome" => $this->getNome(),
            "cnp" => $this->getCnp(),
            "inscricaoEstadual" => $this->getInscricaoEstadual(),
            "inscricaoMunicipal" => $this->getInscricaoMunicipal(),
            "endereco" => $this->getEndereco(),
            "cidade" => $this->getCidade(),
            "uf" => $this->getUf(),
            "cep" => $this->getCep(),
            "fone" => $this->getFone(),
            "fax" => $this->getFax(),
            "tipoCredor" => $this->getTipoCredor(),
            "tipoPessoa" => $this->getTipoPessoa(),
        ];
    }
}
