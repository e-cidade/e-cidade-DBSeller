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

use ECidade\RecursosHumanos\ESocial\Repository\ServidorAlteracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Transformer\S2205;

/**
 * model para cgm
 * @package Protocolo
 */
class CgmFisico extends CgmBase
{

    /**
     * CPF do CGM
     *
     * @var integer
     */
    protected $iCpf;

    /**
     * Carteira Nacional de Habilitação
     *
     * @var string
     */
    protected $sCnh;

    /**
     * Categoria da CNH
     *
     * @var string
     */
    protected $sCategoriaCnh;

    /**
     * Data de Emissão da CNH
     *
     * @var string
     */
    protected $dtDataEmissaoCnh;

    /**
     * Data da Habilitação CNH
     *
     * @var string
     */
    protected $dtDataHabilitacaoCnh;

    /**
     * Data do Vencimento da CNH
     *
     * @var string
     */
    protected $dtDataVencimentoCnh;

    /**
     * Data de Falecimento do CGM
     *
     * @var string
     */
    protected $dtDataFalecimento;

    /**
     * Data de Nascimento do CGM
     *
     * @var string
     */
    protected $dtDataNascimento;

    /**
     * Nome do Pai do CGM
     *
     * @var string
     */
    protected $sNomePai;

    /**
     * Nome da Mãe do CGM
     *
     * @var string
     */
    protected $sNomeMae;

    /**
     * Sexo do CGM
     *
     * @var string
     */
    protected $sSexo;

    /**
     * Profissão do CGM
     *
     * @var string
     */
    protected $sProfissao;

    /**
     * Nacionalidade do CGM
     *
     * @var integer
     */
    protected $iNacionalidade;

    /**
     * Estado Civil do CGM
     *
     * @var integer
     */
    protected $iEstadoCivil;

    /**
     * Número da Identidade do CGM
     *
     * @var string
     */
    protected $sIdentidade;

    /**
     * Orgão emissor da identidade CGM
     *
     * @var string
     */
    protected $sIdentOrgao;

    /**
     * Data de Expiração da Identidade do CGM
     *
     * @var string
     */
    protected $sIdentDtExp;

    /**
     * Natiralidade do CGM
     *
     * @var string
     */
    protected $sNaturalidade;

    /**
     * Escolaridade do CGM
     *
     * @var string
     */
    protected $sEscolaridade;

    /**
     * Trabalha do CGM
     *
     * @var boolean
     */
    protected $lTrabalha;

    /**
     * Renda do CGM
     *
     * @var float
     */
    protected $nRenda;

    /**
     * Local de Trabalho do CGM
     *
     * @var string
     */
    protected $sLocalTrabalho;

    /**
     * PIS
     *
     * @var string
     */
    protected $sPIS;

    /**
     * Código CBO
     *
     * @var string
     */
    protected $iCBO;


    /**
     * Situação do CPF
     *
     * @var integer
     */
    protected $iSituacaoCpf;


    /**
     * familiares do Cgm
     */
    protected $aFamiliares = array();

    /**
     * @type string
     */
    protected $sDocumentoEstrangeiro;

    /**
     * @var string
     */
    protected $sNomeSocial;

    /**
     * @var integer
     */
    protected $iPaisNascimento;

    /**
     * @var string
     */
    protected $sPaisNascimento;

    /**
     * @var string
     * Campo para atender e-social
     */
    protected $sCodigoPaisNascimento;

    /**
     * @var integer
     */
    protected $iPaisNacionalidade;

    /**
     * @var string
     */
    protected $sPaisNacionalidade;

    /**
     * @var string
     * Campo para atender e-social
     */
    protected $sCodigoPaisNacionalidade;

    /**
     * @var integer
     */
    protected $iPaisExterior;

    /**
     * @var string
     */
    protected $sPaisExterior;

    /**
     * @var string
     */
    protected $sCodigoPaisExterior;

    /**
     * @var string
     */
    protected $sLogradouroExterior;

    /**
     * @var integer
     */
    protected $iNumeroExterior;

    /**
     * @var string
     */
    protected $sComplementoExterior;

    /**
     * @var string
     */
    protected $sBairroExterior;

    /**
     * @var string
     */
    protected $sCidadeExterior;

    /**
     * @var string
     */
    protected $sCodigoPostalExterior;

    /**
     * @var string
     */
    protected $sPaisEstrangeiro;

    /**
     * @var string
     */
    protected $sCidadeEstrangeiro;

    /**
     *
     * 1) Masculino Trans
     * 2) Feminino Trans
     * 3) Não Binário
     * 4) Masculino
     * 5) Feminino
     * @var int
     */
    protected $genero;

    const NACIONALIDADE_BRASILEIRA = 1;
    const NACIONALIDADE_ESTRANGEIRA = 2;

