<?php

namespace App\Domain\Educacao\Escola\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Educacao\Escola\Models\AlunoNecessidade;

/**
 * Class Aluno
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed47_i_codigo
 * @property string $ed47_v_nome
 * @property string $ed47_v_ender
 * @property string $ed47_v_compl
 * @property string $ed47_v_bairro
 * @property string $ed47_v_cep
 * @property string $ed47_c_raca
 * @property string $ed47_v_cxpostal
 * @property string $ed47_v_telef
 * @property Carbon $ed47_d_cadast
 * @property string $ed47_v_ident
 * @property integer $ed47_i_login
 * @property string $ed47_c_nomeresp
 * @property string $ed47_c_emailresp
 * @property string $ed47_t_obs
 * @property string $ed47_c_transporte
 * @property string $ed47_c_zona
 * @property string $ed47_c_certidaotipo
 * @property string $ed47_c_certidaonum
 * @property string $ed47_c_certidaolivro
 * @property string $ed47_c_certidaofolha
 * @property string $ed47_c_certidaocart
 * @property Carbon $ed47_c_certidaodata
 * @property string $ed47_c_nis
 * @property string $ed47_c_bolsafamilia
 * @property string $ed47_c_passivo
 * @property Carbon $ed47_d_dtemissao
 * @property Carbon $ed47_d_dthabilitacao
 * @property Carbon $ed47_d_dtvencimento
 * @property Carbon $ed47_d_nasc
 * @property Carbon $ed47_d_ultalt
 * @property integer $ed47_i_estciv
 * @property integer $ed47_i_nacion
 * @property string $ed47_v_categoria
 * @property string $ed47_v_cnh
 * @property string $ed47_v_contato
 * @property string $ed47_v_cpf
 * @property string $ed47_v_email
 * @property string $ed47_v_fax
 * @property string $ed47_v_hora
 * @property string $ed47_v_mae
 * @property string $ed47_v_pai
 * @property string $ed47_v_profis
 * @property string $ed47_v_sexo
 * @property string $ed47_v_telcel
 * @property string $ed47_c_foto
 * @property integer $ed47_o_oid
 * @property string $ed47_c_codigoinep
 * @property integer $ed47_i_pais
 * @property Carbon $ed47_d_identdtexp
 * @property string $ed47_v_identcompl
 * @property string $ed47_c_passaporte
 * @property string $ed47_c_numero
 * @property string $ed47_c_atenddifer
 * @property integer $ed47_i_filiacao
 * @property integer $ed47_i_censoufnat
 * @property integer $ed47_i_censomunicnat
 * @property integer $ed47_i_censoorgemissrg
 * @property integer $ed47_i_censoufident
 * @property integer $ed47_i_censoufcert
 * @property integer $ed47_i_censomuniccert
 * @property integer $ed47_i_censoufend
 * @property integer $ed47_i_censomunicend
 * @property integer $ed47_i_transpublico
 * @property integer $ed47_i_atendespec
 * @property integer $ed47_i_censocartorio
 * @property string $ed47_certidaomatricula
 * @property string $ed47_celularresponsavel
 * @property integer $ed47_situacaodocumentacao
 * @property string $ed47_cartaosus
 * @property integer $ed47_tiposanguineo
 * @property string $ed47_municipioestrangeiro
 * @property integer $ed47_paisresidencia
 * @property integer $ed47_localizacaodiferenciada
 * @property string $ed47_rnm
 * @property string $ed47_rnmresponsavel
 * @property string $ed47_visto
 * @property string $ed47_vistoresponsavel
 */
