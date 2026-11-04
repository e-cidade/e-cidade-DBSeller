<?php
/**
 * Created by PhpStorm.
 * User: andri
 * Date: 29/04/2019
 * Time: 08:20
 */

namespace ECidade\Educacao\Escola\Model;

use DBDate;
use ECidade\Educacao\Escola\Registry\AlunoRegistry;
use ECidade\Educacao\Escola\Registry\PaisRegistry;
use Exception;

class Aluno
{
    const NACIONALIDADE_BRASILEIRO = 1;
    const NACIONALIDADE_NATURALIZADO = 2;
    const NACIONALIDADE_ESTRANGEIRO = 3;

    const ESCOLARIZACAO_ESPECIAL_HOSPITAL = 1;
    const ESCOLARIZACAO_ESPECIAL_DOMICILIO = 2;
    const ESCOLARIZACAO_ESPECIAL_NAO = 3;

    const TRANSPORTE_PUBLICO_UTILIZA = 1;
    const TRANSPORTE_PUBLICO_NAO_UTILIZA = 0;

    const TRANSPORTE_RESPONSAVEL_NENHUM = null;
    const TRANSPORTE_RESPONSAVEL_ESTADO = 1;
    const TRANSPORTE_RESPONSAVEL_MUNICIPIO = 2;

    private $codigo;
    private $nome;
    private $nomeSocial;

    private $raca;
    private $telefone;
    /**
     * @var DBDate
     */
    private $dataCadastro;
    private $identidade;
    private $login;
    private $nomeResponsavel;
    private $emailResponsavel;
    private $observacao;

    private $zonaResidencia;

    /**
     * @var DBDate
     */
    private $dataCertidao;
    private $nis;
    /**
     * @var boolean
     */
    private $bolsafamilia;
    /**
     * @var boolean
     */
    private $alunoPassivo;

    /**
     * @var DBDate
     */
    private $dataNascimento;
    /**
     * @var DBDate
     */
    private $ultimaAlteracao;
    private $estadoCivil;
    private $nacionalidade;
    private $tipoContato;
    private $cpf;
    private $email;
    private $telefoneCelular;
    private $fax;
    private $hora;
    private $filiacao;
    private $mae;
    private $pai;
    private $profissao;
    private $sexo;
    private $foto;
    private $codigoInep;
    /**
     * @var Pais
     */
    private $pais;

    /**
     * @var integer
     */
    private $recebeEscolarizacaoEspecial;

    private $censoUfNascimento;
    private $censoMunicipioNascimento;

    private $endereco;
    private $numero;
    private $complemento;
    private $bairro;
    private $cep;
    private $caixaPostal;
    private $ufEndereco;
    private $municipioEndereco;

    /**
     * @var boolean
     */
    private $utilizaTransportePublico;

    /**
     * @var integer
     */
    private $transporteResponsavel;

    /**
     * @var integer
     */
    private $atendimentoEspecializado;


    private $celularResponsavel;
    private $situacaoDocumentacao;
    private $cartaoSus;
    /**
     * @var int
     */
    private $tipoSanguineo;
    /**
     * @var string
     */
    private $municipioEstrangeiro;
    /**
     * @var Pais
     */
    private $paisResidencia;

    /**
     * @var integer
     */
    private $localizacaodiferenciada;

    /**
     * documentos
     */
    private $tipoCertidao;
    private $numeroCertidao;
    private $livroCertidao;
    private $folhaCertidao;
    private $cartorioCertidao;
    private $ufCertidao;
    private $municipioCertidao;
    private $matriculaCeridao;

    private $passaporte;

    /**
     * @var DBDate
     */
    private $identidadeExpedicao;
    private $identidadeComplemento;
    private $identidadeOrgaoEmissor;
    private $identidadeUF;

    /**
     * @var string
     */
    private $cnhCategoria;
    /**
     * @var DBDate
     */
    private $cnhDataEmissao;
    /**
     * @var DBDate
     */
    private $cnhDataHabilitacao;
    /**
     * @var DBDate
     */
    private $cnhDataVencimento;
    private $cnhNumero;

    /**
     * @var AlunoNecessidadeEspecial[]
     */
    private $necessidadeEspecial = array();

