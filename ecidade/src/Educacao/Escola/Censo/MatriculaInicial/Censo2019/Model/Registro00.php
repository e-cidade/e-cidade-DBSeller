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

class Registro00
{
    const TIPO_REGISTRO = '00';
    private $tipoRegistro = self::TIPO_REGISTRO;
    private $codigo;
    private $codigoInep;
    private $situacaoFuncionamento;
    private $dataIncicioAnoLetivo;
    private $dataFinalAnoLetivo;
    private $nomeEscola;
    private $cep;
    private $municipio;
    private $distrito;
    private $endereco;
    private $numero;
    private $complemento;
    private $bairro;
    private $ddd;
    private $telefone;
    private $outroTelefone;
    private $email;
    private $codigoOrgaoRegionalEnsino;
    private $zonaEscola;
    private $localizacaoDiferenciada;
    private $dependenciaAdministrativa;
    private $secretariaEducacao = 1;
    private $secretariaSeguranca = 0;
    private $secretariaSaude = 0;
    private $outroOrgaoAdministracaoPublica = 0;
    private $mantenedoraPrivada;
    private $mantenedoraSindicatos;
    private $mantenedoraOng;
    private $mantenedoraInstituicaoSemFimLucrativo;
    private $mantenedoraSistemaS;
    private $mantenedoraOscip;
    private $categoriaEscolaPrivada;

    private $secretariaEstadual = 0;
    private $secretariaMunicipal = 0;
    private $naoPossuiConvenio = 1;

    private $termoColaboracao = null;
    private $termoFormento = null;
    private $acordoCooperacao = null;
    private $contratoPrestacaoServico = null;
    private $termoCooperacaoTecnica = null;
    private $coonvenioCooperacao = null;

    private $atividadeComplementar = null;
    private $atividadeEducacional = null;
    private $crecheParcial = null;
    private $crecheIntegral = null;
    private $preEscolaParcial = null;
    private $preEscolaIntegral = null;
    private $FundamentalInicialParcial = null;
    private $FundamentalInicialIntegral = null;
    private $FundamentalFinalParcial= null;
    private $FundamentalFinalIntegral = null;

    private $medioParcial = null;
    private $medioIntegral = null;
    private $especialParcial = null;
    private $especialIntegral = null;
    private $ejaFundamental = null;
    private $ejaMedio = null;

    private $ProfissionalQualificacaoEnsinoFundamentalParcial = null;
    private $ProfissionalQualificacaoEnsinoFundamentalIntegral = null;

    private $ProfissionalQualificacaoNivelMedioParcial = null;
    private $ProfissionalQualificacaoNivelMedioIntegral = null;
    private $ProfissionalQualificacaoConcomitanteNivelMedioParcial = null;
    private $ProfissionalQualificacaoConcomitanteNivelMedioIntegral = null;
    private $ProfissionalQualificacaoIntercomplementarNivelMedioParcial = null;
    private $ProfissionalQualificacaoIntercomplementarNivelMedioIntegral = null;

    private $ProfissionalQualificacaoEnsinoMedioParcial = null;
    private $ProfissionalQualificacaoEnsinoIntegral = null;
    private $ProfissionalQualificacaoConcomitanteEnsinoMedioParcial = null;
    private $ProfissionalQualificacaoConcomitanteEnsinoMedioIntegral = null;
    private $ProfissionalQualificacaoIntercomplementarEnsinoMedioParcial = null;
    private $ProfissionalQualificacaoIntercomplementarEnsinoMedioIntegral = null;
    private $ProfissionalProfissionalEnsinoMedioParcial = null;
    private $ProfissionalProfissionalEnsinoIntegral = null;
    private $ProfissionalProfissionalConcomitanteEnsinoMedioParcial = null;
    private $ProfissionalProfissionalConcomitanteEnsinoMedioIntegral = null;
    private $ProfissionalProfissionalIntercomplementarEnsinoMedioParcial = null;
    private $ProfissionalProfissionalIntercomplementarEnsinoMedioIntegral = null;
    private $ProfissionalProfissionalSubsequente = null;

    private $ProfissionalProfissionalNivelMedioParcial = null;
    private $ProfissionalProfissionalNivelMedioIntegral = null;
    private $ProfissionalProfissionalConcomitanteNivelMedioParcial = null;
    private $ProfissionalProfissionalConcomitanteNivelMedioIntegral = null;
    private $ProfissionalProfissionalIntercomplementarNivelMedioParcial = null;
    private $ProfissionalProfissionalIntercomplementarNivelMedioIntegral = null;

