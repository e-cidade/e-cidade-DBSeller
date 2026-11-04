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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model;

/**
 * Class Registro30
 * @package ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model
 */
class Registro30
{
    const PROFISSIONAL = 333;
    const ALUNO = 666;

    const TIPO_REGISTRO = '30';
    private $tipoRegistro = self::TIPO_REGISTRO;
    private $codigoInepEscola;
    private $codigoPessoa;
    private $codigoInep;
    private $cpf;
    private $nome;
    private $dataNascimento;
    /**
     * @var integer
     */
    private $filiacao;
    private $filiacao1;
    private $filiacao2;
    private $sexo;
    private $corRaca;
    private $nacionalidade;
    private $paisNacionalidade;
    private $municipioNascimento;

    private $visaoMonocular;

    /**
     * Deficiência, transtorno do espectro autista ou altas habilidades/superdotação
     */
    private $deficienciaOuAltismoOuSuperdotacao = 0;

    /**
     * Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação.
     */
    private $cegueira;
    private $baixaVisao;
    private $surdez;
    private $deficienciaAuditiva;
    private $surdocegueira;
    private $deficienciaFisica;
    private $deficienciaintelectual;
    private $deficienciaMultipla;
    private $transtornoAutista;
    private $superdotacao;

    /**
     * Recursos necessários para uso do(a) aluno(a) e para a participação em avaliações do Inep (Saeb)
     */
    private $auxilioLedor;
    private $auxilioTranscricao;
    private $guiaInterprete;
    private $tradutorInterpreteLibras;
    private $leituraLabial;
    private $provaAmpliada;
    private $provaSuperampliada;
    private $audioDeficienteVisual;
    private $provaLinguaPortuguesaSegundaLingua;
    private $provaVideoLibras;
    private $provaBraille;
    private $nenhumRecurso;

    //private $nis;
    private $certidaoNascimento;
    private $justificativaFaltaDocumentacao;
    private $paisResidencia;
    private $cep;
    private $municipioResidencia;
    private $zonaResidencia;
    private $localizacaoDiferenciada;
    private $escolaridade;
    private $tipoEnsinoMedio;
    private $nomeCurso1;
    private $codigoCurso1;
    private $anoConclusao1;
    private $instituicaoSuperior1;
    private $ativo1 = true;
    private $nomeCurso2;
    private $codigoCurso2;
    private $anoConclusao2;
    private $instituicaoSuperior2;
    private $ativo2 = true;
    private $nomeCurso3;
    private $codigoCurso3;
    private $anoConclusao3;
    private $instituicaoSuperior3;
    private $ativo3 = true;

    /**
     * Formação/Complementação pedagógica
     */
    private $componenteCurricular1;
    private $componenteCurricular2;
    private $componenteCurricular3;

    /**
     * Pós-Graduações concluídas
     */
    //private $especializacao;
    //private $mestrado;
    //private $doutorado;
    private $nenhumaPos = null;
    /**
     * Outros cursos específicos (Formação continuada com mínimo de 80 horas)
     */
    private $creche;
    private $preEscola;
    private $anosIniciais;
    private $anosFinais;
    private $ensinoMedio;
    private $eja;
    private $educacaoEspecial;
    private $educacaoIndigena;
    private $educacaoCampo;
    private $educacaoAmbiental;
    private $educacaoDireitosHumanos;
    private $educacaoBilingueSurdos;
    private $generoDiversidadeSexual;
    private $direitosCriancaAdolescente;
    private $educacaoEtnicoRaciais;
    private $gestaoEscolar;
    private $outros;
    private $nenhumCurso;

    private $email;
    private $posGraudacoes = [];
    private $tipoPos1 = null;
    private $areaPos1 = null;
    private $anoConclusaoPos1 = null;
    private $tipoPos2 = null;
    private $areaPos2 = null;
    private $anoConclusaoPos2 = null;
    private $tipoPos3 = null;
    private $areaPos3 = null;
    private $anoConclusaoPos3 = null;
    private $tipoPos4 = null;
    private $areaPos4 = null;
    private $anoConclusaoPos4 = null;
    private $tipoPos5 = null;
    private $areaPos5 = null;
    private $anoConclusaoPos5 = null;
    private $tipoPos6 = null;
    private $areaPos6 = null;
    private $anoConclusaoPos6 = null;

    private $educacaoTIC;

    /**
     * @return mixed
     */
    public function getEducacaoTIC()
    {
        return $this->educacaoTIC;
    }

