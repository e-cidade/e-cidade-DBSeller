<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\Tributario\Juridico\ProcessoEletronico\Domain;

/**
 * Class Devedor
 * @package ECidade\Tributario\Juridico\ProcessoEletronico\Domain
 */
class Devedor
{
    /**
     * @var $tipo_devedor
     */
    private $tipo_devedor;

    /**
     * @var $nome
     */
    private $nome;

    /**
     * @var $cgccpf
     */
    private $cgccpf;

    /**
     * @var $cep
     */
    private $cep;

    /**
     * @var $endereco
     */
    private $endereco;

    /**
     * @var $tipo_logradouro
     */
    private $tipo_logradouro;

    /**
     * @var $numero
     */
    private $numero;

    /**
     * @var $complemento
     */
    private $complemento;

    /**
     * @var $bairro
     */
    private $bairro;

    /**
     * @var $municipio
     */
    private $municipio;

    /**
     * @var $uf
     */
    private $uf;

    /**
     * @var $dtNascimento
     */
    private $dtNascimento;

    /**
     * @var $naturalidade
     */
    private $naturalidade;

    /**
     * @var $pai
     */
    private $pai;

    /**
     * @var $mae
     */
    private $mae;

    /**
     * @var $codigo_logradouro
     */
    private $codigo_logradouro;

    /**
     * @return mixed
     */
    public function getCodigoLogradouro()
    {
        return $this->codigo_logradouro;
    }

    /**
     * @param mixed $codigo_logradouro
     */
    public function setCodigoLogradouro($codigo_logradouro)
    {
        $this->codigo_logradouro = $codigo_logradouro;
    }

    /**
     * @return mixed
     */
    public function getTipoPessoa()
    {
        return (strlen($this->cgccpf) > 11 ? "juridica" : "fisica");
    }

    /**
     * @return mixed
     */
    public function getTipoDevedor()
    {
        return $this->tipo_devedor;
    }

    /**
     * @param $tipo_devedor
     */
    public function setTipoDevedor($tipo_devedor)
    {
        $this->tipo_devedor = $tipo_devedor;
    }

    /**
     * @return mixed
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * @param $genero
     */
    public function setGenero($genero)
    {
        $this->genero = $genero;
    }

    /**
     * @return mixed
     */
    public function getPai()
    {
        return $this->pai;
    }

    /**
     * @param $pai
     */
    public function setPai($pai)
    {
        $this->pai = $pai;
    }

    /**
     * @return mixed
     */
    public function getMae()
    {
        return $this->mae;
    }

    /**
     * @param $mae
     */
    public function setMae($mae)
    {
        $this->mae = $mae;
    }

    /**
     * @param $naturalidade
     */
    public function setNaturalidade($naturalidade)
    {
        $this->naturalidade = $naturalidade;
    }

    /**
     * @return mixed
     */
    public function getNaturalidade()
    {
        return $this->naturalidade;
    }

    /**
     * @return mixed
     */
    public function getDataNascimento()
    {
        return $this->dtNascimento;
    }

    /**
     * @param $dtNasc
     */
    public function setDataNascimento($dtNasc)
    {
        $this->dtNascimento = $dtNasc;
    }

    /**
     * @return mixed
     */
    public function getCgccpf()
    {
        return $this->cgccpf;
    }

    /**
     * @param $cgccpf
     */
    public function setCgccpf($cgccpf)
    {
        $this->cgccpf = $cgccpf;
    }

    /**
     * @return mixed
     */
    public function getTipoLogradouro()
    {
        return $this->tipo_logradouro;
    }

    /**
     * @param $tipoLogradouro
     */
    public function setTipoLogradouro($tipoLogradouro)
    {
        $this->tipo_logradouro = $tipoLogradouro;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * @param $cep
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
    }

    /**
     * @return mixed
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * @param $endereco
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * @return mixed
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * @param $complemento
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
    }

    /**
     * @return mixed
     */
    public function getBairro()
    {
        return $this->bairro;
    }

    /**
     * @param $bairro
     */
    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
    }

    /**
     * @return mixed
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * @param $municipio
     */
    public function setMunicipio($municipio)
    {
        $this->municipio = $municipio;
    }

    /**
     * @return mixed
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * @param $uf
     */
    public function setUf($uf)
    {
        $this->uf = $uf;
    }
}
