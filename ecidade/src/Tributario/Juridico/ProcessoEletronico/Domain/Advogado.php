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
 * Class Advogado
 * @package ECidade\Tributario\Juridico\ProcessoEletronico\Domain
 */
class Advogado
{
    /**
     * @var $nome
     */
    private $nome;
    /**
     * @var $oab
     */
    private $oab;
    /**
     * @var $cep
     */
    private $cep;
    /**
     * @var $cgccpf
     */
    private $cgccpf;
    /**
     * @var $numero
     */
    private $numero;
    /**
     * @var $endereco
     */
    private $endereco;
    /**
     * @var $municipio
     */
    private $municipio;
    /**
     * @var $complemento
     */
    private $complemento;
    /**
     * @var $matriculaadvogado
     */
    private $matriculaadvogado;
    /**
     * @var $bairro
     */
    private $bairro;
    /**
     * @var $uf
     */
    private $uf;

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
    public function getOab()
    {
        return $this->oab;
    }

    /**
     * @param $oab
     */
    public function setOab($oab)
    {
        $this->oab = $oab;
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
    public function getMatriculaadvogado()
    {
        return $this->matriculaadvogado;
    }

    /**
     * @param $matriculaadvogado
     */
    public function setMatriculaadvogado($matriculaadvogado)
    {
        $this->matriculaadvogado = $matriculaadvogado;
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