    function __construct($iCgm = null)
    {


        if (!empty($iCgm)) {
            parent::__construct($iCgm);

            $oDaoCgm = new cl_cgm();
            $sSqlCgm = $oDaoCgm->sql_query_file($iCgm);
            $rsCgm = $oDaoCgm->sql_record($sSqlCgm);

            if ($oDaoCgm->numrows > 0) {
                $oDadosCgm = db_utils::fieldsMemory($rsCgm, 0);

                $this->setCpf($oDadosCgm->z01_cgccpf);
                $this->setCategoriaCnh($oDadosCgm->z01_categoria);
                $this->setCnh($oDadosCgm->z01_cnh);
                $this->setDataEmissaoCnh($oDadosCgm->z01_dtemissao);
                $this->setDataFalecimento($oDadosCgm->z01_dtfalecimento);
                $this->setDataHabilitacaoCnh($oDadosCgm->z01_dthabilitacao);
                $this->setDataNascimento($oDadosCgm->z01_nasc);
                $this->setDataVencimentoCnh($oDadosCgm->z01_dtvencimento);
                $this->setEstadoCivil($oDadosCgm->z01_estciv);
                $this->setIdentidade($oDadosCgm->z01_ident);
                $this->setNacionalidade($oDadosCgm->z01_nacion);
                $this->setNomeMae($oDadosCgm->z01_mae);
                $this->setNomePai($oDadosCgm->z01_pai);
                $this->setProfissao($oDadosCgm->z01_profis);
                $this->setSexo($oDadosCgm->z01_sexo);
                $this->setNaturalidade($oDadosCgm->z01_naturalidade);
                $this->setEscolaridade($oDadosCgm->z01_escolaridade);
                $this->setIdentDataExp($oDadosCgm->z01_identdtexp);
                $this->setIdentOrgao($oDadosCgm->z01_identorgao);
                $this->setLocalTrabalho($oDadosCgm->z01_localtrabalho);
                $this->setRenda($oDadosCgm->z01_renda);
                $this->setTrabalha($oDadosCgm->z01_trabalha == 't' ? true : false);
                $this->setPIS($oDadosCgm->z01_pis);
                $this->setObs($oDadosCgm->z01_obs);
                $this->setNomeCompleto(!empty($oDadosCgm->z01_nomecomple) ? $oDadosCgm->z01_nomecomple : $oDadosCgm->z01_nome);
                $this->setGenero($oDadosCgm->z01_genero);

                $oDaoCgmFisico = new cl_cgmfisico();
                $sCampos = "z04_rhcbo, ";
                $sCampos .= "z04_nomesocial, ";
                $sCampos .= "z04_paisnascimento, ";
                $sCampos .= "paisnascimento.db70_descricao as descr_paisnasc, ";
                $sCampos .= "substr(paisnascimentocodigo.db135_codigo, 2, 3) as cod_paisnasc, ";
                $sCampos .= "z04_paisnacionalidade, ";
                $sCampos .= "paisnacionalidade.db70_descricao as descr_paisnac,";
                $sCampos .= "substr(paisnacionalidadecodigo.db135_codigo, 2, 3) as cod_paisnac";

                $sSqlCgmFisico = $oDaoCgmFisico->sql_query(null, $sCampos, null, "z04_numcgm = {$iCgm}");
                $rsCgmFisico = db_query($sSqlCgmFisico);

                if (is_resource($rsCgmFisico)) {
                    $oDaoCgmFisico->numrows = pg_num_rows($rsCgmFisico);
                }

                if ($oDaoCgmFisico->numrows > 0) {
                    $oCgmFisico = db_utils::fieldsMemory($rsCgmFisico, 0);
                    $this->setCBO($oCgmFisico->z04_rhcbo);
                    /**
                     *  Campos para e-social - Inicio
                     */
                    $this->setNomeSocial($oCgmFisico->z04_nomesocial);
                    $this->setPaisNascimento($oCgmFisico->z04_paisnascimento);
                    $this->setPaisNacionalidade($oCgmFisico->z04_paisnacionalidade);
                    $this->sPaisNascimento = $oCgmFisico->descr_paisnasc;
                    $this->sCodigoPaisNascimento = $oCgmFisico->cod_paisnasc;
                    $this->sPaisNacionalidade = $oCgmFisico->descr_paisnac;
                    $this->sCodigoPaisNacionalidade = $oCgmFisico->cod_paisnac;
                    /**
                     *  Campos para e-social - Fim
                     */
                }

                $oDaoCgmEnderecoExterior = new cl_cgmenderecoexterior();
                $sCampo = "z19_pais, ";
                $sCampo .= "z19_logradouro, ";
                $sCampo .= "z19_numero, ";
                $sCampo .= "z19_complemento, ";
                $sCampo .= "z19_bairro, ";
                $sCampo .= "z19_cidade, ";
                $sCampo .= "z19_codigopostal, ";
                $sCampo .= "db70_descricao as descr_paisendereco, ";
                $sCampo .= "substr(db135_codigo, 2, 3) as cod_paisendereco";
                $sSqlCgmEnderecoExterior = $oDaoCgmEnderecoExterior->sql_query(null, $sCampo, null, "z19_numcgm = {$iCgm}");

                $rsCgmEnderecoExterior = db_query($sSqlCgmEnderecoExterior);

                if (is_resource($rsCgmEnderecoExterior)) {
                    $oDaoCgmEnderecoExterior->numrows = pg_num_rows($rsCgmEnderecoExterior);
                }

                if ($oDaoCgmEnderecoExterior->numrows > 0) {
                    $oDadosCgmEnderecoExterior = db_utils::fieldsMemory($rsCgmEnderecoExterior, 0);
                    $this->sPaisExterior = $oDadosCgmEnderecoExterior->descr_paisendereco;
                    $this->sCodigoPaisExterior = $oDadosCgmEnderecoExterior->cod_paisendereco;
                    $this->setPaisExterior($oDadosCgmEnderecoExterior->z19_pais);
                    $this->setLogradouroExterior($oDadosCgmEnderecoExterior->z19_logradouro);
                    $this->setNumeroExterior($oDadosCgmEnderecoExterior->z19_numero);
                    $this->setComplementoExterior($oDadosCgmEnderecoExterior->z19_complemento);
                    $this->setBairroExterior($oDadosCgmEnderecoExterior->z19_bairro);
                    $this->setCidadeExterior($oDadosCgmEnderecoExterior->z19_cidade);
                    $this->setCodigoPostalExterior($oDadosCgmEnderecoExterior->z19_codigopostal);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getLocalTrabalho()
    {
        return $this->sLocalTrabalho;
    }

    /**
     * @param $sLocalTrabalha
     */
    public function setLocalTrabalho($sLocalTrabalha)
    {
        $this->sLocalTrabalho = $sLocalTrabalha;
    }

    /**
     * @return bool
     */
    public function getTrabalha()
    {
        return $this->lTrabalha;
    }

    /**
     * @param $lTrabalha
     */
    public function setTrabalha($lTrabalha)
    {
        $this->lTrabalha = $lTrabalha;
    }

    /**
     * @return float
     */
    public function getRenda()
    {
        return $this->nRenda;
    }

    /**
     * @param $nRenda
     */
    public function setRenda($nRenda)
    {
        $this->nRenda = $nRenda;
    }

    /**
     * @return string
     */
    public function getNaturalidade()
    {
        return $this->sNaturalidade;
    }

    /**
     * @param $sNaturalidade
     */
    public function setNaturalidade($sNaturalidade)
    {
        $this->sNaturalidade = $sNaturalidade;
    }

    /**
     * @return string
     */
    public function getEscolaridade()
    {
        return $this->sEscolaridade;
    }

    /**
     * @param $sEscolaridade
     */
    public function setEscolaridade($sEscolaridade)
    {
        $this->sEscolaridade = $sEscolaridade;
    }

    /**
     * @return string
     */
    public function getIdentOrgao()
    {
        return $this->sIdentOrgao;
    }

    /**
     * @param string $sIdentOrgao
     */
    public function setIdentOrgao($sIdentOrgao)
    {
        $this->sIdentOrgao = $sIdentOrgao;
    }

    /**
     * @return string
     */
    public function getIdentDataExp()
    {
        return $this->sIdentDtExp;
    }

    /**
     * @param string $sIdentDtExp
     */
    public function setIdentDataExp($sIdentDtExp)
    {
        $this->sIdentDtExp = $sIdentDtExp;
    }

    /**
     * @return string
     */
    public function getDataEmissaoCnh()
    {
        return $this->dtDataEmissaoCnh;
    }

    /**
     * @param string $dtDataEmissaoCnh
     */
    public function setDataEmissaoCnh($dtDataEmissaoCnh)
    {
        $this->dtDataEmissaoCnh = $dtDataEmissaoCnh;
    }

    /**
     * @return string
     */
    public function getDataFalecimento()
    {
        return $this->dtDataFalecimento;
    }

    /**
     * @param string $dtDataFalecimento
     */
    public function setDataFalecimento($dtDataFalecimento)
    {
        $this->dtDataFalecimento = $dtDataFalecimento;
    }

    /**
     * @return string
     */
    public function getDataHabilitacaoCnh()
    {
        return $this->dtDataHabilitacaoCnh;
    }

    /**
     * @param string $dtDataHabilitacaoCnh
     */
    public function setDataHabilitacaoCnh($dtDataHabilitacaoCnh)
    {
        $this->dtDataHabilitacaoCnh = $dtDataHabilitacaoCnh;
    }

    /**
     * @return string
     */
    public function getDataNascimento()
    {
        return $this->dtDataNascimento;
    }

    /**
     * @param string $dtDataNascimento
     */
    public function setDataNascimento($dtDataNascimento)
    {
        $this->dtDataNascimento = $dtDataNascimento;
    }

    /**
     * @return string
     */
    public function getDataVencimentoCnh()
    {
        return $this->dtDataVencimentoCnh;
    }

    /**
     * @param string $dtDataVencimentoCnh
     */
    public function setDataVencimentoCnh($dtDataVencimentoCnh)
    {
        $this->dtDataVencimentoCnh = $dtDataVencimentoCnh;
    }

    /**
     * @return string
     */
    public function getCpf()
    {
        return $this->iCpf;
    }

    /**
     * @param string $iCpf
     */
    public function setCpf($iCpf)
    {
        $this->iCpf = $iCpf;
    }

    /**
     * @return string
     */
    public function getEstadoCivil()
    {
        return $this->iEstadoCivil;
    }

    /**
     * @return string
     */
    public function getDescrEstadoCivil()
    {
        switch ($this->iEstadoCivil) {
            case '1':
                $sEstadoCivil = 'Solteiro';
                break;
            case '2':
                $sEstadoCivil = 'Casado';
                break;
            case '3':
                $sEstadoCivil = 'Viúvo';
                break;
            case '4':
                $sEstadoCivil = 'Divorciado';
                break;
            case '5':
                $sEstadoCivil = 'Separado Consensual';
                break;
            case '6':
                $sEstadoCivil = 'Separado Judicial';
                break;
            case '7':
                $sEstadoCivil = 'União Estavel';
                break;
            default:
                $sEstadoCivil = '';
                break;
        }
        return $sEstadoCivil;
    }

    /**
     * @param integer $iEstadoCivil
     */
    public function setEstadoCivil($iEstadoCivil)
    {
        $this->iEstadoCivil = $iEstadoCivil;
    }

    /**
     * @return integer
     */
    public function getNacionalidade()
    {
        return $this->iNacionalidade;
    }

    /**
     * @return string
     */
    public function getDescrNacionalidade()
    {
        switch ($this->iNacionalidade) {
            case 1:
                return "Brasileira";
                break;
            case 2:
                return "Estrangeira";
                break;
            default:
                return "";
                break;
        }
    }

    /**
     * @param integer $iNacionalidade
     */
    public function setNacionalidade($iNacionalidade)
    {
        $this->iNacionalidade = $iNacionalidade;
    }

    /**
     * @return string
     */
    public function getCategoriaCnh()
    {
        return $this->sCategoriaCnh;
    }

    /**
     * @param $sCategoriaCnh
     */
    public function setCategoriaCnh($sCategoriaCnh)
    {
        $this->sCategoriaCnh = $sCategoriaCnh;
    }

    /**
     * @return string
     */
    public function getCnh()
    {
        return $this->sCnh;
    }

    /**
     * @param $sCnh
     */
    public function setCnh($sCnh)
    {
        $this->sCnh = $sCnh;
    }

    /**
     * @return string
     */
    public function getIdentidade()
    {
        return $this->sIdentidade;
    }

    /**
     * @param $sIdentidade
     */
    public function setIdentidade($sIdentidade)
    {
        $this->sIdentidade = $sIdentidade;
    }

    /**
     * @return string
     */
    public function getNomeMae()
    {
        return $this->sNomeMae;
    }

    /**
     * @param $sNomeMae
     */
    public function setNomeMae($sNomeMae)
    {
        $this->sNomeMae = $sNomeMae;
    }

    /**
     * @return string
     */
    public function getNomePai()
    {
        return $this->sNomePai;
    }

    /**
     * @param $sNomePai
     */
    public function setNomePai($sNomePai)
    {
        $this->sNomePai = $sNomePai;
    }

    /**
     * @return string
     */
    public function getProfissao()
    {
        return $this->sProfissao;
    }

    /**
     * @param $sProfissao
     */
    public function setProfissao($sProfissao)
    {
        $this->sProfissao = $sProfissao;
    }

    /**
     * @return string
     */
    public function getSexo()
    {
        return $this->sSexo;
    }

    /**
     * @param $sSexo
     */
    public function setSexo($sSexo)
    {
        $this->sSexo = $sSexo;
    }


    /**
     * @param $iCBO
     */
    public function setCBO($iCBO)
    {
        $this->iCBO = $iCBO;
    }

    /**
     * @return string
     */
    public function getCBO()
    {
        return $this->iCBO;
    }

    /**
     * @param $sPIS
     */
    public function setPIS($sPIS)
    {
        $this->sPIS = $sPIS;
    }

    /**
     * @return string
     */
    public function getPIS()
    {
        return $this->sPIS;
    }

    /**
     * @return integer
     */
    public function getSituacao()
    {

        if (!empty($this->iSituacaoCpf)) {
            return $this->iSituacaoCpf;
        } else {
            $cl_situacao = new cl_cgmsituacaocpf();
            $sSqlSituacao = $cl_situacao->sql_query("", "*", "", "z01_numcgm = {$this->getCodigo()}");
            $rsSituacao = $cl_situacao->sql_record($sSqlSituacao);
            $aListaSituacao = db_utils::getCollectionByRecord($rsSituacao);
            $iSituacao = count($aListaSituacao);
            if ($iSituacao == 0 || $iSituacao == '0') {
                return false;
            } else {
                return $aListaSituacao[0]->z17_situacao;
            }
        }
    }

    /**
     * @param integer $iSituacaoCpf
     */
    public function setSituacao($iSituacaoCpf)
    {
        $this->iSituacaoCpf = $iSituacaoCpf;
    }

    /**
     * @param string $sNome
     */
    public function setNomeSocial($sNomeSocial)
    {
        $this->sNomeSocial = $sNomeSocial;
    }

    /**
     * @return string
     */
    public function getNomeSocial()
    {
        return $this->sNomeSocial;
    }

    /**
     * @param integer $iPaisNascimento
     */
    public function setPaisNascimento($iPaisNascimento)
    {
        $this->iPaisNascimento = $iPaisNascimento;
    }

    /**
     * @return integer
     */
    public function getPaisNascimento()
    {
        return $this->iPaisNascimento;
    }

    /**
     * @return string
     */
    public function getDescricaoPaisNascimento()
    {
        return $this->sPaisNascimento;
    }

    /**
     * @return string
     */
    public function getCodigoPaisNascimento()
    {
        return $this->sCodigoPaisNascimento;
    }

    /**
     * @param integer $iPaisNacionalidade
     */
    public function setPaisNacionalidade($iPaisNacionalidade)
    {
        $this->iPaisNacionalidade = $iPaisNacionalidade;
    }

    /**
     * @return integer
     */
    public function getPaisNacionalidade()
    {
        return $this->iPaisNacionalidade;
    }

    /**
     * @return string
     */
    public function getDescricaoPaisNacionalidade()
    {
        return $this->sPaisNacionalidade;
    }

    /**
     * @return string
     */
    public function getCodigoPaisNacionalidade()
    {
        return $this->sCodigoPaisNacionalidade;
    }

    public function setPaisExterior($iPaisExterior)
    {
        $this->iPaisExterior = $iPaisExterior;
    }

    public function getPaisExterior()
    {
        return $this->iPaisExterior;
    }

    public function getDescricaoPaisExterior()
    {
        return $this->sPaisExterior;
    }

    public function getCodigoPaisExterior()
    {
        return $this->sCodigoPaisExterior;
    }

    public function setLogradouroExterior($sLogradouroExterior)
    {
        $this->sLogradouroExterior = $sLogradouroExterior;
    }

    public function getLogradouroExterior()
    {
        return $this->sLogradouroExterior;
    }

    public function setNumeroExterior($iNumeroExterior)
    {
        $this->iNumeroExterior = $iNumeroExterior;
    }

    public function getNumeroExterior()
    {
        return $this->iNumeroExterior;
    }

    public function setComplementoExterior($sComplementoExterior)
    {
        $this->sComplementoExterior = $sComplementoExterior;
    }

    public function getComplementoExterior()
    {
        return $this->sComplementoExterior;
    }

    public function setBairroExterior($sBairroExterior)
    {
        $this->sBairroExterior = $sBairroExterior;
    }

    public function getBairroExterior()
    {
        return $this->sBairroExterior;
    }

    public function setCidadeExterior($sCidadeExterior)
    {
        $this->sCidadeExterior = $sCidadeExterior;
    }

    public function getCidadeExterior()
    {
        return $this->sCidadeExterior;
    }

    public function setCodigoPostalExterior($sCodigoPostalExterior)
    {
        $this->sCodigoPostalExterior = $sCodigoPostalExterior;
    }

    public function getCodigoPostalExterior()
    {
        return $this->sCodigoPostalExterior;
    }

    /**
     * @return int
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * @param int $genero
     */
    public function setGenero($genero)
    {
        $this->genero = $genero;
    }


    /**
     * Salva os dados informados do CGM, caso o CGM já exista então
     * é alterado o registro apartir do código (numcgm) informado
     * @throws Exception
     */
    public function save()
    {
        $sMsgErro = 'Falha ao salvar CGM Fisico';

        /**
         * Verifica se existe alguma transação ativa
         */
        if (!db_utils::inTransaction()) {
            throw new Exception("{$sMsgErro}, nenhuma transação encontrada!");
        }

        $oDaoCgm = new cl_cgm();
        $oDaoCgmCpf = new cl_db_cgmcpf();
        $oDaoCgmFisico = new cl_cgmfisico();
        $oDaoCgmJuridico = new cl_cgmjuridico();
        $oDaoCgmEnderecoExterior = new cl_cgmenderecoexterior();

        $oDaoCgm->z01_nome = addslashes(substr($this->getNome(), 0, 40));
        $oDaoCgm->z01_nomecomple = addslashes(substr($this->getNomeCompleto(), 0, 100));
        $oDaoCgm->z01_ender = addslashes($this->getLogradouro());
        $oDaoCgm->z01_numero = $this->getNumero();
        $oDaoCgm->z01_compl = addslashes($this->getComplemento());
        $oDaoCgm->z01_bairro = addslashes($this->getBairro());
        $oDaoCgm->z01_munic = addslashes($this->getMunicipio());
        $oDaoCgm->z01_uf = $this->getUf();
        $oDaoCgm->z01_cep = $this->getCep();
        $oDaoCgm->z01_cxpostal = $this->getCaixaPostal();
        $oDaoCgm->z01_telef = $this->getTelefone();
        $oDaoCgm->z01_incest = $this->getInscricaoEstadual();
        $oDaoCgm->z01_telcel = $this->getCelular();
        $oDaoCgm->z01_email = addslashes($this->getEmail());
        $oDaoCgm->z01_endcon = addslashes($this->getLogradouroComercial());
        $oDaoCgm->z01_numcon = $this->getNumeroComercial();
        $oDaoCgm->z01_comcon = addslashes($this->getComplementoComercial());
        $oDaoCgm->z01_baicon = addslashes($this->getBairroComercial());
        $oDaoCgm->z01_muncon = addslashes($this->getMunicipioComercial());
        $oDaoCgm->z01_ufcon = $this->getUfComercial();
        $oDaoCgm->z01_cepcon = $this->getCepComercial();
        $oDaoCgm->z01_cxposcon = $this->getCaixaPostalComercial();
        $oDaoCgm->z01_telcon = $this->getTelefoneComercial();
        $oDaoCgm->z01_celcon = $this->getCelularComercial();
        $oDaoCgm->z01_emailc = addslashes($this->getEmailComercial());
        $oDaoCgm->z01_fax = $this->getFax();
        $oDaoCgm->z01_login = db_getsession('DB_id_usuario');
        $oDaoCgm->z01_obs = $this->getObs();
        $oDaoCgm->z01_hora = db_hora();
        $oDaoCgm->z01_numcgm = $this->getCodigo();
        $oDaoCgm->z01_cgccpf = $this->getCpf();
        $oDaoCgm->z01_cnh = $this->getCnh();
        $oDaoCgm->z01_categoria = $this->getCategoriaCnh();
        $oDaoCgm->z01_dtemissao = $this->getDataEmissaoCnh();
        $oDaoCgm->z01_dthabilitacao = $this->getDataHabilitacaoCnh();
        $oDaoCgm->z01_dtvencimento = $this->getDataVencimentoCnh();
        $oDaoCgm->z01_dtfalecimento = $this->getDataFalecimento();
        $oDaoCgm->z01_nasc = $this->getDataNascimento();
        $oDaoCgm->z01_pai = addslashes($this->getNomePai());
        $oDaoCgm->z01_mae = addslashes($this->getNomeMae());
        $oDaoCgm->z01_sexo = $this->getSexo();
        $oDaoCgm->z01_profis = addslashes($this->getProfissao());
        $oDaoCgm->z01_nacion = $this->getNacionalidade();
        $oDaoCgm->z01_estciv = $this->getEstadoCivil();
        $oDaoCgm->z01_ident = $this->getIdentidade();
        $oDaoCgm->z01_ultalt = date('Y-m-d', db_getsession('DB_datausu'));
        $oDaoCgm->z01_identorgao = $this->getIdentOrgao();
        $oDaoCgm->z01_identdtexp = $this->getIdentDataExp();
        $oDaoCgm->z01_naturalidade = $this->getNaturalidade();
        $oDaoCgm->z01_escolaridade = $this->getEscolaridade();
        $oDaoCgm->z01_trabalha = $this->getTrabalha() ? "true" : "false";
        $oDaoCgm->z01_localtrabalho = $this->getLocalTrabalho();
        $oDaoCgm->z01_renda = $this->getRenda();
        $oDaoCgm->z01_pis = $this->getPIS();
        $oDaoCgm->z01_obs = $this->getObs();
        $oDaoCgm->z01_genero = $this->getGenero();

        if (!empty($oDaoCgm->z01_numcgm)) {
            $this->validaDadosS2205($oDaoCgm);

            $oDaoCgm->alterar($this->getCodigo());
        } else {
            $oDaoCgm->z01_cadast = !empty($this->getCadastro()) ? $this->getCadastro() : date("Y-m-d");
            $oDaoCgm->z01_login = db_getsession('DB_id_usuario');

            $oDaoCgm->incluir(null);
        }

        if ($oDaoCgm->erro_status == "0") {
            throw new Exception("{$sMsgErro}, {$oDaoCgm->erro_msg}");
        }

        /**
         *  Seta o valor da propriedade $iCodigo com o número do CGM gerado
         */
        $this->setCodigo($oDaoCgm->z01_numcgm);

        /**
         * Caso o CGM informado seja do município da instituição então é verificado se
         * exite a rua e bairro informado nos respectivos cadastros do sistema
         *
         */
        $this->salvaBairroRua();

        /**
         * inseri registro na cgmendereco {Primário}
         */
        if ($this->getEnderecoPrimario() != "" && $this->getEnderecoPrimario() != null) {
            $this->salvaCgmEndereco();
        }

        /**
         * inseri registro na cgmendereco {Secundário}
         */
        $oDaoCgmEndereco = new \cl_cgmendereco();

        if ($this->getEnderecoSecundario() != "" && $this->getEnderecoSecundario() != null) {
            $this->salvaCgmEnderecoSecundario();
        } elseif ($this->getEnderecoSecundario() == "") {
            $sWhereCgmEnderSecundario = "z07_numcgm=" . $this->getCodigo() . " and z07_tipo = 'S' ";
            $oDaoCgmEndereco->excluir(null, $sWhereCgmEnderSecundario);

            if ($oDaoCgmEndereco->erro_status == '0') {
                throw new Exception("{$oDaoCgmEndereco->erro_msg}");
            }
        }

        $oDaoCgmCpf->excluir($this->getCodigo());
        if ($oDaoCgmCpf->erro_status == "0") {
            throw new Exception("{$sMsgErro}, {$oDaoCgmCpf->erro_msg}");
        }

        $oDaoCgmCpf->z01_numcgm = $this->getCodigo();
        $oDaoCgmCpf->z01_cpf = $this->getCpf();

        $oDaoCgmCpf->incluir($this->getCodigo());

        if ($oDaoCgmCpf->erro_status == "0") {
            throw new Exception("{$sMsgErro}, {$oDaoCgmCpf->erro_msg}");
        }

        if ($this->getSituacao()) {
            $this->salvarSituacaoCpf();
        }

        $oDaoCgmJuridico->excluir(null, "z08_numcgm = {$this->getCodigo()}");
        if ($oDaoCgmJuridico->erro_status == "0") {
            throw new Exception("Erro ao excluir cgm {$this->getCodigo()} da tabela cgmjuridico.");
        }

        $oDaoCgmFisico->excluir(null, "z04_numcgm = {$this->getCodigo()}");

        if ($oDaoCgmFisico->erro_status == "0") {
            throw new Exception("Erro ao excluir cgm {$this->getCodigo()} da tabela cgmfisico.");
        }

        $oDaoCgmFisico->z04_numcgm = $this->getCodigo();
        $oDaoCgmFisico->z04_rhcbo = $this->getCBO();

        /**
         *  Campos para e-social - Inicio
         */
        $oDaoCgmFisico->z04_nomesocial = $this->getNomeSocial();
        $oDaoCgmFisico->z04_paisnascimento = $this->getPaisNascimento();
        $oDaoCgmFisico->z04_paisnacionalidade = $this->getPaisNacionalidade();
        /**
         *  Campos para e-social - Fim
         */
        $oDaoCgmFisico->incluir(null);

        if ($oDaoCgmFisico->erro_status == "0") {
            throw new Exception("Erro ao incluir cgm {$this->getCodigo()} da tabela cgmfisico.{$oDaoCgmFisico->erro_msg}");
        }

        /**
         * Tabela cgmenderecoextrior criada para atender e-social
         */
        $oDaoCgmEnderecoExterior->excluir(null, "z19_numcgm = {$this->getCodigo()}");

        if ($oDaoCgmEnderecoExterior->erro_status == "0") {
            throw new Exception("Erro ao excluir cgm {$this->getCodigo()} da tabela cgmfisico.");
        }

        if ($this->getPaisExterior() != null) {
            $oDaoCgmEnderecoExterior->z19_numcgm = $this->getCodigo();
            $oDaoCgmEnderecoExterior->z19_pais = $this->getPaisExterior();
            $oDaoCgmEnderecoExterior->z19_logradouro = $this->getLogradouroExterior();
            $oDaoCgmEnderecoExterior->z19_numero = $this->getNumeroExterior();
            $oDaoCgmEnderecoExterior->z19_complemento = $this->getComplementoExterior();
            $oDaoCgmEnderecoExterior->z19_bairro = $this->getBairroExterior();
            $oDaoCgmEnderecoExterior->z19_cidade = $this->getCidadeExterior();
            $oDaoCgmEnderecoExterior->z19_codigopostal = $this->getCodigoPostalExterior();

            $oDaoCgmEnderecoExterior->incluir(null);

            if ($oDaoCgmEnderecoExterior->erro_status == "0") {
                throw new Exception("Erro ao vincular cgm {$this->getCodigo()} com endereço no exterior.{$oDaoCgmEnderecoExterior->erro_msg}");
            }
        }
        $this->vincularDocumentoEstrangeiro();
        $this->salvaCgmTipoEmpresa();
    }

    public function adicionarFamiliar($oFamiliar)
    {

        foreach ($this->aFamiliares as $oFamiliarCadastrado) {
            if ($oFamiliarCadastrado->iCgm == $oFamiliar->iCgm) {
                throw new Exception("Familiar já cadastrado para a familia");
            }
        }
        $this->aFamiliares[] = $oFamiliar;
    }

    /**
     * @throws Exception
     */
    public function salvarFamiliares()
    {

        /**
         * o Cgm deve estar cadastrado na familia
         */
        $lCgmOk = false;
        foreach ($this->aFamiliares as $oFamiliar) {
            if ($oFamiliar->iCgm == $this->getCodigo()) {
                $lCgmOk = true;
            }
        }
        if (!$lCgmOk) {
            throw new Exception("o CGm {$this->getCodigo()} - {$this->getNome()}, deve fazer parte da composição familiar");
        }
        /**
         * Verificamos se o cgm já possui uma familia cadastrada
         */
        $iCodigoFamilia = null;
        $oDaoCgmFamilia = db_utils::getDao("cgmcomposicaofamiliar");
        $sWhere = "z15_numcgm = {$this->getCodigo()}";
        $sSqlFamilia = $oDaoCgmFamilia->sql_query_file(null, "z15_cgmfamilia", null, $sWhere);
        $rsFamilia = db_query($sSqlFamilia);

        if (!$rsFamilia) {
            throw new DBException("Ocorreu um erro ao verificar os familiares");
        }

        $oDaoCgmFamilia->numrows = pg_num_rows($rsFamilia);

        if ($oDaoCgmFamilia->numrows > 0) {
            $oDadosFamilia = db_utils::fieldsMemory($rsFamilia, 0);
            $iCodigoFamilia = $oDadosFamilia->z15_cgmfamilia;
        }
        if (empty($iCodigoFamilia)) {
            $oDaoCgmCodigoFamilia = db_utils::getDao("cgmfamilia");
            $oDaoCgmCodigoFamilia->incluir(null);
            $iCodigoFamilia = $oDaoCgmCodigoFamilia->z13_sequencial;
        }
        $oDaoCgmFamilia->excluir(null, "z15_cgmfamilia = {$iCodigoFamilia}");
        foreach ($this->aFamiliares as $oFamilia) {
            $oDaoCgmFamilia->z15_cgmtipofamiliar = $oFamilia->iTipo;
            $oDaoCgmFamilia->z15_cgmfamilia = $iCodigoFamilia;
            $oDaoCgmFamilia->z15_numcgm = $oFamilia->iCgm;
            $oDaoCgmFamilia->incluir(null);
            if ($oDaoCgmFamilia->erro_status == 0) {
                throw new Exception("Erro ao incluir familiar!\n{$oDaoCgmFamilia->erro_msg}");
            }
        }
    }

    /**
     * @return stdClass[]
     */
    public function getFamiliares()
    {

        $iCodigoFamilia = null;
        $oDaoCgmFamilia = db_utils::getDao("cgmcomposicaofamiliar");
        $sWhere = "z15_numcgm = {$this->getCodigo()}";
        $sSqlFamilia = $oDaoCgmFamilia->sql_query_file(null, "z15_cgmfamilia", null, $sWhere);
        $rsFamilia = db_query($sSqlFamilia);

        if (is_resource($rsFamilia)) {
            $oDaoCgmFamilia->numrows = pg_num_rows($rsFamilia);
        }

        if ($oDaoCgmFamilia->numrows > 0) {
            $oDadosFamilia = db_utils::fieldsMemory($rsFamilia, 0);
            $iCodigoFamilia = $oDadosFamilia->z15_cgmfamilia;
        }

        if (!empty($iCodigoFamilia)) {
            $sWhere = "z15_cgmfamilia  = {$iCodigoFamilia}";
            $sSqlFamilia = $oDaoCgmFamilia->sql_query(null, "*", null, $sWhere);
            $rsFamilia = null;
            $rsFamilia = db_query($sSqlFamilia);
            $oDaoCgmFamilia->numrows = 0;

            if (is_resource($rsFamilia)) {
                $oDaoCgmFamilia->numrows = pg_num_rows($rsFamilia);
            }

            for ($i = 0; $i < $oDaoCgmFamilia->numrows; $i++) {
                $oFamiliaCadastrada = db_utils::fieldsMemory($rsFamilia, $i);
                $oFamiliar = new stdClass();
                $oFamiliar->iCgm = $oFamiliaCadastrada->z01_numcgm;
                $oFamiliar->sNome = $oFamiliaCadastrada->z01_nome;
                $oFamiliar->iTipo = $oFamiliaCadastrada->z15_cgmtipofamiliar;
                $oFamiliar->sTipo = $oFamiliaCadastrada->z14_descricao;

                $this->aFamiliares[] = $oFamiliar;
            }
        }
        return $this->aFamiliares;
    }

    function removerFamiliares()
    {
        $this->aFamiliares = array();
    }

    /**
     * método responsavel por atualizar a situação do cpf
     * esse metodo é privado, pois será chamado dentro do metodo save
     * desta classe.
     * @throws Exception
     */
    private function salvarSituacaoCpf()
    {

        $iCgm = $this->getCodigo();
        $iSituacao = $this->getSituacao();
        $sMsgErro = 'Falha ao Atualizar a Situação do CPF ';
        $sSqlSalvaSituacao = "";

        /*
         * verificamos se ja existe registro na tabela cgmsituacaocpf, referente ao cgm consultado
         * se existe definimos o metodo alterar() da classe, se não existe utilizamos incluir()
         */
        $cl_situacao = db_utils::getDao('cgmsituacaocpf');
        $sSqlVerSituacao = $cl_situacao->sql_query("", "*", "", "z17_numcgm = {$this->getCodigo()}");
        $rsVerSituacao = $cl_situacao->sql_record($sSqlVerSituacao);
        $aListaVerSituacao = db_utils::getCollectionByRecord($rsVerSituacao);
        $iVerSituacao = count($aListaVerSituacao);

        $cl_situacao->z17_numcgm = $iCgm;
        $cl_situacao->z17_situacao = $iSituacao;

        if ($iVerSituacao == 0) {
            $cl_situacao->incluir('');
            if ($cl_situacao->erro_status == "0") {
                throw new Exception("{$sMsgErro}, {$cl_situacao->erro_msg}");
            }
        } else {
            $cl_situacao->z17_sequencial = $aListaVerSituacao[0]->z17_sequencial;

            $cl_situacao->alterar($cl_situacao->z17_sequencial);
            if ($cl_situacao->erro_status == "0") {
                throw new Exception("{$sMsgErro}, {$cl_situacao->erro_msg}");
            }
        }
    }

    /**
     * Verifica no banco se todas as perguntas
     * do questionário foram respondidas
     */
    public function preencheuEsocial()
    {

        $sSqlAvaliacaoRespostaCgm = " select                                                                                                                           ";
        $sSqlAvaliacaoRespostaCgm .= "   db103_sequencial as codigo_pergunta,                                                                                           ";
        $sSqlAvaliacaoRespostaCgm .= "   db103_descricao as pergunta,                                                                                                   ";
        $sSqlAvaliacaoRespostaCgm .= "   array_accum(distinct db103_avaliacaogrupopergunta) as grupo_pergunta,                                                          ";
        $sSqlAvaliacaoRespostaCgm .= "   array_accum(db106_sequencial) as codigo_resposta,                                                                              ";
        $sSqlAvaliacaoRespostaCgm .= "   sum(case when db106_sequencial is NULL then 0 else 1 end) as qtde_respostas                                                    ";
        $sSqlAvaliacaoRespostaCgm .= " from                                                                                                                             ";
        $sSqlAvaliacaoRespostaCgm .= "   avaliacaopergunta                                                                                                              ";
        $sSqlAvaliacaoRespostaCgm .= "   inner join avaliacaogrupopergunta on db102_sequencial = db103_avaliacaogrupopergunta                                           ";
        $sSqlAvaliacaoRespostaCgm .= "   inner join avaliacao on db101_sequencial = db102_avaliacao                                                                     ";
        $sSqlAvaliacaoRespostaCgm .= "   inner join avaliacaotipo on db100_sequencial = db101_avaliacaotipo                                                             ";
        $sSqlAvaliacaoRespostaCgm .= "   inner join avaliacaoperguntaopcao on db104_avaliacaopergunta = db103_sequencial                                                ";
        $sSqlAvaliacaoRespostaCgm .= "   left join avaliacaoresposta on db106_avaliacaoperguntaopcao = db104_sequencial                                                 ";
        $sSqlAvaliacaoRespostaCgm .= "   left join avaliacaogrupoperguntaresposta on db108_avaliacaoresposta = db106_sequencial                                         ";
        $sSqlAvaliacaoRespostaCgm .= "   left join avaliacaogruporesposta on db107_sequencial = db108_avaliacaogruporesposta                                            ";
        $sSqlAvaliacaoRespostaCgm .= "   left join avaliacaogruporespostarhpessoal on eso02_avaliacaogruporesposta = db107_sequencial                                   ";
        $sSqlAvaliacaoRespostaCgm .= " where                                                                                                                            ";
        $sSqlAvaliacaoRespostaCgm .= "   db100_sequencial = 5                                                                                                           ";
        $sSqlAvaliacaoRespostaCgm .= "   and db101_sequencial = 3000008                                                                                                 ";
        $sSqlAvaliacaoRespostaCgm .= "   and (eso02_rhpessoal IN (                                                                                                      ";
        $sSqlAvaliacaoRespostaCgm .= "                             select                                                                                               ";
        $sSqlAvaliacaoRespostaCgm .= "                               rh01_regist                                                                                        ";
        $sSqlAvaliacaoRespostaCgm .= "                             from                                                                                                 ";
        $sSqlAvaliacaoRespostaCgm .= "                               rhpessoal                                                                                          ";
        $sSqlAvaliacaoRespostaCgm .= "                             where                                                                                                ";
        $sSqlAvaliacaoRespostaCgm .= "                               rh01_numcgm = " . $this->getCodigo() . "                                                              ";
        $sSqlAvaliacaoRespostaCgm .= "                           )                                                                                                      ";
        $sSqlAvaliacaoRespostaCgm .= "         or db106_sequencial is null                                                                                              ";
        $sSqlAvaliacaoRespostaCgm .= "       )                                                                                                                          ";
        $sSqlAvaliacaoRespostaCgm .= "   and db102_descricao not ilike '%dependente%'                                                                                   ";
        $sSqlAvaliacaoRespostaCgm .= "   and db102_sequencial NOT IN (3000040, 3000041, 3000042, 3000044, 3000045, 3000046, 3000057, 3000060, 3000061, 3000062)         ";
        $sSqlAvaliacaoRespostaCgm .= "   and db104_sequencial NOT IN (                                                                                                  ";
        $sSqlAvaliacaoRespostaCgm .= "                                 select                                                                                           ";
        $sSqlAvaliacaoRespostaCgm .= "                                   DISTINCT db106_avaliacaoperguntaopcao                                                          ";
        $sSqlAvaliacaoRespostaCgm .= "                                 from                                                                                             ";
        $sSqlAvaliacaoRespostaCgm .= "                                   avaliacaoresposta                                                                              ";
        $sSqlAvaliacaoRespostaCgm .= "                                 inner join avaliacaogrupoperguntaresposta on db108_avaliacaoresposta = db106_sequencial          ";
        $sSqlAvaliacaoRespostaCgm .= "                                 inner join avaliacaogruporesposta on db107_sequencial = db108_avaliacaogruporesposta             ";
        $sSqlAvaliacaoRespostaCgm .= "                                 inner join avaliacaogruporespostarhpessoal on eso02_avaliacaogruporesposta = db107_sequencial    ";
        $sSqlAvaliacaoRespostaCgm .= "                                 where                                                                                            ";
        $sSqlAvaliacaoRespostaCgm .= "                                   eso02_rhpessoal IN (                                                                           ";
        $sSqlAvaliacaoRespostaCgm .= "                                                         select                                                                   ";
        $sSqlAvaliacaoRespostaCgm .= "                                                           rh01_regist                                                            ";
        $sSqlAvaliacaoRespostaCgm .= "                                                         from                                                                     ";
        $sSqlAvaliacaoRespostaCgm .= "                                                           rhpessoal                                                              ";
        $sSqlAvaliacaoRespostaCgm .= "                                                         where                                                                    ";
        $sSqlAvaliacaoRespostaCgm .= "                                                           rh01_numcgm = " . $this->getCodigo() . "                                  ";
        $sSqlAvaliacaoRespostaCgm .= "                                                       )                                                                          ";
        $sSqlAvaliacaoRespostaCgm .= "                                   and db106_resposta IS NOT NULL                                                                 ";
        $sSqlAvaliacaoRespostaCgm .= "                                   and db106_resposta <> ''                                                                       ";
        $sSqlAvaliacaoRespostaCgm .= "                               )                                                                                                  ";
        $sSqlAvaliacaoRespostaCgm .= " group by                                                                                                                         ";
        $sSqlAvaliacaoRespostaCgm .= "   db103_sequencial,                                                                                                              ";
        $sSqlAvaliacaoRespostaCgm .= "   db103_descricao                                                                                                                ";
        $sSqlAvaliacaoRespostaCgm .= " having sum(case when db106_sequencial is null then 0 else 1 end) = 0                                                             ";
        $sSqlAvaliacaoRespostaCgm .= " order by                                                                                                                         ";
        $sSqlAvaliacaoRespostaCgm .= "   grupo_pergunta,                                                                                                                ";
        $sSqlAvaliacaoRespostaCgm .= "   pergunta                                                                                                                       ";

        $rsAvaliacaoRespostaCgm = db_query($sSqlAvaliacaoRespostaCgm);

        if (!$rsAvaliacaoRespostaCgm) {
            throw new DBException("Ocorreu um erro ao verificar se o usuario respondeu ao eSocial");
        }

        if (pg_num_rows($rsAvaliacaoRespostaCgm) == 0) {
            return true;
        }

        return false;
    }

    /**
     * Documento estrangeiro
     * @param $sDocumento
     */
    public function setDocumentoEstrangeiro($sDocumento)
    {
        $this->sDocumentoEstrangeiro = $sDocumento;
    }

    /**
     * @param $sCidade
     */
    public function setCidadeEstrangeiro($sCidade)
    {
        $this->sCidadeEstrangeiro = $sCidade;
    }

    /**
     * @param $sPais
     */
    public function setPaisEstrangeiro($sPais)
    {
        $this->sPaisEstrangeiro = $sPais;
    }

    /**
     * Documento estrangeiro
     * @return string
     * @throws Exception
     */
    public function getDocumentoEstrangeiro()
    {

        $this->carregarInformacoesEstrangeiros();
        return $this->sDocumentoEstrangeiro;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getPaisEstrangeiro()
    {

        $this->carregarInformacoesEstrangeiros();
        return $this->sPaisEstrangeiro;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCidadeEstrangeiro()
    {

        $this->carregarInformacoesEstrangeiros();
        return $this->sCidadeEstrangeiro;
    }

    /**
     * Carrega as informações do CGM Estrangeiro
     * @throws Exception
     */
    private function carregarInformacoesEstrangeiros()
    {

        if (empty($this->sPaisEstrangeiro) && $this->iNacionalidade == self::NACIONALIDADE_ESTRANGEIRA) {
            $oDaoDocumento = new cl_cgmestrangeiro();
            $sSqlDocumento = $oDaoDocumento->sql_query_file(null, "*", null, "z09_numcgm = {$this->iCodigo}");
            $rsDocumento = db_query($sSqlDocumento);
            if (!$rsDocumento) {
                throw new Exception("Ocorreu um erro ao buscar o número do documento estrangeiro.");
            }

            if (pg_num_rows($rsDocumento) > 0) {
                $stdDadosEstrangeiro = db_utils::fieldsMemory($rsDocumento, 0);
                $this->setDocumentoEstrangeiro($stdDadosEstrangeiro->z09_documento);
                $this->setCidadeEstrangeiro($stdDadosEstrangeiro->z09_cidade);
                $this->setPaisEstrangeiro($stdDadosEstrangeiro->z09_pais);
            }
        }
    }

    /**
     * Retorna se o cgm é estrangeiro
     * @return bool
     */
    public function estrangeiro()
    {
        return (int)$this->iNacionalidade === self::NACIONALIDADE_ESTRANGEIRA;
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function vincularDocumentoEstrangeiro()
    {

        $oDaoDocumento = new cl_cgmestrangeiro();
        $oDaoDocumento->excluir(null, "z09_numcgm = {$this->iCodigo}");

        if ($this->iNacionalidade == self::NACIONALIDADE_BRASILEIRA || empty($this->iNacionalidade) || empty($this->sDocumentoEstrangeiro)) {
            return true;
        }

        $oDaoDocumento->z09_sequencial = null;
        $oDaoDocumento->z09_numcgm = $this->iCodigo;
        $oDaoDocumento->z09_documento = $this->sDocumentoEstrangeiro;
        $oDaoDocumento->z09_pais = $this->sPaisEstrangeiro;
        $oDaoDocumento->z09_cidade = $this->sCidadeEstrangeiro;
        $oDaoDocumento->incluir($oDaoDocumento->z09_sequencial);
        if ($oDaoDocumento->erro_status == "0") {
            throw new Exception("Não foi possível vincular o documento do CGM estrangeiro.");
        }
        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'numero' => $this->getNumero(),
            'nome' => $this->getNome()
        );
    }

    /**
     * Valida Alteração de Dados Cadastrais para evento S-2205
     * @return void
     */
    private function validaDadosS2205($dados)
    {

        $clcgmfisico = new cl_cgmfisico();
        $clcgmenderecoexterior = new cl_cgmenderecoexterior();
        $clrhpessoal = new cl_rhpessoal;

        // Apontando dados de outras tabelas referentes ao CGM
        $dados->z04_nomesocial = $this->getNomeSocial();
        $dados->z19_pais = $this->getPaisExterior();
        $dados->z19_logradouro = $this->getLogradouroExterior();
        $dados->z19_numero = $this->getNumeroExterior();
        $dados->z19_complemento = $this->getComplementoExterior();
        $dados->z19_bairro = $this->getBairroExterior();
        $dados->z19_cidade = $this->getCidadeExterior();
        $dados->z19_codigopostal = $this->getCodigoPostalExterior();
        $dados->z01_nome = $this->getNome();

        $sqlRH = $clrhpessoal->sql_query(null, '*', null, "rh01_numcgm =  {$this->getCodigo()}");
        $result = $clrhpessoal->sql_record($sqlRH);

        if ($clrhpessoal->numrows > 0) {
            $dadosServidor = pg_fetch_object($result, 0);

            $sqlCgm = $clcgmfisico->sql_query(null, '*', null, "z01_numcgm =  {$this->getCodigo()}");
            $result = $clcgmfisico->sql_record($sqlCgm);

            $sqlEndExt = $clcgmenderecoexterior->sql_query(null, '*', null, "z19_numcgm =  {$this->getCodigo()}");
            $resultEndExt = $clcgmenderecoexterior->sql_record($sqlEndExt);

            if ($clcgmfisico->numrows > 0 || $clcgmenderecoexterior->numrows > 0) {
                if ($result || $resultEndExt) {
                    $dadosAtuais = pg_fetch_object($result, 0);
                    $dadosExtAtuais = empty($resultEndExt) ? new stdClass() : pg_fetch_object($resultEndExt, 0);
                    foreach (S2205::getCamposControleAlteracao() as $campo) {
                        if (isset($dados->$campo)) {
                            if (isset($dadosAtuais->$campo) && $dadosAtuais->$campo != $dados->$campo ||
                                isset($dadosExtAtuais->$campo) && $dadosExtAtuais->$campo != $dados->$campo) {
                                $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout(
                                    $dadosServidor->rh01_regist,
                                    Tipo::S2205
                                );
                                $servidorAlteracao->setDataS2205(new DBDate(date('Y-m-d')));
                                $servidorAlteracao->setDataS2405(new DBDate(date('Y-m-d')));
                                $servidorAlteracao->save();

                                break;
                            }
                        }
                    }
                }
            }
        }
    }
}