    /**
     * @var AlunoRecursoNecessarioAvaliacaoInep[]
     */
    private $recursoNecessarioAvaliacaoInep = array();

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param mixed $codigo
     * @return Aluno
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
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
     * @return Aluno
     */
    public function setNome($nome)
    {
        $this->nome = trim($nome);
        return $this;
    }

        /**
     * @return string
     */
    public function getNomeSocial()
    {
        return $this->nomeSocial;
    }

    /**
     * @param string $nomeSocial
     * @return Aluno
     */
    public function setNomeSocial($nome)
    {
        $this->nomeSocial = trim($nome);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRaca()
    {
        return $this->raca;
    }

    /**
     * @param mixed $raca
     * @return Aluno
     */
    public function setRaca($raca)
    {
        $this->raca = trim($raca);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * @param mixed $telefone
     * @return Aluno
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
        return $this;
    }

    /**
     * @return DBDate
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * @param DBDate $dataCadastro
     * @return Aluno
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentidade()
    {
        return $this->identidade;
    }

    /**
     * @param mixed $identidade
     * @return Aluno
     */
    public function setIdentidade($identidade)
    {
        $this->identidade = $identidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     * @return Aluno
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNomeResponsavel()
    {
        return $this->nomeResponsavel;
    }

    /**
     * @param mixed $nomeResponsavel
     * @return Aluno
     */
    public function setNomeResponsavel($nomeResponsavel)
    {
        $this->nomeResponsavel = trim($nomeResponsavel);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmailResponsavel()
    {
        return $this->emailResponsavel;
    }

    /**
     * @param mixed $emailResponsavel
     * @return Aluno
     */
    public function setEmailResponsavel($emailResponsavel)
    {
        $this->emailResponsavel = trim($emailResponsavel);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObservacao()
    {
        return $this->observacao;
    }

    /**
     * @param mixed $observacao
     * @return Aluno
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getZonaResidencia()
    {
        return $this->zonaResidencia;
    }

    /**
     * @param mixed $zonaResidencia
     * @return Aluno
     */
    public function setZonaResidencia($zonaResidencia)
    {
        $this->zonaResidencia = trim($zonaResidencia);
        return $this;
    }

    /**
     * @return DBDate
     */
    public function getDataCertidao()
    {
        return $this->dataCertidao;
    }

    /**
     * @param DBDate $dataCertidao
     * @return Aluno
     */
    public function setDataCertidao($dataCertidao)
    {
        $this->dataCertidao = $dataCertidao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNis()
    {
        return $this->nis;
    }

    /**
     * @param mixed $nis
     * @return Aluno
     */
    public function setNis($nis)
    {
        $this->nis = $nis;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBolsafamilia()
    {
        return $this->bolsafamilia;
    }

    /**
     * @param bool $bolsafamilia
     * @return Aluno
     */
    public function setBolsafamilia($bolsafamilia)
    {
        $this->bolsafamilia = $bolsafamilia;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAlunoPassivo()
    {
        return $this->alunoPassivo;
    }

    /**
     * @param boolean $alunoPassivo
     * @return Aluno
     */
    public function setAlunoPassivo($alunoPassivo)
    {
        $this->alunoPassivo = $alunoPassivo;
        return $this;
    }

    /**
     * @return DBDate
     */
    public function getCnhDataEmissao()
    {
        return $this->cnhDataEmissao;
    }

    /**
     * @param DBDate $cnhDataEmissao
     * @return Aluno
     */
    public function setCnhDataEmissao($cnhDataEmissao)
    {
        $this->cnhDataEmissao = $cnhDataEmissao;
        return $this;
    }

    /**
     * @return DBDate
     */
    public function getCnhDataHabilitacao()
    {
        return $this->cnhDataHabilitacao;
    }

    /**
     * @param DBDate $cnhDataHabilitacao
     * @return Aluno
     */
    public function setCnhDataHabilitacao($cnhDataHabilitacao)
    {
        $this->cnhDataHabilitacao = $cnhDataHabilitacao;
        return $this;
    }

    /**
     * @return DBDate
     */
    public function getCnhDataVencimento()
    {
        return $this->cnhDataVencimento;
    }

    /**
     * @param DBDate $cnhDataVencimento
     * @return Aluno
     */
    public function setCnhDataVencimento($cnhDataVencimento)
    {
        $this->cnhDataVencimento = $cnhDataVencimento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCnhNumero()
    {
        return $this->cnhNumero;
    }

    /**
     * @param mixed $cnhNumero
     * @return Aluno
     */
    public function setCnhNumero($cnhNumero)
    {
        $this->cnhNumero = $cnhNumero;
        return $this;
    }

    /**
     * @return DBDate
     */
    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    /**
     * @param DBDate $dataNascimento
     * @return Aluno
     */
    public function setDataNascimento($dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;
        return $this;
    }

    /**
     * @return DBDate
     */
    public function getUltimaAlteracao()
    {
        return $this->ultimaAlteracao;
    }

    /**
     * @param DBDate $ultimaAlteracao
     * @return Aluno
     */
    public function setUltimaAlteracao($ultimaAlteracao)
    {
        $this->ultimaAlteracao = $ultimaAlteracao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEstadoCivil()
    {
        return $this->estadoCivil;
    }

    /**
     * @param mixed $estadoCivil
     * @return Aluno
     */
    public function setEstadoCivil($estadoCivil)
    {
        $this->estadoCivil = $estadoCivil;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNacionalidade()
    {
        return $this->nacionalidade;
    }

    /**
     * @param mixed $nacionalidade
     * @return Aluno
     */
    public function setNacionalidade($nacionalidade)
    {
        $this->nacionalidade = (int) $nacionalidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCnhCategoria()
    {
        return $this->cnhCategoria;
    }

    /**
     * @param mixed $cnhCategoria
     * @return Aluno
     */
    public function setCnhCategoria($cnhCategoria)
    {
        $this->cnhCategoria = $cnhCategoria;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTipoContato()
    {
        return $this->tipoContato;
    }

    /**
     * @param mixed $tipoContato
     * @return Aluno
     */
    public function setTipoContato($tipoContato)
    {
        $this->tipoContato = $tipoContato;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param mixed $cpf
     * @return Aluno
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return Aluno
     */
    public function setEmail($email)
    {
        $this->email = trim($email);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTelefoneCelular()
    {
        return $this->telefoneCelular;
    }

    /**
     * @param mixed $telefoneCelular
     * @return Aluno
     */
    public function setTelefoneCelular($telefoneCelular)
    {
        $this->telefoneCelular = $telefoneCelular;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param mixed $fax
     * @return Aluno
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * @param mixed $hora
     * @return Aluno
     */
    public function setHora($hora)
    {
        $this->hora = $hora;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFiliacao()
    {
        return $this->filiacao;
    }

    /**
     * @param mixed $filiacao
     * @return Aluno
     */
    public function setFiliacao($filiacao)
    {
        $this->filiacao = $filiacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMae()
    {
        return $this->mae;
    }

    /**
     * @param mixed $mae
     * @return Aluno
     */
    public function setMae($mae)
    {
        $this->mae = trim($mae);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPai()
    {
        return $this->pai;
    }

    /**
     * @param mixed $pai
     * @return Aluno
     */
    public function setPai($pai)
    {
        $this->pai = trim($pai);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfissao()
    {
        return $this->profissao;
    }

    /**
     * @param mixed $profissao
     * @return Aluno
     */
    public function setProfissao($profissao)
    {
        $this->profissao = $profissao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * @param mixed $sexo
     * @return Aluno
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * @param mixed $foto
     * @return Aluno
     */
    public function setFoto($foto)
    {
        $this->foto = trim($foto);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoInep()
    {
        return $this->codigoInep;
    }

    /**
     * @param mixed $codigoInep
     * @return Aluno
     */
    public function setCodigoInep($codigoInep)
    {
        $this->codigoInep = $codigoInep;
        return $this;
    }

    /**
     * @return Pais
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * @param Pais $pais
     * @return Aluno
     */
    public function setPais($pais)
    {
        $this->pais = $pais;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecebeEscolarizacaoEspecial()
    {
        return $this->recebeEscolarizacaoEspecial;
    }

    /**
     * @param int $recebeEscolarizacaoEspecial
     * @return Aluno
     */
    public function setRecebeEscolarizacaoEspecial($recebeEscolarizacaoEspecial)
    {
        $recebeEscolarizacaoEspecial = trim($recebeEscolarizacaoEspecial) == ""? 3 : $recebeEscolarizacaoEspecial;
        $this->recebeEscolarizacaoEspecial = (int) trim($recebeEscolarizacaoEspecial);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCensoUfNascimento()
    {
        return $this->censoUfNascimento;
    }

    /**
     * @param mixed $censoUfNascimento
     * @return Aluno
     */
    public function setCensoUfNascimento($censoUfNascimento)
    {
        $this->censoUfNascimento = $censoUfNascimento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCensoMunicipioNascimento()
    {
        return $this->censoMunicipioNascimento;
    }

    /**
     * @param mixed $censoMunicipioNascimento
     * @return Aluno
     */
    public function setCensoMunicipioNascimento($censoMunicipioNascimento)
    {
        $this->censoMunicipioNascimento = $censoMunicipioNascimento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * @param mixed $endereco
     * @return Aluno
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param mixed $numero
     * @return Aluno
     */
    public function setNumero($numero)
    {
        $this->numero = trim($numero);
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
     * @return Aluno
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBairro()
    {
        return $this->bairro;
    }

    /**
     * @param mixed $bairro
     * @return Aluno
     */
    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * @param mixed $cep
     * @return Aluno
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCaixaPostal()
    {
        return $this->caixaPostal;
    }

    /**
     * @param mixed $caixaPostal
     * @return Aluno
     */
    public function setCaixaPostal($caixaPostal)
    {
        $this->caixaPostal = $caixaPostal;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUfEndereco()
    {
        return $this->ufEndereco;
    }

    /**
     * @param mixed $ufEndereco
     * @return Aluno
     */
    public function setUfEndereco($ufEndereco)
    {
        $this->ufEndereco = $ufEndereco;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMunicipioEndereco()
    {
        return $this->municipioEndereco;
    }

    /**
     * @param mixed $municipioEndereco
     * @return Aluno
     */
    public function setMunicipioEndereco($municipioEndereco)
    {
        $this->municipioEndereco = $municipioEndereco;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUtilizaTransportePublico()
    {
        return $this->utilizaTransportePublico;
    }

    /**
     * @param bool $utilizaTransportePublico
     * @return Aluno
     */
    public function setUtilizaTransportePublico($utilizaTransportePublico)
    {
        $this->utilizaTransportePublico = $utilizaTransportePublico;
        return $this;
    }

    /**
     * @return int
     */
    public function getTransporteResponsavel()
    {
        return $this->transporteResponsavel;
    }

    /**
     * @param int $transporteResponsavel
     * @return Aluno
     */
    public function setTransporteResponsavel($transporteResponsavel)
    {
        $this->transporteResponsavel = (int) trim($transporteResponsavel);
        return $this;
    }

    /**
     * @return int
     */
    public function getAtendimentoEspecializado()
    {
        return $this->atendimentoEspecializado;
    }

    /**
     * @param int $atendimentoEspecializado
     * @return Aluno
     */
    public function setAtendimentoEspecializado($atendimentoEspecializado)
    {
        $this->atendimentoEspecializado = $atendimentoEspecializado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCelularResponsavel()
    {
        return $this->celularResponsavel;
    }

    /**
     * @param mixed $celularResponsavel
     * @return Aluno
     */
    public function setCelularResponsavel($celularResponsavel)
    {
        $this->celularResponsavel = $celularResponsavel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSituacaoDocumentacao()
    {
        return $this->situacaoDocumentacao;
    }

    /**
     * @param mixed $situacaoDocumentacao
     * @return Aluno
     */
    public function setSituacaoDocumentacao($situacaoDocumentacao)
    {
        $this->situacaoDocumentacao = $situacaoDocumentacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCartaoSus()
    {
        return $this->cartaoSus;
    }

    /**
     * @param mixed $cartaoSus
     * @return Aluno
     */
    public function setCartaoSus($cartaoSus)
    {
        $this->cartaoSus = $cartaoSus;
        return $this;
    }

    /**
     * @return int
     */
    public function getTipoSanguineo()
    {
        return $this->tipoSanguineo;
    }

    /**
     * @param int $tipoSanguineo
     * @return Aluno
     */
    public function setTipoSanguineo($tipoSanguineo)
    {
        $this->tipoSanguineo = $tipoSanguineo;
        return $this;
    }

    /**
     * @return string
     */
    public function getMunicipioEstrangeiro()
    {
        return $this->municipioEstrangeiro;
    }

    /**
     * @param string $municipioEstrangeiro
     * @return Aluno
     */
    public function setMunicipioEstrangeiro($municipioEstrangeiro)
    {
        $this->municipioEstrangeiro = $municipioEstrangeiro;
        return $this;
    }

    /**
     * @return Pais
     */
    public function getPaisResidencia()
    {
        return $this->paisResidencia;
    }

    /**
     * @param Pais $paisResidencia
     * @return Aluno
     */
    public function setPaisResidencia($paisResidencia)
    {
        $this->paisResidencia = $paisResidencia;
        return $this;
    }

    /**
     * @return int
     */
    public function getLocalizacaodiferenciada()
    {
        return $this->localizacaodiferenciada;
    }

    /**
     * @param int $localizacaodiferenciada
     * @return Aluno
     */
    public function setLocalizacaodiferenciada($localizacaodiferenciada)
    {
        $this->localizacaodiferenciada = $localizacaodiferenciada;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTipoCertidao()
    {
        return $this->tipoCertidao;
    }

    /**
     * @param mixed $tipoCertidao
     * @return Aluno
     */
    public function setTipoCertidao($tipoCertidao)
    {
        $this->tipoCertidao = $tipoCertidao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumeroCertidao()
    {
        return $this->numeroCertidao;
    }

    /**
     * @param mixed $numeroCertidao
     * @return Aluno
     */
    public function setNumeroCertidao($numeroCertidao)
    {
        $this->numeroCertidao = trim($numeroCertidao);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLivroCertidao()
    {
        return $this->livroCertidao;
    }

    /**
     * @param mixed $livroCertidao
     * @return Aluno
     */
    public function setLivroCertidao($livroCertidao)
    {
        $this->livroCertidao = trim($livroCertidao);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFolhaCertidao()
    {
        return $this->folhaCertidao;
    }

    /**
     * @param mixed $folhaCertidao
     * @return Aluno
     */
    public function setFolhaCertidao($folhaCertidao)
    {
        $this->folhaCertidao = trim($folhaCertidao);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCartorioCertidao()
    {
        return $this->cartorioCertidao;
    }

    /**
     * @param mixed $cartorioCertidao
     * @return Aluno
     */
    public function setCartorioCertidao($cartorioCertidao)
    {
        $this->cartorioCertidao = $cartorioCertidao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUfCertidao()
    {
        return $this->ufCertidao;
    }

    /**
     * @param mixed $ufCertidao
     * @return Aluno
     */
    public function setUfCertidao($ufCertidao)
    {
        $this->ufCertidao = $ufCertidao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMunicipioCertidao()
    {
        return $this->municipioCertidao;
    }

    /**
     * @param mixed $municipioCertidao
     * @return Aluno
     */
    public function setMunicipioCertidao($municipioCertidao)
    {
        $this->municipioCertidao = $municipioCertidao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMatriculaCeridao()
    {
        return $this->matriculaCeridao;
    }

    /**
     * @param mixed $matriculaCeridao
     * @return Aluno
     */
    public function setMatriculaCeridao($matriculaCeridao)
    {
        $this->matriculaCeridao = trim($matriculaCeridao);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassaporte()
    {
        return $this->passaporte;
    }

    /**
     * @param mixed $passaporte
     * @return Aluno
     */
    public function setPassaporte($passaporte)
    {
        $this->passaporte = trim($passaporte);
        return $this;
    }

    /**
     * @return DBDate
     */
    public function getIdentidadeExpedicao()
    {
        return $this->identidadeExpedicao;
    }

    /**
     * @param DBDate $identidadeExpedicao
     * @return Aluno
     */
    public function setIdentidadeExpedicao($identidadeExpedicao)
    {
        $this->identidadeExpedicao = $identidadeExpedicao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentidadeComplemento()
    {
        return $this->identidadeComplemento;
    }

    /**
     * @param mixed $identidadeComplemento
     * @return Aluno
     */
    public function setIdentidadeComplemento($identidadeComplemento)
    {
        $this->identidadeComplemento = trim($identidadeComplemento);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentidadeOrgaoEmissor()
    {
        return $this->identidadeOrgaoEmissor;
    }

    /**
     * @param mixed $identidadeOrgaoEmissor
     * @return Aluno
     */
    public function setIdentidadeOrgaoEmissor($identidadeOrgaoEmissor)
    {
        $this->identidadeOrgaoEmissor = $identidadeOrgaoEmissor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentidadeUF()
    {
        return $this->identidadeUF;
    }

    /**
     * @param mixed $identidadeUF
     * @return Aluno
     */
    public function setIdentidadeUF($identidadeUF)
    {
        $this->identidadeUF = $identidadeUF;
        return $this;
    }


    /**
     * @param array $state
     * @return Aluno
     * @throws Exception
     */
    public static function fromState(array $state)
    {
        $self = new self();
        if (array_key_exists('ed47_i_codigo', $state)) {
            $self->setCodigo($state['ed47_i_codigo']);
        }
        if (array_key_exists('ed47_v_nome', $state)) {
            $self->setNome($state['ed47_v_nome']);
        }
        if (array_key_exists('ed47_v_nomesocial', $state)) {
            $self->setNomeSocial($state['ed47_v_nomesocial']);
        }
        if (array_key_exists('ed47_v_ender', $state)) {
            $self->setEndereco($state['ed47_v_ender']);
        }
        if (array_key_exists('ed47_v_compl', $state)) {
            $self->setComplemento($state['ed47_v_compl']);
        }
        if (array_key_exists('ed47_v_bairro', $state)) {
            $self->setBairro($state['ed47_v_bairro']);
        }
        if (array_key_exists('ed47_v_cep', $state)) {
            $self->setCep($state['ed47_v_cep']);
        }
        if (array_key_exists('ed47_c_raca', $state)) {
            $self->setRaca($state['ed47_c_raca']);
        }

        if (array_key_exists('ed47_v_cxpostal', $state)) {
            $self->setCaixaPostal($state['ed47_v_cxpostal']);
        }
        if (array_key_exists('ed47_v_telef', $state)) {
            $self->setTelefone($state['ed47_v_telef']);
        }
        if (array_key_exists('ed47_d_cadast', $state)) {
            $dataCadastro = !empty($state['ed47_d_cadast']) ? new DBDate($state['ed47_d_cadast']) : null;
            $self->setDataCadastro($dataCadastro);
        }
        if (array_key_exists('ed47_v_ident', $state)) {
            $self->setIdentidade($state['ed47_v_ident']);
        }
        if (array_key_exists('ed47_i_login', $state)) {
            $self->setLogin($state['ed47_i_login']);
        }
        if (array_key_exists('ed47_c_nomeresp', $state)) {
            $self->setNomeResponsavel($state['ed47_c_nomeresp']);
        }
        if (array_key_exists('ed47_c_emailresp', $state)) {
            $self->setEmail($state['ed47_c_emailresp']);
        }
        if (array_key_exists('ed47_t_obs', $state)) {
            $self->setObservacao($state['ed47_t_obs']);
        }
        if (array_key_exists('ed47_c_transporte', $state)) {
            $self->setTransporteResponsavel($state['ed47_c_transporte']);
        }
        if (array_key_exists('ed47_c_zona', $state)) {
            $self->setZonaResidencia($state['ed47_c_zona']);
        }
        if (array_key_exists('ed47_c_certidaotipo', $state)) {
            $self->setTipoCertidao($state['ed47_c_certidaotipo']);
        }
        if (array_key_exists('ed47_c_certidaonum', $state)) {
            $self->setNumeroCertidao($state['ed47_c_certidaonum']);
        }

        if (array_key_exists('ed47_c_certidaolivro', $state)) {
            $self->setLivroCertidao($state['ed47_c_certidaolivro']);
        }
        if (array_key_exists('ed47_c_certidaofolha', $state)) {
            $self->setFolhaCertidao($state['ed47_c_certidaofolha']);
        }

        if (array_key_exists('ed47_c_certidaodata', $state)) {
            $self->setDataCertidao($state['ed47_c_certidaodata']);
        }
        if (array_key_exists('ed47_c_nis', $state)) {
            $self->setNis($state['ed47_c_nis']);
        }
        if (array_key_exists('ed47_c_bolsafamilia', $state)) {
            $self->setBolsafamilia($state['ed47_c_bolsafamilia'] === 'S');
        }
        if (array_key_exists('ed47_c_passivo', $state)) {
            $self->setAlunoPassivo($state['ed47_c_passivo'] === 'S');
        }
        if (array_key_exists('ed47_d_dtemissao', $state)) {
            $cnhDataEmissao = !empty($state['ed47_d_dtemissao']) ? new DBDate($state['ed47_d_dtemissao']) : null;
            $self->setCnhDataEmissao($cnhDataEmissao);
        }
        if (array_key_exists('ed47_d_dthabilitacao', $state)) {
            $cnhData = !empty($state['ed47_d_dthabilitacao']) ? new DBDate($state['ed47_d_dthabilitacao']) : null;
            $self->setCnhDataHabilitacao($cnhData);
        }
        if (array_key_exists('ed47_d_dtvencimento', $state)) {
            $cnhData = !empty($state['ed47_d_dtvencimento']) ? new DBDate($state['ed47_d_dtvencimento']) : null;
            $self->setCnhDataVencimento($cnhData);
        }
        if (array_key_exists('ed47_d_nasc', $state)) {
            $dataNascimento = !empty($state['ed47_d_nasc']) ? new DBDate($state['ed47_d_nasc']) : null;
            $self->setDataNascimento($dataNascimento);
        }
        if (array_key_exists('ed47_d_ultalt', $state)) {
            $ultimaAlteracao = !empty($state['ed47_d_ultalt']) ? new DBDate($state['ed47_d_ultalt']) : null;
            $self->setUltimaAlteracao($ultimaAlteracao);
        }
        if (array_key_exists('ed47_i_estciv', $state)) {
            $self->setEstadoCivil($state['ed47_i_estciv']);
        }
        if (array_key_exists('ed47_i_nacion', $state)) {
            $self->setNacionalidade($state['ed47_i_nacion']);
        }
        if (array_key_exists('ed47_v_categoria', $state)) {
            $self->setCnhCategoria($state['ed47_v_categoria']);
        }
        if (array_key_exists('ed47_v_cnh', $state)) {
            $self->setCnhNumero($state['ed47_v_cnh']);
        }
        if (array_key_exists('ed47_v_contato', $state)) {
            $self->setTipoContato($state['ed47_v_contato']);
        }
        if (array_key_exists('ed47_v_cpf', $state)) {
            $self->setCpf($state['ed47_v_cpf']);
        }
        if (array_key_exists('ed47_v_email', $state)) {
            $self->setEmail($state['ed47_v_email']);
        }
        if (array_key_exists('ed47_v_fax', $state)) {
            $self->setFax($state['ed47_v_fax']);
        }
        if (array_key_exists('ed47_v_hora', $state)) {
            $self->setHora($state['ed47_v_hora']);
        }
        if (array_key_exists('ed47_v_mae', $state)) {
            $self->setMae($state['ed47_v_mae']);
        }
        if (array_key_exists('ed47_v_pai', $state)) {
            $self->setPai($state['ed47_v_pai']);
        }
        if (array_key_exists('ed47_v_profis', $state)) {
            $self->setProfissao($state['ed47_v_profis']);
        }
        if (array_key_exists('ed47_v_sexo', $state)) {
            $self->setSexo($state['ed47_v_sexo']);
        }
        if (array_key_exists('ed47_v_telcel', $state)) {
            $self->setTelefoneCelular($state['ed47_v_telcel']);
        }
        if (array_key_exists('ed47_c_foto', $state)) {
            $self->setFoto($state['ed47_c_foto']);
        }
        if (array_key_exists('ed47_c_codigoinep', $state)) {
            $self->setCodigoInep($state['ed47_c_codigoinep']);
        }
        if (array_key_exists('ed47_i_pais', $state)) {
            $self->setPais(PaisRegistry::get($state['ed47_i_pais']));
        }
        if (array_key_exists('ed47_d_identdtexp', $state)) {
            $self->setIdentidade($state['ed47_d_identdtexp']);
        }
        if (array_key_exists('ed47_v_identcompl', $state)) {
            $self->setIdentidadeComplemento($state['ed47_v_identcompl']);
        }
        if (array_key_exists('ed47_c_passaporte', $state)) {
            $self->setPassaporte($state['ed47_c_passaporte']);
        }
        if (array_key_exists('ed47_c_numero', $state)) {
            $self->setNumero($state['ed47_c_numero']);
        }
        if (array_key_exists('ed47_c_atenddifer', $state)) {
            $self->setRecebeEscolarizacaoEspecial($state['ed47_c_atenddifer']);
        }
        if (array_key_exists('ed47_i_filiacao', $state)) {
            $self->setFiliacao($state['ed47_i_filiacao']);
        }
        if (array_key_exists('ed47_i_censoufnat', $state)) {
            $self->setCensoUfNascimento($state['ed47_i_censoufnat']);
        }
        if (array_key_exists('ed47_i_censomunicnat', $state)) {
            $self->setCensoMunicipioNascimento($state['ed47_i_censomunicnat']);
        }
        if (array_key_exists('ed47_i_censoorgemissrg', $state)) {
            $self->setIdentidadeOrgaoEmissor($state['ed47_i_censoorgemissrg']);
        }
        if (array_key_exists('ed47_i_censoufident', $state)) {
            $self->setIdentidadeUF($state['ed47_i_censoufident']);
        }
        if (array_key_exists('ed47_i_censoufcert', $state)) {
            $self->setUfCertidao($state['ed47_i_censoufcert']);
        }
        if (array_key_exists('ed47_i_censomuniccert', $state)) {
            $self->setMunicipioCertidao($state['ed47_i_censomuniccert']);
        }
        if (array_key_exists('ed47_i_censoufend', $state)) {
            $self->setUfEndereco($state['ed47_i_censoufend']);
        }
        if (array_key_exists('ed47_i_censomunicend', $state)) {
            $self->setMunicipioEndereco($state['ed47_i_censomunicend']);
        }
        if (array_key_exists('ed47_i_transpublico', $state)) {
            $self->setUtilizaTransportePublico($state['ed47_i_transpublico'] == 1);
        }
        if (array_key_exists('ed47_i_atendespec', $state)) {
            $self->setAtendimentoEspecializado($state['ed47_i_atendespec']);
        }
        if (array_key_exists('ed47_i_censocartorio', $state)) {
            $self->setCartorioCertidao($state['ed47_i_censocartorio']);
        }
        if (array_key_exists('ed47_certidaomatricula', $state)) {
            $self->setMatriculaCeridao($state['ed47_certidaomatricula']);
        }
        if (array_key_exists('ed47_celularresponsavel', $state)) {
            $self->setCelularResponsavel($state['ed47_celularresponsavel']);
        }
        if (array_key_exists('ed47_situacaodocumentacao', $state)) {
            $self->setSituacaoDocumentacao($state['ed47_situacaodocumentacao']);
        }
        if (array_key_exists('ed47_cartaosus', $state)) {
            $self->setCartaoSus($state['ed47_cartaosus']);
        }
        if (array_key_exists('ed47_tiposanguineo', $state)) {
            $self->setTipoSanguineo($state['ed47_tiposanguineo']);
        }
        if (array_key_exists('ed47_municipioestrangeiro', $state)) {
            $self->setMunicipioEstrangeiro($state['ed47_municipioestrangeiro']);
        }
        if (array_key_exists('ed47_paisresidencia', $state)) {
            $self->setPaisResidencia(PaisRegistry::get($state['ed47_paisresidencia']));
        }
        if (array_key_exists('ed47_localizacaodiferenciada', $state)) {
            $self->setLocalizacaodiferenciada($state['ed47_localizacaodiferenciada']);
        }

        AlunoRegistry::set($self);
        return $self;
    }

    /**
     * @param array $necessidades
     */
    public function setNecessidades(array $necessidades)
    {
        $this->necessidadeEspecial = $necessidades;
    }

    /**
     * @param AlunoRecursoNecessarioAvaliacaoInep[] $recursos
     */
    public function setRecursoNecessarioAvaliacaoInep(array $recursos)
    {
        $this->recursoNecessarioAvaliacaoInep = $recursos;
    }

    public function getNecessidadesEspeciais()
    {
        return $this->necessidadeEspecial;
    }
}