    private $cnpjMantenedoraPrincipal;
    private $cnpjEscolaPrivada;
    private $regulamentacao;
    private $esferaFederal = 0;
    private $esferaEstadual = 0;
    private $esferaMunicipal = 0;
    private $unidadeVinculada = 0;
    private $codigoEscolaSede = null;
    private $codigoIES = null;
    private $uf;

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param mixed $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
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
     */
    public function setCodigoInep($codigoInep)
    {
        $this->codigoInep = $codigoInep;
    }

    /**
     * @return mixed
     */
    public function getSituacaoFuncionamento()
    {
        return $this->situacaoFuncionamento;
    }

    /**
     * @param mixed $situacaoFuncionamento
     */
    public function setSituacaoFuncionamento($situacaoFuncionamento)
    {
        $this->situacaoFuncionamento = $situacaoFuncionamento;
    }

    /**
     * @return mixed
     */
    public function getDataIncicioAnoLetivo()
    {
        return $this->dataIncicioAnoLetivo;
    }

    /**
     * @param mixed $dataIncicioAnoLetivo
     */
    public function setDataIncicioAnoLetivo($dataIncicioAnoLetivo)
    {
        $this->dataIncicioAnoLetivo = $dataIncicioAnoLetivo;
    }

    /**
     * @return mixed
     */
    public function getDataFinalAnoLetivo()
    {
        return $this->dataFinalAnoLetivo;
    }

    /**
     * @param mixed $dataFinalAnoLetivo
     */
    public function setDataFinalAnoLetivo($dataFinalAnoLetivo)
    {
        $this->dataFinalAnoLetivo = $dataFinalAnoLetivo;
    }

    /**
     * @return mixed
     */
    public function getNomeEscola()
    {
        return $this->nomeEscola;
    }

    /**
     * @param mixed $nomeEscola
     */
    public function setNomeEscola($nomeEscola)
    {
        $this->nomeEscola = $nomeEscola;
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
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
    }

    /**
     * @return mixed
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * @param mixed $municipio
     */
    public function setMunicipio($municipio)
    {
        $this->municipio = $municipio;
    }

    /**
     * @return mixed
     */
    public function getDistrito()
    {
        return $this->distrito;
    }

    /**
     * @param mixed $distrito
     */
    public function setDistrito($distrito)
    {
        $this->distrito = $distrito;
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
     * @param mixed $numero
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
     * @param mixed $complemento
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
     * @param mixed $bairro
     */
    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
    }

    /**
     * @return mixed
     */
    public function getDdd()
    {
        return $this->ddd;
    }

    /**
     * @param mixed $ddd
     */
    public function setDdd($ddd)
    {
        $this->ddd = $ddd;
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
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }

    /**
     * @return mixed
     */
    public function getOutroTelefone()
    {
        return $this->outroTelefone;
    }