class Aluno extends Model
{
    protected $table = 'escola.aluno';
    protected $primaryKey = 'ed47_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    protected $dates = [
        'ed47_d_cadast',
        'ed47_d_dtemissao',
        'ed47_d_dthabilitacao',
        'ed47_d_dtvencimento',
        'ed47_d_nasc',
        'ed47_d_ultalt',
        'ed47_d_identdtexp',
    ];

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->ed47_i_codigo;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->ed47_v_nome;
    }

    /**
     * @return string
     */
    public function getEndereco()
    {
        return $this->ed47_v_ender;
    }

    /**
     * @return string
     */
    public function getComplemento()
    {
        return $this->ed47_v_compl;
    }

    /**
     * @return string
     */
    public function getBairro()
    {
        return $this->ed47_v_bairro;
    }

    /**
     * @return string
     */
    public function getCep()
    {
        return $this->ed47_v_cep;
    }

    /**
     * @return string
     */
    public function getRaca()
    {
        return $this->ed47_c_raca;
    }

    /**
     * @return string
     */
    public function getCaixaPostal()
    {
        return $this->ed47_v_cxpostal;
    }

    /**
     * @return string
     */
    public function getTelefonia()
    {
        return $this->ed47_v_telef;
    }

    /**
     * @return Carbon
     */
    public function getDataCadastro()
    {
        return $this->ed47_d_cadast;
    }

    /**
     * @return string
     */
    public function getIdentidade()
    {
        return $this->ed47_v_ident;
    }

    /**
     * @return int
     */
    public function getLogin()
    {
        return $this->ed47_i_login;
    }

    /**
     * @return string
     */
    public function getNomeResponsavel()
    {
        return $this->ed47_c_nomeresp;
    }

    /**
     * @return string
     */
    public function getEmailResponsavel()
    {
        return $this->ed47_c_emailresp;
    }

    /**
     * @return string
     */
    public function getObservacoes()
    {
        return $this->ed47_t_obs;
    }

    /**
     * @return string
     */
    public function getTransporte()
    {
        return $this->ed47_c_transporte;
    }

    /**
     * @return string
     */
    public function getZona()
    {
        return $this->ed47_c_zona;
    }

    /**
     * @return string
     */
    public function getCertidaoTipo()
    {
        return $this->ed47_c_certidaotipo;
    }

    /**
     * @return string
     */
    public function getCertidaoNumero()
    {
        return $this->ed47_c_certidaonum;
    }

    /**
     * @return string
     */
    public function getCertidaoLivro()
    {
        return $this->ed47_c_certidaolivro;
    }

    /**
     * @return string
     */
    public function getCertidaoFolha()
    {
        return $this->ed47_c_certidaofolha;
    }

    /**
     * @return string
     */
    public function getCerticaoCartorio()
    {
        return $this->ed47_c_certidaocart;
    }

    /**
     * @return Carbon
     */
    public function getCertidaoData()
    {
        return $this->ed47_c_certidaodata;
    }

    /**
     * @return string
     */
    public function getNis()
    {
        return $this->ed47_c_nis;
    }

    /**
     * @return string
     */
    public function getBolsaFamilia()
    {
        return $this->ed47_c_bolsafamilia;
    }

    /**
     * @return string
     */
    public function getPassivo()
    {
        return $this->ed47_c_passivo;
    }

    /**
     * @return Carbon
     */
    public function getDataEmissao()
    {
        return $this->ed47_d_dtemissao;
    }

    /**
     * @return Carbon
     */
    public function getDataHabilitacao()
    {
        return $this->ed47_d_dthabilitacao;
    }

    /**
     * @return Carbon
     */
    public function getDataVencimento()
    {
        return $this->ed47_d_dtvencimento;
    }

    /**
     * @return Carbon
     */
    public function getDataNascimento()
    {
        return $this->ed47_d_nasc;
    }

    /**
     * @return Carbon
     */
    public function getDataUltimaAtualizacao()
    {
        return $this->ed47_d_ultalt;
    }

    /**
     * @return int
     */
    public function getEstadoCivil()
    {
        return $this->ed47_i_estciv;
    }

    /**
     * @return int
     */
    public function getNacionalidade()
    {
        return $this->ed47_i_nacion;
    }

    /**
     * @return string
     */
    public function getCategoria()
    {
        return $this->ed47_v_categoria;
    }

    /**
     * @return string
     */
    public function getCnh()
    {
        return $this->ed47_v_cnh;
    }

    /**
     * @return string
     */
    public function getContato()
    {
        return $this->ed47_v_contato;
    }

    /**
     * @return string
     */
    public function getCpf()
    {
        return $this->ed47_v_cpf;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->ed47_v_email;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->ed47_v_fax;
    }

    /**
     * @return string
     */
    public function getHora()
    {
        return $this->ed47_v_hora;
    }

    /**
     * @return string
     */
    public function getNomeMae()
    {
        return $this->ed47_v_mae;
    }

    /**
     * @return string
     */
    public function getNomePai()
    {
        return $this->ed47_v_pai;
    }

    /**
     * @return string
     */
    public function getProfissao()
    {
        return $this->ed47_v_profis;
    }

    /**
     * @return string
     */
    public function getSexo()
    {
        return $this->ed47_v_sexo;
    }

    /**
     * @return string
     */
    public function getTelefoneCelular()
    {
        return $this->ed47_v_telcel;
    }

    /**
     * @return string
     */
    public function getFoto()
    {
        return $this->ed47_c_foto;
    }

    /**
     * @return int
     */
    public function getOId()
    {
        return $this->ed47_o_oid;
    }

    /**
     * @return string
     */
    public function getCodigoInep()
    {
        return $this->ed47_c_codigoinep;
    }

    /**
     * @return int
     */
    public function getPais()
    {
        return $this->ed47_i_pais;
    }

    /**
     * @return Carbon
     */
    public function getIdentidadeDataExpedicao()
    {
        return $this->ed47_d_identdtexp;
    }

    /**
     * @return string
     */
    public function getOrgaoEmissor()
    {
        return $this->ed47_v_identcompl;
    }

    /**
     * @return string
     */
    public function getPassaporte()
    {
        return $this->ed47_c_passaporte;
    }

    /**
     * @return string
     */
    public function getNumeroPassaporte()
    {
        return $this->ed47_c_numero;
    }

    /**
     * @return string
     */
    public function getAtendimentoDiferenciado()
    {
        return $this->ed47_c_atenddifer;
    }

    /**
     * @return int
     */
    public function getFiliacao()
    {
        return $this->ed47_i_filiacao;
    }

    /**
     * @return int
     */
    public function getCensoUfNascimento()
    {
        return $this->ed47_i_censoufnat;
    }

    /**
     * @return int
     */
    public function getCensoMunicipioNascimento()
    {
        return $this->ed47_i_censomunicnat;
    }

    /**
     * @return int
     */
    public function getCensoOrigemEmissaoRg()
    {
        return $this->ed47_i_censoorgemissrg;
    }

    /**
     * @return int
     */
    public function getCensoUfIdentidade()
    {
        return $this->ed47_i_censoufident;
    }

    /**
     * @return int
     */
    public function getCensoUfCertidao()
    {
        return $this->ed47_i_censoufcert;
    }

    /**
     * @return int
     */
    public function getCensoMunicipioCertidao()
    {
        return $this->ed47_i_censomuniccert;
    }

    /**
     * @return int
     */
    public function getCensoUfEndereco()
    {
        return $this->ed47_i_censoufend;
    }

    /**
     * @return int
     */
    public function getCensoMunicipioEndereco()
    {
        return $this->ed47_i_censomunicend;
    }

    /**
     * @return int
     */
    public function getUtilizaTransportePublico()
    {
        return $this->ed47_i_transpublico;
    }

    /**
     * @return int
     */
    public function getAtendimentoEspecializado()
    {
        return $this->ed47_i_atendespec;
    }

    /**
     * @return int
     */
    public function getCensoCartorio()
    {
        return $this->ed47_i_censocartorio;
    }

    /**
     * @return string
     */
    public function getCertidaoMatricula()
    {
        return $this->ed47_certidaomatricula;
    }

    /**
     * @return string
     */
    public function getCelularResponsavel()
    {
        return $this->ed47_celularresponsavel;
    }

    /**
     * @return int
     */
    public function getSituacaoDocumentacao()
    {
        return $this->ed47_situacaodocumentacao;
    }

    /**
     * @return string
     */
    public function getCartaoSus()
    {
        return $this->ed47_cartaosus;
    }

    /**
     * @return int
     */
    public function getTipoSanguineo()
    {
        return $this->ed47_tiposanguineo;
    }

    /**
     * @return string
     */
    public function getMunicipioEstrangeiro()
    {
        return $this->ed47_municipioestrangeiro;
    }

    /**
     * @return int
     */
    public function getPaisResidencia()
    {
        return $this->ed47_paisresidencia;
    }

    /**
     * @return int
     */
    public function getLocalizacaoDiferenciada()
    {
        return $this->ed47_localizacaodiferenciada;
    }

    /**
     * @param $ed47_i_codigo
     */
    public function setCodigo($ed47_i_codigo)
    {
        $this->ed47_i_codigo = $ed47_i_codigo;
    }

    /**
     * @param $ed47_v_nome
     */
    public function setNome($ed47_v_nome)
    {
        $this->ed47_v_nome = $ed47_v_nome;
    }

    /**
     * @param $ed47_v_ender
     */
    public function setEndereco($ed47_v_ender)
    {
        $this->ed47_v_ender = $ed47_v_ender;
    }

    /**
     * @param $ed47_v_compl
     * @return mixed
     */
    public function setComplemento($ed47_v_compl)
    {
        $this->ed47_v_compl = $ed47_v_compl;
    }

    /**
     * @param $ed47_v_bairro
     */
    public function setBairro($ed47_v_bairro)
    {
        $this->ed47_v_bairro = $ed47_v_bairro;
    }

    /**
     * @param $ed47_v_cep
     */
    public function setCep($ed47_v_cep)
    {
        $this->ed47_v_cep = $ed47_v_cep;
    }

    /**
     * @param $ed47_c_raca
     */
    public function setRaca($ed47_c_raca)
    {
        $this->ed47_c_raca = $ed47_c_raca;
    }

    /**
     * @param $ed47_v_cxpostal
     */
    public function setCaixaPostal($ed47_v_cxpostal)
    {
        $this->ed47_v_cxpostal = $ed47_v_cxpostal;
    }

    /**
     * @param $ed47_v_telef
     */
    public function setTelefonia($ed47_v_telef)
    {
        $this->ed47_v_telef = $ed47_v_telef;
    }

    /**
     * @param $ed47_d_cadast
     * @return mixed
     */
    public function setDataCadastro($ed47_d_cadast)
    {
        $this->ed47_d_cadast = $ed47_d_cadast;
    }

    /**
     * @param $ed47_v_ident
     */
    public function setIdentidade($ed47_v_ident)
    {
        $this->ed47_v_ident = $ed47_v_ident;
    }

    /**
     * @param $ed47_i_login
     */
    public function setLogin($ed47_i_login)
    {
        $this->ed47_i_login = $ed47_i_login;
    }

    /**
     * @param $ed47_c_nomeresp
     * @return mixed
     */
    public function setNomeResponsavel($ed47_c_nomeresp)
    {
        $this->ed47_c_nomeresp = $ed47_c_nomeresp;
    }

    /**
     * @param $ed47_c_emailresp
     * @return mixed
     */
    public function setEmailResponsavel($ed47_c_emailresp)
    {
        $this->ed47_c_emailresp = $ed47_c_emailresp;
    }

    /**
     * @param $ed47_t_obs
     * @return mixed
     */
    public function setObservacoes($ed47_t_obs)
    {
        $this->ed47_t_obs = $ed47_t_obs;
    }

    /**
     * @param $ed47_c_transporte
     */
    public function setTransporte($ed47_c_transporte)
    {
        $this->ed47_c_transporte = $ed47_c_transporte;
    }

    /**
     * @param $ed47_c_zona
     */
    public function setZona($ed47_c_zona)
    {
        $this->ed47_c_zona = $ed47_c_zona;
    }

    /**
     * @param $ed47_c_certidaotipo
     */
    public function setCertidaoTipo($ed47_c_certidaotipo)
    {
        $this->ed47_c_certidaotipo = $ed47_c_certidaotipo;
    }

    /**
     * @param $ed47_c_certidaonum
     */
    public function setCertidaoNumero($ed47_c_certidaonum)
    {
        $this->ed47_c_certidaonum = $ed47_c_certidaonum;
    }

    /**
     * @param $ed47_c_certidaolivro
     */
    public function setCertidaoLivro($ed47_c_certidaolivro)
    {
        $this->ed47_c_certidaolivro = $ed47_c_certidaolivro;
    }

    /**
     * @param $ed47_c_certidaofolha
     */
    public function setCertidaoFolha($ed47_c_certidaofolha)
    {
        $this->ed47_c_certidaofolha = $ed47_c_certidaofolha;
    }

    /**
     * @param $ed47_c_certidaocart
     */
    public function setCerticaoCartorio($ed47_c_certidaocart)
    {
        $this->ed47_c_certidaocart = $ed47_c_certidaocart;
    }

    /**
     * @param $ed47_c_certidaodata
     */
    public function setCertidaoData($ed47_c_certidaodata)
    {
        $this->ed47_c_certidaodata = $ed47_c_certidaodata;
    }

    /**
     * @param $ed47_c_nis
     */
    public function setNis($ed47_c_nis)
    {
        $this->ed47_c_nis = $ed47_c_nis;
    }

    /**
     * @param $ed47_c_bolsafamilia
     */
    public function setBolsaFamilia($ed47_c_bolsafamilia)
    {
        $this->ed47_c_bolsafamilia = $ed47_c_bolsafamilia;
    }

    /**
     * @param $ed47_c_passivo
     */
    public function setPassivo($ed47_c_passivo)
    {
        $this->ed47_c_passivo = $ed47_c_passivo;
    }

    /**
     * @param $ed47_d_dtemissao
     */
    public function setDataEmissao($ed47_d_dtemissao)
    {
        $this->ed47_d_dtemissao = $ed47_d_dtemissao;
    }

    /**
     * @param $ed47_d_dthabilitacao
     */
    public function setDataHabilitacao($ed47_d_dthabilitacao)
    {
        $this->ed47_d_dthabilitacao = $ed47_d_dthabilitacao;
    }

    /**
     * @param $ed47_d_dtvencimento
     */
    public function setDataVencimento($ed47_d_dtvencimento)
    {
        $this->ed47_d_dtvencimento = $ed47_d_dtvencimento;
    }

    /**
     * @param $ed47_d_nasc
     * @return mixed
     */
    public function setDataNascimento($ed47_d_nasc)
    {
        $this->ed47_d_nasc = $ed47_d_nasc;
    }

    /**
     * @param $ed47_d_ultalt
     * @return mixed
     */
    public function setDataUltimaAtualizacao($ed47_d_ultalt)
    {
        $this->ed47_d_ultalt = $ed47_d_ultalt;
    }

    /**
     * @param $ed47_i_estciv
     */
    public function setEstadoCivil($ed47_i_estciv)
    {
        $this->ed47_i_estciv = $ed47_i_estciv;
    }

    /**
     * @param $ed47_i_nacion
     * @return mixed
     */
    public function setNacionalidade($ed47_i_nacion)
    {
        $this->ed47_i_nacion = $ed47_i_nacion;
    }

    /**
     * @param $ed47_v_categoria
     */
    public function setCategoria($ed47_v_categoria)
    {
        $this->ed47_v_categoria = $ed47_v_categoria;
    }

    /**
     * @param $ed47_v_cnh
     */
    public function setCnh($ed47_v_cnh)
    {
        $this->ed47_v_cnh = $ed47_v_cnh;
    }

    /**
     * @param $ed47_v_contato
     */
    public function setContato($ed47_v_contato)
    {
        $this->ed47_v_contato = $ed47_v_contato;
    }

    /**
     * @param $ed47_v_cpf
     */
    public function setCpf($ed47_v_cpf)
    {
        $this->ed47_v_cpf = $ed47_v_cpf;
    }

    /**
     * @param $ed47_v_email
     */
    public function setEmail($ed47_v_email)
    {
        $this->ed47_v_email = $ed47_v_email;
    }

    /**
     * @param $ed47_v_fax
     */
    public function setFax($ed47_v_fax)
    {
        $this->ed47_v_fax = $ed47_v_fax;
    }

    /**
     * @param $ed47_v_hora
     */
    public function setHora($ed47_v_hora)
    {
        $this->ed47_v_hora = $ed47_v_hora;
    }

    /**
     * @param $ed47_v_mae
     */
    public function setNomeMae($ed47_v_mae)
    {
        $this->ed47_v_mae = $ed47_v_mae;
    }

    /**
     * @param $ed47_v_pai
     */
    public function setNomePai($ed47_v_pai)
    {
        $this->ed47_v_pai = $ed47_v_pai;
    }

    /**
     * @param $ed47_v_profis
     */
    public function setProfissao($ed47_v_profis)
    {
        $this->ed47_v_profis = $ed47_v_profis;
    }

    /**
     * @param $ed47_v_sexo
     */
    public function setSexo($ed47_v_sexo)
    {
        $this->ed47_v_sexo = $ed47_v_sexo;
    }

    /**
     * @param $ed47_v_telcel
     * @return mixed
     */
    public function setTelefoneCelular($ed47_v_telcel)
    {
        $this->ed47_v_telcel = $ed47_v_telcel;
    }

    /**
     * @param $ed47_c_foto
     */
    public function setFoto($ed47_c_foto)
    {
        $this->ed47_c_foto = $ed47_c_foto;
    }

    /**
     * @param $ed47_o_oid
     */
    public function setOId($ed47_o_oid)
    {
        $this->ed47_o_oid = $ed47_o_oid;
    }

    /**
     * @param $ed47_c_codigoinep
     */
    public function setCodigoInep($ed47_c_codigoinep)
    {
        $this->ed47_c_codigoinep = $ed47_c_codigoinep;
    }

    /**
     * @param $ed47_i_pais
     */
    public function setPais($ed47_i_pais)
    {
        $this->ed47_i_pais = $ed47_i_pais;
    }

    /**
     * @param $ed47_d_identdtexp
     * @return mixed
     */
    public function setIdentidadeDataExpedicao($ed47_d_identdtexp)
    {
        $this->ed47_d_identdtexp = $ed47_d_identdtexp;
    }

    /**
     * @param $ed47_v_identcompl
     */
    public function setOrgaoEmissor($ed47_v_identcompl)
    {
        $this->ed47_v_identcompl = $ed47_v_identcompl;
    }

    /**
     * @param $ed47_c_passaporte
     */
    public function setPassaporte($ed47_c_passaporte)
    {
        $this->ed47_c_passaporte = $ed47_c_passaporte;
    }

    /**
     * @param $ed47_c_numero
     * @return mixed
     */
    public function setNumeroPassaporte($ed47_c_numero)
    {
        $this->ed47_c_numero = $ed47_c_numero;
    }

    /**
     * @param $ed47_c_atenddifer
     * @return mixed
     */
    public function setAtendimentoDiferenciado($ed47_c_atenddifer)
    {
        $this->ed47_c_atenddifer = $ed47_c_atenddifer;
    }

    /**
     * @param $ed47_i_filiacao
     */
    public function setFiliacao($ed47_i_filiacao)
    {
        $this->ed47_i_filiacao = $ed47_i_filiacao;
    }

    /**
     * @param $ed47_i_censoufnat
     * @return mixed
     */
    public function setCensoUfNascimento($ed47_i_censoufnat)
    {
        $this->ed47_i_censoufnat = $ed47_i_censoufnat;
    }

    /**
     * @param $ed47_i_censomunicnat
     * @return mixed
     */
    public function setCensoMunicipioNascimento($ed47_i_censomunicnat)
    {
        $this->ed47_i_censomunicnat = $ed47_i_censomunicnat;
    }

    /**
     * @param $ed47_i_censoorgemissrg
     */
    public function setCensoOrigemEmissaoRg($ed47_i_censoorgemissrg)
    {
        $this->ed47_i_censoorgemissrg = $ed47_i_censoorgemissrg;
    }

    /**
     * @param $ed47_i_censoufident
     */
    public function setCensoUfIdentidade($ed47_i_censoufident)
    {
        $this->ed47_i_censoufident = $ed47_i_censoufident;
    }

    /**
     * @param $ed47_i_censoufcert
     */
    public function setCensoUfCertidao($ed47_i_censoufcert)
    {
        $this->ed47_i_censoufcert = $ed47_i_censoufcert;
    }

    /**
     * @param $ed47_i_censomuniccert
     * @return mixed
     */
    public function setCensoMunicipioCertidao($ed47_i_censomuniccert)
    {
        $this->ed47_i_censomuniccert = $ed47_i_censomuniccert;
    }

    /**
     * @param $ed47_i_censoufend
     */
    public function setCensoUfEndereco($ed47_i_censoufend)
    {
        $this->ed47_i_censoufend = $ed47_i_censoufend;
    }

    /**
     * @param $ed47_i_censomunicend
     * @return mixed
     */
    public function setCensoMunicipioEndereco($ed47_i_censomunicend)
    {
        $this->ed47_i_censomunicend = $ed47_i_censomunicend;
    }

    /**
     * @param $ed47_i_transpublico
     * @return mixed
     */
    public function setUtilizaTransportePublico($ed47_i_transpublico)
    {
        $this->ed47_i_transpublico = $ed47_i_transpublico;
    }

    /**
     * @param $ed47_i_atendespec
     * @return mixed
     */
    public function setAtendimentoEspecializado($ed47_i_atendespec)
    {
        $this->ed47_i_atendespec = $ed47_i_atendespec;
    }

    /**
     * @param $ed47_i_censocartorio
     */
    public function setCensoCartorio($ed47_i_censocartorio)
    {
        $this->ed47_i_censocartorio = $ed47_i_censocartorio;
    }

    /**
     * @param $ed47_certidaomatricula
     */
    public function setCertidaoMatricula($ed47_certidaomatricula)
    {
        $this->ed47_certidaomatricula = $ed47_certidaomatricula;
    }

    /**
     * @param $ed47_celularresponsavel
     */
    public function setCelularResponsavel($ed47_celularresponsavel)
    {
        $this->ed47_celularresponsavel = $ed47_celularresponsavel;
    }

    /**
     * @param $ed47_situacaodocumentacao
     */
    public function setSituacaoDocumentacao($ed47_situacaodocumentacao)
    {
        $this->ed47_situacaodocumentacao = $ed47_situacaodocumentacao;
    }

    /**
     * @param $ed47_cartaosus
     */
    public function setCartaoSus($ed47_cartaosus)
    {
        $this->ed47_cartaosus = $ed47_cartaosus;
    }

    /**
     * @param $ed47_tiposanguineo
     */
    public function setTipoSanguineo($ed47_tiposanguineo)
    {
        $this->ed47_tiposanguineo = $ed47_tiposanguineo;
    }

    /**
     * @param $ed47_municipioestrangeiro
     */
    public function setMunicipioEstrangeiro($ed47_municipioestrangeiro)
    {
        $this->ed47_municipioestrangeiro = $ed47_municipioestrangeiro;
    }

    /**
     * @param $ed47_paisresidencia
     */
    public function setPaisResidencia($ed47_paisresidencia)
    {
        $this->ed47_paisresidencia = $ed47_paisresidencia;
    }

    /**
     * @param $ed47_localizacaodiferenciada
     */
    public function setLocalizacaoDiferenciada($ed47_localizacaodiferenciada)
    {
        $this->ed47_localizacaodiferenciada = $ed47_localizacaodiferenciada;
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'ed60_i_aluno', 'ed47_i_codigo');
    }

    public function paisNascimento()
    {
        return $this->belongsTo(Pais::class, 'ed47_i_pais', 'ed228_i_codigo');
    }

    public function historicos()
    {
        return $this->hasMany(Historico::class, 'ed61_i_aluno', 'ed47_i_codigo');
    }

    public function alunoNecessidade()
    {
        return $this->hasMany(AlunoNecessidade::class, 'ed214_i_aluno', 'ed47_i_codigo');
    }

    public function necessidadeSubdivisaoAluno()
    {
        return $this->hasMany(NecessidadeSubdivisaoAluno::class, 'ed187_aluno', 'ed47_i_codigo');
    }

    /**
     * @param $ed47_visto
     */
    public function setVisto($ed47_visto)
    {
        $this->ed47_visto = $ed47_visto;
    }

    /**
     * @return string $ed47_visto
     */
    public function getVisto()
    {
        return $this->ed47_visto;
    }

    /**
     * @return string $ed47_vistoresponsavel
     */
    public function getVistoResponsavel()
    {
        return $this->ed47_vistoresponsavel;
    }

    /**
     * @param $ed47_vistoresponsavel
     */
    public function setVistoResponsavel($ed47_vistoresponsavel)
    {
        $this->ed47_vistoresponsavel = $ed47_vistoresponsavel;
    }

    /**
     * @param $ed47_rnm
     */
    public function setRnm($ed47_rnm)
    {
        $this->ed47_rnm = $ed47_rnm;
    }

    /**
     * @return string $ed47_rnm
     */
    public function getRnm()
    {
        return $this->ed47_rnm;
    }

    /**
     * @param $ed47_rnmresponsavel
     */
    public function setRnmResponsavel($ed47_rnmresponsavel)
    {
        $this->ed47_rnmresponsavel = $ed47_rnmresponsavel;
    }

    /**
     * @return string $ed47_rnmresponsavel
     */
    public function getRnmResponsavel()
    {
        return $this->ed47_rnmresponsavel;
    }
}