    /**
     * @param mixed $educacaoTIC
     * @return Registro30
     */
    public function setEducacaoTIC($educacaoTIC)
    {
        $this->educacaoTIC = $educacaoTIC;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getEducacaoBilingueSurdos()
    {
        return $this->educacaoBilingueSurdos;
    }

    /**
     * @param mixed $educacaoBilingueSurdos
     * @return Registro30
     */
    public function setEducacaoBilingueSurdos($educacaoBilingueSurdos)
    {
        $this->educacaoBilingueSurdos = $educacaoBilingueSurdos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisaoMonocular()
    {
        return $this->visaoMonocular;
    }

    /**
     * @param mixed $visaoMonocular
     * @return Registro30
     */
    public function setVisaoMonocular($visaoMonocular)
    {
        $this->visaoMonocular = $visaoMonocular;
        return $this;
    }


    /**
     * @return string
     */
    public function getTipoRegistro()
    {
        return $this->tipoRegistro;
    }

    /**
     * @param string $tipoRegistro
     * @return Registro30
     */
    public function setTipoRegistro($tipoRegistro)
    {
        $this->tipoRegistro = $tipoRegistro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoInepEscola()
    {
        return $this->codigoInepEscola;
    }

    /**
     * @param mixed $codigoInepEscola
     * @return Registro30
     */
    public function setCodigoInepEscola($codigoInepEscola)
    {
        $this->codigoInepEscola = $codigoInepEscola;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoPessoa()
    {
        return $this->codigoPessoa;
    }

    /**
     * @param mixed $codigoPessoa
     * @return Registro30
     */
    public function setCodigoPessoa($codigoPessoa)
    {
        $this->codigoPessoa = $codigoPessoa;
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
     * @return Registro30
     */
    public function setCodigoInep($codigoInep)
    {
        $this->codigoInep = $codigoInep;
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
     * @return Registro30
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     * @return Registro30
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    /**
     * @param mixed $dataNascimento
     * @return Registro30
     */
    public function setDataNascimento($dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFiliacao1()
    {
        return $this->filiacao1;
    }

    /**
     * @param mixed $filiacao1
     * @return Registro30
     */
    public function setFiliacao1($filiacao1)
    {
        $this->filiacao1 = $filiacao1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFiliacao2()
    {
        return $this->filiacao2;
    }

    /**
     * @param mixed $filiacao2
     * @return Registro30
     */
    public function setFiliacao2($filiacao2)
    {
        $this->filiacao2 = $filiacao2;
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
     * @return Registro30
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCorRaca()
    {
        return $this->corRaca;
    }

    /**
     * @param mixed $corRaca
     * @return Registro30
     */
    public function setCorRaca($corRaca)
    {
        $this->corRaca = $corRaca;
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
     * @return Registro30
     */
    public function setNacionalidade($nacionalidade)
    {
        $this->nacionalidade = $nacionalidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaisNacionalidade()
    {
        return $this->paisNacionalidade;
    }

    /**
     * @param mixed $paisNacionalidade
     * @return Registro30
     */
    public function setPaisNacionalidade($paisNacionalidade)
    {
        $this->paisNacionalidade = $paisNacionalidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMunicipioNascimento()
    {
        return $this->municipioNascimento;
    }

    /**
     * @param mixed $municipioNascimento
     * @return Registro30
     */
    public function setMunicipioNascimento($municipioNascimento)
    {
        $this->municipioNascimento = $municipioNascimento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeficienciaOuAltismoOuSuperdotacao()
    {
        return $this->deficienciaOuAltismoOuSuperdotacao;
    }

    /**
     * @param mixed $deficienciaOuAltismoOuSuperdotacao
     * @return Registro30
     */
    public function setDeficienciaOuAltismoOuSuperdotacao($deficienciaOuAltismoOuSuperdotacao)
    {
        $this->deficienciaOuAltismoOuSuperdotacao = $deficienciaOuAltismoOuSuperdotacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCegueira()
    {
        return $this->cegueira;
    }

    /**
     * @param mixed $cegueira
     * @return Registro30
     */
    public function setCegueira($cegueira)
    {
        $this->cegueira = $cegueira;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaixaVisao()
    {
        return $this->baixaVisao;
    }

    /**
     * @param mixed $baixaVisao
     * @return Registro30
     */
    public function setBaixaVisao($baixaVisao)
    {
        $this->baixaVisao = $baixaVisao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSurdez()
    {
        return $this->surdez;
    }

    /**
     * @param mixed $surdez
     * @return Registro30
     */
    public function setSurdez($surdez)
    {
        $this->surdez = $surdez;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeficienciaAuditiva()
    {
        return $this->deficienciaAuditiva;
    }

    /**
     * @param mixed $deficienciaAuditiva
     * @return Registro30
     */
    public function setDeficienciaAuditiva($deficienciaAuditiva)
    {
        $this->deficienciaAuditiva = $deficienciaAuditiva;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSurdocegueira()
    {
        return $this->surdocegueira;
    }

    /**
     * @param mixed $surdocegueira
     * @return Registro30
     */
    public function setSurdocegueira($surdocegueira)
    {
        $this->surdocegueira = $surdocegueira;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeficienciaFisica()
    {
        return $this->deficienciaFisica;
    }

    /**
     * @param mixed $deficienciaFisica
     * @return Registro30
     */
    public function setDeficienciaFisica($deficienciaFisica)
    {
        $this->deficienciaFisica = $deficienciaFisica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeficienciaintelectual()
    {
        return $this->deficienciaintelectual;
    }

    /**
     * @param mixed $deficienciaintelectual
     * @return Registro30
     */
    public function setDeficienciaintelectual($deficienciaintelectual)
    {
        $this->deficienciaintelectual = $deficienciaintelectual;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeficienciaMultipla()
    {
        return $this->deficienciaMultipla;
    }

    /**
     * @param mixed $deficienciaMultipla
     * @return Registro30
     */
    public function setDeficienciaMultipla($deficienciaMultipla)
    {
        $this->deficienciaMultipla = $deficienciaMultipla;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranstornoAutista()
    {
        return $this->transtornoAutista;
    }

    /**
     * @param mixed $transtornoAutista
     * @return Registro30
     */
    public function setTranstornoAutista($transtornoAutista)
    {
        $this->transtornoAutista = $transtornoAutista;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSuperdotacao()
    {
        return $this->superdotacao;
    }

    /**
     * @param mixed $superdotacao
     * @return Registro30
     */
    public function setSuperdotacao($superdotacao)
    {
        $this->superdotacao = $superdotacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuxilioLedor()
    {
        return $this->auxilioLedor;
    }

    /**
     * @param mixed $auxilioLedor
     * @return Registro30
     */
    public function setAuxilioLedor($auxilioLedor)
    {
        $this->auxilioLedor = $auxilioLedor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuxilioTranscricao()
    {
        return $this->auxilioTranscricao;
    }

    /**
     * @param mixed $auxilioTranscricao
     * @return Registro30
     */
    public function setAuxilioTranscricao($auxilioTranscricao)
    {
        $this->auxilioTranscricao = $auxilioTranscricao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGuiaInterprete()
    {
        return $this->guiaInterprete;
    }

    /**
     * @param mixed $guiaInterprete
     * @return Registro30
     */
    public function setGuiaInterprete($guiaInterprete)
    {
        $this->guiaInterprete = $guiaInterprete;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTradutorInterpreteLibras()
    {
        return $this->tradutorInterpreteLibras;
    }

    /**
     * @param mixed $tradutorInterpreteLibras
     * @return Registro30
     */
    public function setTradutorInterpreteLibras($tradutorInterpreteLibras)
    {
        $this->tradutorInterpreteLibras = $tradutorInterpreteLibras;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLeituraLabial()
    {
        return $this->leituraLabial;
    }

    /**
     * @param mixed $leituraLabial
     * @return Registro30
     */
    public function setLeituraLabial($leituraLabial)
    {
        $this->leituraLabial = $leituraLabial;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProvaAmpliada()
    {
        return $this->provaAmpliada;
    }

    /**
     * @param mixed $provaAmpliada
     * @return Registro30
     */
    public function setProvaAmpliada($provaAmpliada)
    {
        $this->provaAmpliada = $provaAmpliada;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProvaSuperampliada()
    {
        return $this->provaSuperampliada;
    }

    /**
     * @param mixed $provaSuperampliada
     * @return Registro30
     */
    public function setProvaSuperampliada($provaSuperampliada)
    {
        $this->provaSuperampliada = $provaSuperampliada;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAudioDeficienteVisual()
    {
        return $this->audioDeficienteVisual;
    }

    /**
     * @param mixed $audioDeficienteVisual
     * @return Registro30
     */
    public function setAudioDeficienteVisual($audioDeficienteVisual)
    {
        $this->audioDeficienteVisual = $audioDeficienteVisual;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProvaLinguaPortuguesaSegundaLingua()
    {
        return $this->provaLinguaPortuguesaSegundaLingua;
    }

    /**
     * @param mixed $provaLinguaPortuguesaSegundaLingua
     * @return Registro30
     */
    public function setProvaLinguaPortuguesaSegundaLingua($provaLinguaPortuguesaSegundaLingua)
    {
        $this->provaLinguaPortuguesaSegundaLingua = $provaLinguaPortuguesaSegundaLingua;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProvaVideoLibras()
    {
        return $this->provaVideoLibras;
    }

    /**
     * @param mixed $provaVideoLibras
     * @return Registro30
     */
    public function setProvaVideoLibras($provaVideoLibras)
    {
        $this->provaVideoLibras = $provaVideoLibras;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProvaBraille()
    {
        return $this->provaBraille;
    }

    /**
     * @param mixed $provaBraille
     * @return Registro30
     */
    public function setProvaBraille($provaBraille)
    {
        $this->provaBraille = $provaBraille;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNenhumRecurso()
    {
        return $this->nenhumRecurso;
    }

    /**
     * @param mixed $nenhumRecurso
     * @return Registro30
     */
    public function setNenhumRecurso($nenhumRecurso)
    {
        $this->nenhumRecurso = $nenhumRecurso;
        return $this;
    }

    // /**
    //  * @return mixed
    //  */
    // public function getNis()
    // {
    //     return $this->nis;
    // }

    // /**
    //  * @param mixed $nis
    //  * @return Registro30
    //  */
    // public function setNis($nis)
    // {
    //     $this->nis = $nis;
    //     return $this;
    // }

    /**
     * @return mixed
     */
    public function getCertidaoNascimento()
    {
        return $this->certidaoNascimento;
    }

    /**
     * @param mixed $certidaoNascimento
     * @return Registro30
     */
    public function setCertidaoNascimento($certidaoNascimento)
    {
        $this->certidaoNascimento = $certidaoNascimento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJustificativaFaltaDocumentacao()
    {
        return $this->justificativaFaltaDocumentacao;
    }

    /**
     * @param mixed $justificativaFaltaDocumentacao
     * @return Registro30
     */
    public function setJustificativaFaltaDocumentacao($justificativaFaltaDocumentacao)
    {
        $this->justificativaFaltaDocumentacao = $justificativaFaltaDocumentacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaisResidencia()
    {
        return $this->paisResidencia;
    }

    /**
     * @param mixed $paisResidencia
     * @return Registro30
     */
    public function setPaisResidencia($paisResidencia)
    {
        $this->paisResidencia = $paisResidencia;
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
     * @return Registro30
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMunicipioResidencia()
    {
        return $this->municipioResidencia;
    }

    /**
     * @param mixed $municipioResidencia
     * @return Registro30
     */
    public function setMunicipioResidencia($municipioResidencia)
    {
        $this->municipioResidencia = $municipioResidencia;
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
     * @return Registro30
     */
    public function setZonaResidencia($zonaResidencia)
    {
        $this->zonaResidencia = $zonaResidencia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocalizacaoDiferenciada()
    {
        return $this->localizacaoDiferenciada;
    }

    /**
     * @param mixed $localizacaoDiferenciada
     * @return Registro30
     */
    public function setLocalizacaoDiferenciada($localizacaoDiferenciada)
    {
        $this->localizacaoDiferenciada = $localizacaoDiferenciada;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEscolaridade()
    {
        return $this->escolaridade;
    }

    /**
     * @param mixed $escolaridade
     * @return Registro30
     */
    public function setEscolaridade($escolaridade)
    {
        $this->escolaridade = $escolaridade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTipoEnsinoMedio()
    {
        return $this->tipoEnsinoMedio;
    }

    /**
     * @param mixed $tipoEnsinoMedio
     * @return Registro30
     */
    public function setTipoEnsinoMedio($tipoEnsinoMedio)
    {
        $this->tipoEnsinoMedio = $tipoEnsinoMedio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoCurso1()
    {
        return $this->codigoCurso1;
    }

    /**
     * @param mixed $codigoCurso1
     * @return Registro30
     */
    public function setCodigoCurso1($codigoCurso1)
    {
        $this->codigoCurso1 = $codigoCurso1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnoConclusao1()
    {
        return $this->anoConclusao1;
    }

    /**
     * @param mixed $anoConclusao1
     * @return Registro30
     */
    public function setAnoConclusao1($anoConclusao1)
    {
        $this->anoConclusao1 = $anoConclusao1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstituicaoSuperior1()
    {
        return $this->instituicaoSuperior1;
    }

    /**
     * @param mixed $instituicaoSuperior1
     * @return Registro30
     */
    public function setInstituicaoSuperior1($instituicaoSuperior1)
    {
        $this->instituicaoSuperior1 = $instituicaoSuperior1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoCurso2()
    {
        return $this->codigoCurso2;
    }

    /**
     * @param mixed $codigoCurso2
     * @return Registro30
     */
    public function setCodigoCurso2($codigoCurso2)
    {
        $this->codigoCurso2 = $codigoCurso2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnoConclusao2()
    {
        return $this->anoConclusao2;
    }

    /**
     * @param mixed $anoConclusao2
     * @return Registro30
     */
    public function setAnoConclusao2($anoConclusao2)
    {
        $this->anoConclusao2 = $anoConclusao2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstituicaoSuperior2()
    {
        return $this->instituicaoSuperior2;
    }

    /**
     * @param mixed $instituicaoSuperior2
     * @return Registro30
     */
    public function setInstituicaoSuperior2($instituicaoSuperior2)
    {
        $this->instituicaoSuperior2 = $instituicaoSuperior2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoCurso3()
    {
        return $this->codigoCurso3;
    }

    /**
     * @param mixed $codigoCurso3
     * @return Registro30
     */
    public function setCodigoCurso3($codigoCurso3)
    {
        $this->codigoCurso3 = $codigoCurso3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnoConclusao3()
    {
        return $this->anoConclusao3;
    }

    /**
     * @param mixed $anoConclusao3
     * @return Registro30
     */
    public function setAnoConclusao3($anoConclusao3)
    {
        $this->anoConclusao3 = $anoConclusao3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstituicaoSuperior3()
    {
        return $this->instituicaoSuperior3;
    }

    /**
     * @param mixed $instituicaoSuperior3
     * @return Registro30
     */
    public function setInstituicaoSuperior3($instituicaoSuperior3)
    {
        $this->instituicaoSuperior3 = $instituicaoSuperior3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComponenteCurricular1()
    {
        return $this->componenteCurricular1;
    }

    /**
     * @param mixed $componenteCurricular1
     * @return Registro30
     */
    public function setComponenteCurricular1($componenteCurricular1)
    {
        $this->componenteCurricular1 = $componenteCurricular1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComponenteCurricular2()
    {
        return $this->componenteCurricular2;
    }

    /**
     * @param mixed $componenteCurricular2
     * @return Registro30
     */
    public function setComponenteCurricular2($componenteCurricular2)
    {
        $this->componenteCurricular2 = $componenteCurricular2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComponenteCurricular3()
    {
        return $this->componenteCurricular3;
    }

    /**
     * @param mixed $componenteCurricular3
     * @return Registro30
     */
    public function setComponenteCurricular3($componenteCurricular3)
    {
        $this->componenteCurricular3 = $componenteCurricular3;
        return $this;
    }

    // /**
    //  * @return mixed
    //  */
    // public function getEspecializacao()
    // {
    //     return $this->especializacao;
    // }

    // /**
    //  * @param mixed $especializacao
    //  * @return Registro30
    //  */
    // public function setEspecializacao($especializacao)
    // {
    //     $this->especializacao = $especializacao;
    //     return $this;
    // }

    // /**
    //  * @return mixed
    //  */
    // public function getMestrado()
    // {
    //     return $this->mestrado;
    // }

    // /**
    //  * @param mixed $mestrado
    //  * @return Registro30
    //  */
    // public function setMestrado($mestrado)
    // {
    //     $this->mestrado = $mestrado;
    //     return $this;
    // }

    // /**
    //  * @return mixed
    //  */
    // public function getDoutorado()
    // {
    //     return $this->doutorado;
    // }

    // /**
    //  * @param mixed $doutorado
    //  * @return Registro30
    //  */
    // public function setDoutorado($doutorado)
    // {
    //     $this->doutorado = $doutorado;
    //     return $this;
    // }

    /**
     * @return mixed
     */
    public function getNenhumaPos()
    {
        return $this->nenhumaPos;
    }

    /**
     * @param mixed $nenhumaPos
     * @return Registro30
     */
    public function setNenhumaPos($nenhumaPos)
    {
        $this->nenhumaPos = $nenhumaPos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreche()
    {
        return $this->creche;
    }

    /**
     * @param mixed $creche
     * @return Registro30
     */
    public function setCreche($creche)
    {
        $this->creche = $creche;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPreEscola()
    {
        return $this->preEscola;
    }

    /**
     * @param mixed $preEscola
     * @return Registro30
     */
    public function setPreEscola($preEscola)
    {
        $this->preEscola = $preEscola;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnosIniciais()
    {
        return $this->anosIniciais;
    }

    /**
     * @param mixed $anosIniciais
     * @return Registro30
     */
    public function setAnosIniciais($anosIniciais)
    {
        $this->anosIniciais = $anosIniciais;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnosFinais()
    {
        return $this->anosFinais;
    }

    /**
     * @param mixed $anosFinais
     * @return Registro30
     */
    public function setAnosFinais($anosFinais)
    {
        $this->anosFinais = $anosFinais;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnsinoMedio()
    {
        return $this->ensinoMedio;
    }

    /**
     * @param mixed $ensinoMedio
     * @return Registro30
     */
    public function setEnsinoMedio($ensinoMedio)
    {
        $this->ensinoMedio = $ensinoMedio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEja()
    {
        return $this->eja;
    }

    /**
     * @param mixed $eja
     * @return Registro30
     */
    public function setEja($eja)
    {
        $this->eja = $eja;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEducacaoEspecial()
    {
        return $this->educacaoEspecial;
    }

    /**
     * @param mixed $educacaoEspecial
     * @return Registro30
     */
    public function setEducacaoEspecial($educacaoEspecial)
    {
        $this->educacaoEspecial = $educacaoEspecial;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEducacaoIndigena()
    {
        return $this->educacaoIndigena;
    }

    /**
     * @param mixed $educacaoIndigena
     * @return Registro30
     */
    public function setEducacaoIndigena($educacaoIndigena)
    {
        $this->educacaoIndigena = $educacaoIndigena;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEducacaoCampo()
    {
        return $this->educacaoCampo;
    }

    /**
     * @param mixed $educacaoCampo
     * @return Registro30
     */
    public function setEducacaoCampo($educacaoCampo)
    {
        $this->educacaoCampo = $educacaoCampo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEducacaoAmbiental()
    {
        return $this->educacaoAmbiental;
    }

    /**
     * @param mixed $educacaoAmbiental
     * @return Registro30
     */
    public function setEducacaoAmbiental($educacaoAmbiental)
    {
        $this->educacaoAmbiental = $educacaoAmbiental;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEducacaoDireitosHumanos()
    {
        return $this->educacaoDireitosHumanos;
    }

    /**
     * @param mixed $educacaoDireitosHumanos
     * @return Registro30
     */
    public function setEducacaoDireitosHumanos($educacaoDireitosHumanos)
    {
        $this->educacaoDireitosHumanos = $educacaoDireitosHumanos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeneroDiversidadeSexual()
    {
        return $this->generoDiversidadeSexual;
    }

    /**
     * @param mixed $generoDiversidadeSexual
     * @return Registro30
     */
    public function setGeneroDiversidadeSexual($generoDiversidadeSexual)
    {
        $this->generoDiversidadeSexual = $generoDiversidadeSexual;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDireitosCriancaAdolescente()
    {
        return $this->direitosCriancaAdolescente;
    }

    /**
     * @param mixed $direitosCriancaAdolescente
     * @return Registro30
     */
    public function setDireitosCriancaAdolescente($direitosCriancaAdolescente)
    {
        $this->direitosCriancaAdolescente = $direitosCriancaAdolescente;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEducacaoEtnicoRaciais()
    {
        return $this->educacaoEtnicoRaciais;
    }

    /**
     * @param mixed $educacaoEtnicoRaciais
     * @return Registro30
     */
    public function setEducacaoEtnicoRaciais($educacaoEtnicoRaciais)
    {
        $this->educacaoEtnicoRaciais = $educacaoEtnicoRaciais;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGestaoEscolar()
    {
        return $this->gestaoEscolar;
    }

    /**
     * @param mixed $gestaoEscolar
     * @return Registro30
     */
    public function setGestaoEscolar($gestaoEscolar)
    {
        $this->gestaoEscolar = $gestaoEscolar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutros()
    {
        return $this->outros;
    }

    /**
     * @param mixed $outros
     * @return Registro30
     */
    public function setOutros($outros)
    {
        $this->outros = $outros;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNenhumCurso()
    {
        return $this->nenhumCurso;
    }

    /**
     * @param mixed $nenhumCurso
     * @return Registro30
     */
    public function setNenhumCurso($nenhumCurso)
    {
        $this->nenhumCurso = $nenhumCurso;
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
     * @return Registro30
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function toArray()
    {
        return array(
            "tipoRegistro " => $this->getTipoRegistro(),
            "codigoInepEscola" => $this->getCodigoInepEscola(),
            "codigoPessoa" => $this->getCodigoPessoa(),
            "codigoInep" => $this->getCodigoInep(),
            "cpf" => $this->getCpf(),
            "nome" => $this->getNome(),
            "dataNascimento" => $this->getDataNascimento(),
            "filiacao" => $this->getFiliacao(),
            "filiacao1" => $this->getFiliacao1(),
            "filiacao2" => $this->getFiliacao2(),
            "sexo" => $this->getSexo(),
            "corRaca" => $this->getCorRaca(),
            "nacionalidade" => $this->getNacionalidade(),
            "paisNacionalidade" => $this->getPaisNacionalidade(),
            "municipioNascimento" => $this->getMunicipioNascimento(),
            "deficienciaOuAltismoOuSuperdotacao" => $this->getDeficienciaOuAltismoOuSuperdotacao(),
            "cegueira" => $this->getCegueira(),
            "baixaVisao" => $this->getBaixaVisao(),
            "surdez" => $this->getSurdez(),
            "deficienciaAuditiva" => $this->getDeficienciaAuditiva(),
            "surdocegueira" => $this->getSurdocegueira(),
            "deficienciaFisica" => $this->getDeficienciaFisica(),
            "deficienciaintelectual" => $this->getDeficienciaintelectual(),
            "deficienciaMultipla" => $this->getDeficienciaMultipla(),
            "transtornoAutista" => $this->getTranstornoAutista(),
            "superdotacao" => $this->getSuperdotacao(),
            "auxilioLedor" => $this->getAuxilioLedor(),
            "auxilioTranscricao" => $this->getAuxilioTranscricao(),
            "guiaInterprete" => $this->getGuiaInterprete(),
            "tradutorInterpreteLibras" => $this->getTradutorInterpreteLibras(),
            "leituraLabial" => $this->getLeituraLabial(),
            "provaAmpliada" => $this->getProvaAmpliada(),
            "provaSuperampliada" => $this->getProvaSuperampliada(),
            "audioDeficienteVisual" => $this->getAudioDeficienteVisual(),
            "provaLinguaPortuguesaSegundaLingua" => $this->getProvaLinguaPortuguesaSegundaLingua(),
            "provaVideoLibras" => $this->getProvaVideoLibras(),
            "provaBraille" => $this->getProvaBraille(),
            "nenhumRecurso" => $this->getNenhumRecurso(),
            "certidaoNascimento" => $this->getCertidaoNascimento(),
            "justificativaFaltaDocumentacao" => $this->getJustificativaFaltaDocumentacao(),
            "paisResidencia" => $this->getPaisResidencia(),
            "cep" => $this->getCep(),
            "municipioResidencia" => $this->getMunicipioResidencia(),
            "zonaResidencia" => $this->getZonaResidencia(),
            "localizacaoDiferenciada" => $this->getLocalizacaoDiferenciada(),
            "escolaridade" => $this->getEscolaridade(),
            "tipoEnsinoMedio" => $this->getTipoEnsinoMedio(),
            "codigoCurso1" => $this->getCodigoCurso1(),
            "anoConclusao1" => $this->getAnoConclusao1(),
            "instituicaoSuperior1" => $this->getInstituicaoSuperior1(),
            "codigoCurso2" => $this->getCodigoCurso2(),
            "anoConclusao2" => $this->getAnoConclusao2(),
            "instituicaoSuperior2" => $this->getInstituicaoSuperior2(),
            "codigoCurso3" => $this->getCodigoCurso3(),
            "anoConclusao3" => $this->getAnoConclusao3(),
            "instituicaoSuperior3" => $this->getInstituicaoSuperior3(),
            "componenteCurricular1" => $this->getComponenteCurricular1(),
            "componenteCurricular2" => $this->getComponenteCurricular2(),
            "componenteCurricular3" => $this->getComponenteCurricular3(),
            "tipoPos1" => $this->getTipoPos1(),
            "areaPos1" => $this->getAreaPos1(),
            "anoConclusaoPos1" => $this->getAnoConclusaoPos1(),
            "tipoPos2" => $this->getTipoPos2(),
            "areaPos2" => $this->getAreaPos2(),
            "anoConclusaoPos2" => $this->getAnoConclusaoPos2(),
            "tipoPos3" => $this->getTipoPos3(),
            "areaPos3" => $this->getAreaPos3(),
            "anoConclusaoPos3" => $this->getAnoConclusaoPos3(),
            "tipoPos4" => $this->getTipoPos4(),
            "areaPos4" => $this->getAreaPos4(),
            "anoConclusaoPos4" => $this->getAnoConclusaoPos4(),
            "tipoPos5" => $this->getTipoPos5(),
            "areaPos5" => $this->getAreaPos5(),
            "anoConclusaoPos5" => $this->getAnoConclusaoPos5(),
            "tipoPos6" => $this->getTipoPos6(),
            "areaPos6" => $this->getAreaPos6(),
            "anoConclusaoPos6" => $this->getAnoConclusaoPos6(),
            "nenhumaPos" => $this->getNenhumaPos(),
            "nenhumaPos" => $this->getNenhumaPos(),
            "creche" => $this->getCreche(),
            "preEscola" => $this->getPreEscola(),
            "anosIniciais" => $this->getAnosIniciais(),
            "anosFinais" => $this->getAnosFinais(),
            "ensinoMedio" => $this->getEnsinoMedio(),
            "eja" => $this->getEja(),
            "educacaoEspecial" => $this->getEducacaoEspecial(),
            "educacaoIndigena" => $this->getEducacaoIndigena(),
            "educacaoCampo" => $this->getEducacaoCampo(),
            "educacaoAmbiental" => $this->getEducacaoAmbiental(),
            "educacaoDireitosHumanos" => $this->getEducacaoDireitosHumanos(),
            "educacaoBilingueSurdos" => $this->getEducacaoBilingueSurdos(),
            "educacaoTIC" => $this->getEducacaoTIC(),
            "generoDiversidadeSexual" => $this->getGeneroDiversidadeSexual(),
            "direitosCriancaAdolescente" => $this->getDireitosCriancaAdolescente(),
            "educacaoEtnicoRaciais" => $this->getEducacaoEtnicoRaciais(),
            "gestaoEscolar" => $this->getGestaoEscolar(),
            "outros" => $this->getOutros(),
            "nenhumCurso" => $this->getNenhumCurso(),
            "email" => $this->getEmail(),
            "visaoMonocular" => $this->getVisaoMonocular()
        );
    }

    /**
     * @return int
     */
    public function getFiliacao()
    {
        return $this->filiacao;
    }

    /**
     * @param int $filiacao
     * @return Registro30
     */
    public function setFiliacao($filiacao)
    {
        $this->filiacao = $filiacao;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAtivo1()
    {
        return $this->ativo1;
    }

    /**
     * @param boolean $ativo1
     * @return Registro30
     */
    public function setAtivo1($ativo1)
    {
        $this->ativo1 = $ativo1;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAtivo2()
    {
        return $this->ativo2;
    }

    /**
     * @param boolean $ativo2
     * @return Registro30
     */
    public function setAtivo2($ativo2)
    {
        $this->ativo2 = $ativo2;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAtivo3()
    {
        return $this->ativo3;
    }

    /**
     * @param boolean $ativo3
     * @return Registro30
     */
    public function setAtivo3($ativo3)
    {
        $this->ativo3 = $ativo3;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeCurso1()
    {
        return $this->nomeCurso1;
    }

    /**
     * @param mixed $nomeCurso1
     * @return Registro30
     */
    public function setNomeCurso1($nomeCurso1)
    {
        $this->nomeCurso1 = $nomeCurso1;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeCurso2()
    {
        return $this->nomeCurso2;
    }

    /**
     * @param mixed $nomeCurso2
     * @return Registro30
     */
    public function setNomeCurso2($nomeCurso2)
    {
        $this->nomeCurso2 = $nomeCurso2;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeCurso3()
    {
        return $this->nomeCurso3;
    }

    /**
     * @param mixed $nomeCurso3
     * @return Registro30
     */
    public function setNomeCurso3($nomeCurso3)
    {
        $this->nomeCurso3 = $nomeCurso3;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosGraduacoes()
    {
        return $this->posGraudacoes;
    }

    /**
     * @param array $aPosGraduacoes
     * @return Registro30
     */
    public function setPosGraduacoes($aPosGraduacoes)
    {
        if ($this->getEscolaridade() != 6) {
            return;
        }

        if (empty($aPosGraduacoes)) {
            $this->setNenhumaPos(1);
        }

        $this->posGraudacoes = $aPosGraduacoes;

        foreach ($aPosGraduacoes as $key => $pos) {
            switch ($key + 1) {
                case 1:
                    $this->setTipoPos1($pos['ed183_tipoformacao']);
                    $this->setAreaPos1($pos['ed183_areaformacao']);
                    $this->setAnoConclusaoPos1($pos['ed183_anoconclusao']);
                    break;
                case 2:
                    $this->setTipoPos2($pos['ed183_tipoformacao']);
                    $this->setAreaPos2($pos['ed183_areaformacao']);
                    $this->setAnoConclusaoPos2($pos['ed183_anoconclusao']);
                    break;
                case 3:
                    $this->setTipoPos3($pos['ed183_tipoformacao']);
                    $this->setAreaPos3($pos['ed183_areaformacao']);
                    $this->setAnoConclusaoPos3($pos['ed183_anoconclusao']);
                    break;
                case 4:
                    $this->setTipoPos4($pos['ed183_tipoformacao']);
                    $this->setAreaPos4($pos['ed183_areaformacao']);
                    $this->setAnoConclusaoPos4($pos['ed183_anoconclusao']);
                    break;
                case 5:
                    $this->setTipoPos5($pos['ed183_tipoformacao']);
                    $this->setAreaPos5($pos['ed183_areaformacao']);
                    $this->setAnoConclusaoPos5($pos['ed183_anoconclusao']);
                    break;
                case 6:
                    $this->setTipoPos6($pos['ed183_tipoformacao']);
                    $this->setAreaPos6($pos['ed183_areaformacao']);
                    $this->setAnoConclusaoPos6($pos['ed183_anoconclusao']);
                    break;
            }
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTipoPos1()
    {
        return $this->tipoPos1;
    }

    /**
     * @param mixed $tipoPos1
     */
    public function setTipoPos1($tipoPos1)
    {
        $this->tipoPos1 = $tipoPos1;
    }

    /**
     * @return mixed
     */
    public function getAreaPos1()
    {
        return $this->areaPos1;
    }

    /**
     * @param mixed $areaPos1
     */
    public function setAreaPos1($areaPos1)
    {
        $this->areaPos1 = $areaPos1;
    }

    /**
     * @return mixed
     */
    public function getAnoConclusaoPos1()
    {
        return $this->anoConclusaoPos1;
    }

    /**
     * @param mixed $anoConclusaoPos1
     */
    public function setAnoConclusaoPos1($anoConclusaoPos1)
    {
        $this->anoConclusaoPos1 = $anoConclusaoPos1;
    }

    /**
     * @return mixed
     */
    public function getAnoConclusaoPos2()
    {
        return $this->anoConclusaoPos2;
    }

    /**
     * @param mixed $anoConclusaoPos2
     */
    public function setAnoConclusaoPos2($anoConclusaoPos2)
    {
        $this->anoConclusaoPos2 = $anoConclusaoPos2;
    }

    /**
     * @return mixed
     */
    public function getTipoPos2()
    {
        return $this->tipoPos2;
    }

    /**
     * @param mixed $tipoPos2
     */
    public function setTipoPos2($tipoPos2)
    {
        $this->tipoPos2 = $tipoPos2;
    }

    /**
     * @return mixed
     */
    public function getAreaPos2()
    {
        return $this->areaPos2;
    }

    /**
     * @param mixed $areaPos2
     */
    public function setAreaPos2($areaPos2)
    {
        $this->areaPos2 = $areaPos2;
    }

    /**
     * @return mixed
     */
    public function getTipoPos3()
    {
        return $this->tipoPos3;
    }

    /**
     * @param mixed $tipoPos3
     */
    public function setTipoPos3($tipoPos3)
    {
        $this->tipoPos3 = $tipoPos3;
    }

    /**
     * @return mixed
     */
    public function getAnoConclusaoPos3()
    {
        return $this->anoConclusaoPos3;
    }

    /**
     * @param mixed $anoConclusaoPos3
     */
    public function setAnoConclusaoPos3($anoConclusaoPos3)
    {
        $this->anoConclusaoPos3 = $anoConclusaoPos3;
    }

    /**
     * @return mixed
     */
    public function getAreaPos3()
    {
        return $this->areaPos3;
    }

    /**
     * @param mixed $areaPos3
     */
    public function setAreaPos3($areaPos3)
    {
        $this->areaPos3 = $areaPos3;
    }

    /**
     * @return mixed
     */
    public function getTipoPos4()
    {
        return $this->tipoPos4;
    }

    /**
     * @param mixed $tipoPos4
     */
    public function setTipoPos4($tipoPos4)
    {
        $this->tipoPos4 = $tipoPos4;
    }

    /**
     * @return mixed
     */
    public function getAreaPos4()
    {
        return $this->areaPos4;
    }

    /**
     * @param mixed $areaPos4
     */
    public function setAreaPos4($areaPos4)
    {
        $this->areaPos4 = $areaPos4;
    }

    /**
     * @return mixed
     */
    public function getAnoConclusaoPos4()
    {
        return $this->anoConclusaoPos4;
    }

    /**
     * @param mixed $anoConclusaoPos4
     */
    public function setAnoConclusaoPos4($anoConclusaoPos4)
    {
        $this->anoConclusaoPos4 = $anoConclusaoPos4;
    }

    /**
     * @return mixed
     */
    public function getTipoPos5()
    {
        return $this->tipoPos5;
    }

    /**
     * @param mixed $tipoPos5
     */
    public function setTipoPos5($tipoPos5)
    {
        $this->tipoPos5 = $tipoPos5;
    }

    /**
     * @return mixed
     */
    public function getAreaPos5()
    {
        return $this->areaPos5;
    }

    /**
     * @param mixed $areaPos5
     */
    public function setAreaPos5($areaPos5)
    {
        $this->areaPos5 = $areaPos5;
    }

    /**
     * @return mixed
     */
    public function getAnoConclusaoPos5()
    {
        return $this->anoConclusaoPos5;
    }

    /**
     * @param mixed $anoConclusaoPos5
     */
    public function setAnoConclusaoPos5($anoConclusaoPos5)
    {
        $this->anoConclusaoPos5 = $anoConclusaoPos5;
    }

    /**
     * @return mixed
     */
    public function getTipoPos6()
    {
        return $this->tipoPos6;
    }

    /**
     * @param mixed $tipoPos6
     */
    public function setTipoPos6($tipoPos6)
    {
        $this->tipoPos6 = $tipoPos6;
    }

    /**
     * @return mixed
     */
    public function getAreaPos6()
    {
        return $this->areaPos6;
    }

    /**
     * @param mixed $areaPos6
     */
    public function setAreaPos6($areaPos6)
    {
        $this->areaPos6 = $areaPos6;
    }

    /**
     * @return mixed
     */
    public function getAnoConclusaoPos6()
    {
        return $this->anoConclusaoPos6;
    }

    /**
     * @param mixed $anoConclusaoPos6
     */
    public function setAnoConclusaoPos6($anoConclusaoPos6)
    {
        $this->anoConclusaoPos6 = $anoConclusaoPos6;
    }
}