    /**
     * @param mixed $outroTelefone
     */
    public function setOutroTelefone($outroTelefone)
    {
        $this->outroTelefone = $outroTelefone;
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
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getCodigoOrgaoRegionalEnsino()
    {
        return $this->codigoOrgaoRegionalEnsino;
    }

    /**
     * @param mixed $codigoOrgaoRegionalEnsino
     */
    public function setCodigoOrgaoRegionalEnsino($codigoOrgaoRegionalEnsino)
    {
        $this->codigoOrgaoRegionalEnsino = $codigoOrgaoRegionalEnsino;
    }

    /**
     * @return mixed
     */
    public function getZonaEscola()
    {
        return $this->zonaEscola;
    }

    /**
     * @param mixed $zonaEscola
     */
    public function setZonaEscola($zonaEscola)
    {
        $this->zonaEscola = $zonaEscola;
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
     */
    public function setLocalizacaoDiferenciada($localizacaoDiferenciada)
    {
        $this->localizacaoDiferenciada = $localizacaoDiferenciada;
    }

    /**
     * @return mixed
     */
    public function getDependenciaAdministrativa()
    {
        return $this->dependenciaAdministrativa;
    }

    /**
     * @param mixed $dependenciaAdministrativa
     */
    public function setDependenciaAdministrativa($dependenciaAdministrativa)
    {
        $this->dependenciaAdministrativa = $dependenciaAdministrativa;
    }

    /**
     * @return mixed
     */
    public function getSecretariaEducacao()
    {
        return $this->secretariaEducacao;
    }

    /**
     * @param mixed $secretariaEducacao
     */
    public function setSecretariaEducacao($secretariaEducacao)
    {
        $this->secretariaEducacao = $secretariaEducacao;
    }

    /**
     * @return mixed
     */
    public function getSecretariaSeguranca()
    {
        return $this->secretariaSeguranca;
    }

    /**
     * @param mixed $secretariaSeguranca
     */
    public function setSecretariaSeguranca($secretariaSeguranca)
    {
        $this->secretariaSeguranca = $secretariaSeguranca;
    }

    /**
     * @return mixed
     */
    public function getOutroOrgaoAdministracaoPublica()
    {
        return $this->outroOrgaoAdministracaoPublica;
    }

    /**
     * @param mixed $outroOrgaoAdministracaoPublica
     */
    public function setOutroOrgaoAdministracaoPublica($outroOrgaoAdministracaoPublica)
    {
        $this->outroOrgaoAdministracaoPublica = $outroOrgaoAdministracaoPublica;
    }

    /**
     * @return mixed
     */
    public function getMantenedoraPrivada()
    {
        return $this->mantenedoraPrivada;
    }

    /**
     * @param mixed $mantenedoraPrivada
     */
    public function setMantenedoraPrivada($mantenedoraPrivada)
    {
        $this->mantenedoraPrivada = $mantenedoraPrivada;
    }

    /**
     * @return mixed
     */
    public function getMantenedoraSindicatos()
    {
        return $this->mantenedoraSindicatos;
    }

    /**
     * @param mixed $mantenedoraSindicatos
     */
    public function setMantenedoraSindicatos($mantenedoraSindicatos)
    {
        $this->mantenedoraSindicatos = $mantenedoraSindicatos;
    }

    /**
     * @return mixed
     */
    public function getMantenedoraOng()
    {
        return $this->mantenedoraOng;
    }

    /**
     * @param mixed $mantenedoraOng
     */
    public function setMantenedoraOng($mantenedoraOng)
    {
        $this->mantenedoraOng = $mantenedoraOng;
    }

    /**
     * @return mixed
     */
    public function getMantenedoraInstituicaoSemFimLucrativo()
    {
        return $this->mantenedoraInstituicaoSemFimLucrativo;
    }

    /**
     * @param mixed $mantenedoraInstituicaoSemFimLucrativo
     */
    public function setMantenedoraInstituicaoSemFimLucrativo($mantenedoraInstituicaoSemFimLucrativo)
    {
        $this->mantenedoraInstituicaoSemFimLucrativo = $mantenedoraInstituicaoSemFimLucrativo;
    }

    /**
     * @return mixed
     */
    public function getMantenedoraSistemaS()
    {
        return $this->mantenedoraSistemaS;
    }

    /**
     * @param mixed $mantenedoraSistemaS
     */
    public function setMantenedoraSistemaS($mantenedoraSistemaS)
    {
        $this->mantenedoraSistemaS = $mantenedoraSistemaS;
    }

    /**
     * @return mixed
     */
    public function getMantenedoraOscip()
    {
        return $this->mantenedoraOscip;
    }

    /**
     * @param mixed $mantenedoraOscip
     */
    public function setMantenedoraOscip($mantenedoraOscip)
    {
        $this->mantenedoraOscip = $mantenedoraOscip;
    }

    // 22 a 25 Órgão a que a escola pública está vinculada

    /**
     * @return mixed
     */
    public function getCategoriaEscolaPrivada()
    {
        return $this->categoriaEscolaPrivada;
    }

    /**
     * @param mixed $categoriaEscolaPrivada
     */
    public function setCategoriaEscolaPrivada($categoriaEscolaPrivada)
    {
        $this->categoriaEscolaPrivada = $categoriaEscolaPrivada;
    }

    // 26 a 31  Mantenedora da escola privada

    /**
     * @return mixed
     */
    public function getCnpjMantenedoraPrincipal()
    {
        return $this->cnpjMantenedoraPrincipal;
    }

    /**
     * @param mixed $cnpjMantenedoraPrincipal
     */
    public function setCnpjMantenedoraPrincipal($cnpjMantenedoraPrincipal)
    {
        $this->cnpjMantenedoraPrincipal = $cnpjMantenedoraPrincipal;
    }

    /**
     * @return mixed
     */
    public function getCnpjEscolaPrivada()
    {
        return $this->cnpjEscolaPrivada;
    }

    /**
     * @param mixed $cnpjEscolaPrivada
     */
    public function setCnpjEscolaPrivada($cnpjEscolaPrivada)
    {
        $this->cnpjEscolaPrivada = $cnpjEscolaPrivada;
    }

    /**
     * @return mixed
     */
    public function getRegulamentacao()
    {
        return $this->regulamentacao;
    }

    /**
     * @param mixed $regulamentacao
     */
    public function setRegulamentacao($regulamentacao)
    {
        $this->regulamentacao = $regulamentacao;
    }

    /**
     * @return mixed
     */
    public function getEsferaFederal()
    {
        return $this->esferaFederal;
    }

    /**
     * @param mixed $esferaFederal
     */
    public function setEsferaFederal($esferaFederal)
    {
        $this->esferaFederal = $esferaFederal;
    }

    /**
     * @return mixed
     */
    public function getEsferaEstadual()
    {
        return $this->esferaEstadual;
    }

    /**
     * @param mixed $esferaEstadual
     */
    public function setEsferaEstadual($esferaEstadual)
    {
        $this->esferaEstadual = $esferaEstadual;
    }

    /**
     * @return mixed
     */
    public function getEsferaMunicipal()
    {
        return $this->esferaMunicipal;
    }

    // 37 a 39  Esfera administrativa do conselho ou órgão responsável pela regulamentação/autorização

    /**
     * @param mixed $esferaMunicipal
     */
    public function setEsferaMunicipal($esferaMunicipal)
    {
        $this->esferaMunicipal = $esferaMunicipal;
    }

    /**
     * @return mixed
     */
    public function getUnidadeVinculada()
    {
        return $this->unidadeVinculada;
    }

    /**
     * @param mixed $unidadeVinculada
     */
    public function setUnidadeVinculada($unidadeVinculada)
    {
        $this->unidadeVinculada = $unidadeVinculada;
    }

    /**
     * @return mixed
     */
    public function getCodigoEscolaSede()
    {
        return $this->codigoEscolaSede;
    }

    /**
     * @param mixed $codigoEscolaSede
     */
    public function setCodigoEscolaSede($codigoEscolaSede)
    {
        $this->codigoEscolaSede = $codigoEscolaSede;
    }

    /**
     * @return mixed
     */
    public function getCodigoIES()
    {
        return $this->codigoIES;
    }

    /**
     * @param mixed $codigoIES
     */
    public function setCodigoIES($codigoIES)
    {
        $this->codigoIES = $codigoIES;
    }

    public function setUf($uf)
    {
        $this->uf = $uf;
    }

    /**
     * @return int
     */
    public function getSecretariaSaude()
    {
        return $this->secretariaSaude;
    }

    /**
     * @param int $secretariaSaude
     */
    public function setSecretariaSaude($secretariaSaude)
    {
        $this->secretariaSaude = $secretariaSaude;
        return $this;
    }

    public function getTipoRegistro()
    {
        return $this->tipoRegistro;
    }

    public function getUF()
    {
        return $this->uf;
    }
    public function toArray()
    {
        return array(
            "registro" => $this->getTipoRegistro(),
            "codigo" => $this->getCodigo(),
            "codigoInep" => $this->getCodigoInep(),
            "situacaoFuncionamento" => $this->getSituacaoFuncionamento(),
            "dataIncicioAnoLetivo" => $this->getDataIncicioAnoLetivo(),
            "dataFinalAnoLetivo" => $this->getDataFinalAnoLetivo(),
            "nomeEscola" => $this->getNomeEscola(),
            "cep" => $this->getCep(),
            "municipio" => $this->getMunicipio(),
            "distrito" => $this->getDistrito(),
            "endereco" => $this->getEndereco(),
            "numero" => $this->getNumero(),
            "complemento" => $this->getComplemento(),
            "bairro" => $this->getBairro(),
            "ddd" => $this->getDdd(),
            "telefone" => $this->getTelefone(),
            "outroTelefone" => $this->getOutroTelefone(),
            "email" => $this->getEmail(),
            "codigoOrgaoRegionalEnsino" => $this->getCodigoOrgaoRegionalEnsino(),
            "zonaEscola" => $this->getZonaEscola(),
            "localizacaoDiferenciada" => $this->getLocalizacaoDiferenciada(),
            "dependenciaAdministrativa" => $this->getDependenciaAdministrativa(),
            "secretariaEducacao" => $this->getSecretariaEducacao(),
            "secretariaSeguranca" => $this->getSecretariaSeguranca(),
            "secretariaSaude" => $this->getSecretariaSaude(),
            "outroOrgaoAdministracaoPublica" => $this->getOutroOrgaoAdministracaoPublica(),
            "mantenedoraPrivada" => $this->getMantenedoraPrivada(),
            "mantenedoraSindicatos" => $this->getMantenedoraSindicatos(),
            "mantenedoraOng" => $this->getMantenedoraOng(),
            "mantenedoraInstituicaoSemFimLucrativo" => $this->getMantenedoraInstituicaoSemFimLucrativo(),
            "mantenedoraSistemaS" => $this->getMantenedoraSistemaS(),
            "mantenedoraOscip" => $this->getMantenedoraOscip(),
            "categoriaEscolaPrivada" => $this->getCategoriaEscolaPrivada(),
            "secretariaEstadual" => 0,
            "secretariaMunicipal" => 0,
            "naoPossuiConvenio" => 1,
            "termoColaboracao" => null,
            "termoFormento" => null,
            "acordoCooperacao" => null,
            "contratoPrestacaoServico" => null,
            "termoCooperacaoTecnica" => null,
            "coonvenioCooperacao" => null,
            "atividadeComplementar" => null,
            "termoColaboracaoMunicipal" => null,
            "termoFormentoMunicipal" => null,
            "acordoCooperacaoMunicipal" => null,
            "contratoPrestacaoServicoMunicipal" => null,
            "termoCooperacaoTecnicaMunicipal" => null,
            "coonvenioCooperacaoMunicipal" => null,
            "atividadeEducacional" => null,
            "crecheParcial" => null,
            "crecheIntegral" => null,
            "preEscolaParcial" => null,
            "preEscolaIntegral" => null,
            "FundamentalInicialParcial" => null,
            "FundamentalInicialIntegral" => null,
            "FundamentalFinalParcial" => null,
            "FundamentalFinalIntegral" => null,
            "medioParcial" => null,
            "medioIntegral" => null,
            "especialParcial" => null,
            "especialIntegral" => null,
            "ejaFundamental" => null,
            "ejaMedio" => null,
//            "ProfissionalQualificacaoEnsinoFundamentalParcial" => null,
//            "ProfissionalQualificacaoEnsinoFundamentalIntegral" => null,
//            "ProfissionalQualificacaoNivelMedioParcial" => null,
//            "ProfissionalQualificacaoNivelMedioIntegral" => null,
//            "ProfissionalQualificacaoConcomitanteNivelMedioParcial" => null,
//            "ProfissionalQualificacaoConcomitanteNivelMedioIntegral" => null,
//            "ProfissionalQualificacaoIntercomplementarNivelMedioParcial" => null,
//            "ProfissionalQualificacaoIntercomplementarNivelMedioIntegral" => null,
//            "ProfissionalQualificacaoEnsinoMedioParcial" => null,
//            "ProfissionalQualificacaoEnsinoIntegral" => null,
//            "ProfissionalQualificacaoConcomitanteEnsinoMedioParcial" => null,
//            "ProfissionalQualificacaoConcomitanteEnsinoMedioIntegral" => null,
//            "ProfissionalQualificacaoIntercomplementarEnsinoMedioParcial" => null,
//            "ProfissionalQualificacaoIntercomplementarEnsinoMedioIntegral" => null,
//            "ProfissionalProfissionalEnsinoMedioParcial" => null,
//            "ProfissionalProfissionalEnsinoIntegral" => null,
//            "ProfissionalProfissionalConcomitanteEnsinoMedioParcial" => null,
//            "ProfissionalProfissionalConcomitanteEnsinoMedioIntegral" => null,
//            "ProfissionalProfissionalIntercomplementarEnsinoMedioParcial" => null,
//            "ProfissionalProfissionalIntercomplementarEnsinoMedioIntegral" => null,
//            "ProfissionalProfissionalSubsequente" => null,
//            "ProfissionalProfissionalNivelMedioParcial" => null,
//            "ProfissionalProfissionalNivelMedioIntegral" => null,
//            "ProfissionalProfissionalConcomitanteNivelMedioParcial" => null,
//            "ProfissionalProfissionalConcomitanteNivelMedioIntegral" => null,
//            "ProfissionalProfissionalIntercomplementarNivelMedioParcial" => null,
//            "ProfissionalProfissionalIntercomplementarNivelMedioIntegral" => null,
            "cnpjMantenedoraPrincipal" => $this->getCnpjMantenedoraPrincipal(),
            "cnpjEscolaPrivada" => $this->getCnpjEscolaPrivada(),
            "regulamentacao" => $this->getRegulamentacao(),
            "esferaFederal" => $this->getEsferaFederal(),
            "esferaEstadual" => $this->getEsferaEstadual(),
            "esferaMunicipal" => $this->getEsferaMunicipal(),
            "unidadeVinculada" => $this->getUnidadeVinculada(),
            "codigoEscolaSede" => $this->getCodigoEscolaSede(),
            "codigoIES" => $this->getCodigoIES(),
            "uf" => $this->getUf(),
        );
    }
}
