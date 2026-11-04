<?php

namespace App\Domain\Configuracao\Instituicao\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Configuracao\Departamento\Models\Departamento;

/**
 * Class Atendimento
 *
 * @package App\Domain\Configuracao\Model
 *
 * @property integer codigo
 * @property string nomeinst
 * @property string ender
 * @property string munic
 * @property string uf
 * @property string telef
 * @property string email
 * @property integer ident
 * @property float tx_banc
 * @property string numbanco
 * @property string url
 * @property string logo
 * @property string figura
 * @property Date dtcont
 * @property integer diario
 * @property string pref
 * @property string vicepref
 * @property string fax
 * @property string cgc
 * @property string cep
 * @property boolean tpropri
 * @property boolean tsocios
 * @property boolean prefeitura
 * @property string bairro
 * @property integer numcgm
 * @property string codtrib
 * @property integer tribinst
 * @property integer segmento
 * @property integer formvencfebraban
 * @property integer numero
 * @property string nomedebconta
 * @property integer db21_tipoinstit
 * @property integer db21_ativo
 * @property integer db21_regracgmiss
 * @property integer db21_regracgmiptu
 * @property integer db21_codcli
 * @property string nomeinstabrev
 * @property boolean db21_usasisagua
 * @property string db21_codigomunicipoestado
 * @property Date db21_datalimite
 * @property Date db21_criacao
 * @property string db21_compl
 * @property oid db21_imgmarcadagua
 * @property integer db21_esfera
 * @property integer db21_tipopoder
 * @property integer db21_codtj
 * @property string db21_codsiconfi
 * @property boolean db21_unidade_gestora_rpps
 * @property integer db21_esfera_op
 * @property float db21_valor_teto_remuneratorio
 * @property boolean db21_ente_federativo_resp
 * @method static Builder|DBConfig isPrefeitura()
 */

class DBConfig extends Model
{
    protected $table = 'configuracoes.db_config';
    protected $primaryKey = 'codigo';
    public $timestamps = false;

    public function scopeIsPrefeitura(Builder $query)
    {
        return $query->where("prefeitura", true);
    }

    /**
     * @param integer $codigo
     * @return this
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param string $nomeinst
     * @return this
     */
    public function setNome($nomeinst)
    {
        $this->nomeinst = $nomeinst;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nomeinst;
    }

    /**
     * @param string $ender
     * @return this
     */
    public function setEndereco($ender)
    {
        $this->ender = $ender;
        return $this;
    }

    /**
     * @return string
     */
    public function getEndereco()
    {
        return $this->ender;
    }

    /**
     * @param string $munic
     * @return this
     */
    public function setMunicipio($munic)
    {
        $this->munic = $munic;
        return $this;
    }

    /**
     * @return string
     */
    public function getMunicipio()
    {
        return $this->munic;
    }

    /**
     * @param string $uf
     * @return this
     */
    public function setUf($uf)
    {
        $this->uf = $uf;
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
     * @param string $telef
     * @return this
     */
    public function setTelefone($telef)
    {
        $this->telef = $telef;
        return $this;
    }

    /**
     * @return string
     */
    public function getTelefone()
    {
        return $this->telef;
    }

    /**
     * @param string $email
     * @return this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param integer $ident
     * @return this
     */
    public function setIdent($ident)
    {
        $this->ident = $ident;
        return $this;
    }

    /**
     * @return integer
     */
    public function getIdent()
    {
        return $this->ident;
    }

    /**
     * @param float $taxaBanco
     * @return this
     */
    public function setTaxaBanco($taxaBanco)
    {
        $this->tx_banc = $taxaBanco;
        return $this;
    }

    /**
     * @return float
     */
    public function getTaxaBanco()
    {
        return $this->tx_banc;
    }

    /**
     * @param string $numbanco
     * @return this
     */
    public function setNumBanco($numbanco)
    {
        $this->numbanco = $numbanco;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumBanco()
    {
        return $this->numbanco;
    }

    /**
     * @param string $url
     * @return this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $logo
     * @return this
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $figura
     * @return this
     */
    public function setFigura($figura)
    {
        $this->figura = $figura;
        return $this;
    }

    /**
     * @return string
     */
    public function getFigura()
    {
        return $this->figura;
    }

    /**
     * @param date $dtcont
     * @return this
     */
    public function setDataContabilitade($dtcont)
    {
        $this->dtcont = $dtcont;
        return $this;
    }

    /**
     * @return date
     */
    public function getDataContabilitade()
    {
        return $this->dtcont;
    }

    /**
     * @param integer $diario
     * @return this
     */
    public function setDiario($diario)
    {
        $this->diario = $diario;
        return $this;
    }

    /**
     * @return integer
     */
    public function getDiario()
    {
        return $this->diario;
    }

    /**
     * @param string $pref
     * @return this
     */
    public function setPrefeito($pref)
    {
        $this->pref = $pref;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrefeito()
    {
        return $this->pref;
    }

    /**
     * @param string $vicepref
     * @return this
     */
    public function setVicePrefeito($vicepref)
    {
        $this->vicepref = $vicepref;
        return $this;
    }

    /**
     * @return string
     */
    public function getVicePrefeito()
    {
        return $this->vicepref;
    }

    /**
     * @param string $fax
     * @return this
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
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
     * @param string $cgc
     * @return this
     */
    public function setCgc($cgc)
    {
        $this->cgc = $cgc;
        return $this;
    }

    /**
     * @return string
     */
    public function getCgc()
    {
        return $this->cgc;
    }

    /**
     * @param string $cep
     * @return this
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
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
     * @param boolean $tpropri
     * @return this
     */
    public function setTPropri($tpropri)
    {
        $this->tpropri = $tpropri;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getTPropri()
    {
        return $this->tpropri;
    }

    /**
     * @param boolean $tsocios
     * @return this
     */
    public function setTSocios($tsocios)
    {
        $this->tsocios = $tsocios;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getTSocios()
    {
        return $this->tsocios;
    }

    /**
     * @param boolean $prefeitura
     * @return this
     */
    public function setPrefeitura($prefeitura)
    {
        $this->prefeitura = $prefeitura;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrefeitura()
    {
        return $this->prefeitura;
    }

    /**
     * @param string $bairro
     * @return this
     */
    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
        return $this;
    }

    /**
     * @return string
     */
    public function getBairro()
    {
        return $this->bairro;
    }

    /**
     * @param integer $numcgm
     * @return this
     */
    public function setCgm($numcgm)
    {
        $this->numcgm = $numcgm;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCgm()
    {
        return $this->numcgm;
    }

    /**
     * @param string $codtrib
     * @return this
     */
    public function setCodTrib($codtrib)
    {
        $this->codtrib = $codtrib;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodTrib()
    {
        return $this->codtrib;
    }

    /**
     * @param integer $tribinst
     * @return this
     */
    public function setTribInst($tribinst)
    {
        $this->tribinst = $tribinst;
        return $this;
    }

    /**
     * @return integer
     */
    public function getTribInst()
    {
        return $this->tribinst;
    }

    /**
     * @param integer $segmento
     * @return this
     */
    public function setSegmento($segmento)
    {
        $this->segmento = $segmento;
        return $this;
    }

    /**
     * @return integer
     */
    public function getSegmento()
    {
        return $this->segmento;
    }

    /**
     * @param integer $formvencfebraban
     * @return this
     */
    public function setFormaVencimentoFebraban($formvencfebraban)
    {
        $this->formvencfebraban = $formvencfebraban;
        return $this;
    }

    /**
     * @return integer
     */
    public function getFormaVencimentoFebraban()
    {
        return $this->formvencfebraban;
    }

    /**
     * @param integer $numero
     * @return this
     */
    public function setnumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * @return integer
     */
    public function getnumero()
    {
        return $this->numero;
    }

    /**
     * @param string $nomedebconta
     * @return this
     */
    public function setNomeDebitoConta($nomedebconta)
    {
        $this->nomedebconta = $nomedebconta;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeDebitoConta()
    {
        return $this->nomedebconta;
    }

    /**
     * @param integer $db21_tipoinstit
     * @return this
     */
    public function setTipoInstituicao($db21_tipoinstit)
    {
        $this->db21_tipoinstit = $db21_tipoinstit;
        return $this;
    }

    /**
     * @return integer
     */
    public function getTipoInstituicao()
    {
        return $this->db21_tipoinstit;
    }

    /**
     * @param integer $db21_ativo
     * @return this
     */
    public function setAtivo($db21_ativo)
    {
        $this->db21_ativo = $db21_ativo;
        return $this;
    }

    /**
     * @return integer
     */
    public function getAtivo()
    {
        return $this->db21_ativo;
    }

    /**
     * @param integer $db21_regracgmiss
     * @return this
     */
    public function setRegraCgmIss($db21_regracgmiss)
    {
        $this->db21_regracgmiss = $db21_regracgmiss;
        return $this;
    }

    /**
     * @return integer
     */
    public function getRegraCgmIss()
    {
        return $this->db21_regracgmiss;
    }

    /**
     * @param integer $db21_regracgmiptu
     * @return this
     */
    public function setRegraCgmIptu($db21_regracgmiptu)
    {
        $this->db21_regracgmiptu = $db21_regracgmiptu;
        return $this;
    }

    /**
     * @return integer
     */
    public function getRegraCgmIptu()
    {
        return $this->db21_regracgmiptu;
    }

    /**
     * @param integer $db21_codcli
     * @return this
     */
    public function setCodCli($db21_codcli)
    {
        $this->db21_codcli = $db21_codcli;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCodCli()
    {
        return $this->db21_codcli;
    }

    /**
     * @param string $nomeinstabrev
     * @return this
     */
    public function setNomeInstaBrev($nomeinstabrev)
    {
        $this->nomeinstabrev = $nomeinstabrev;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeInstaBrev()
    {
        return $this->nomeinstabrev;
    }

    /**
     * @param boolean $db21_usasisagua
     * @return this
     */
    public function setUsaSisAgua($db21_usasisagua)
    {
        $this->db21_usasisagua = $db21_usasisagua;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getUsaSisAgua()
    {
        return $this->db21_usasisagua;
    }

    /**
     * @param string $db21_codigomunicipoestado
     * @return this
     */
    public function setCodigoMunicipioEstado($db21_codigomunicipoestado)
    {
        $this->ov01_sequencial = $db21_codigomunicipoestado;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodigoMunicipioEstado()
    {
        return $this->db21_codigomunicipoestado;
    }

    /**
     * @param date $db21_datalimite
     * @return this
     */
    public function setDataLimite($db21_datalimite)
    {
        $this->db21_datalimite = $db21_datalimite;
        return $this;
    }

    /**
     * @return date
     */
    public function getDataLimite()
    {
        return $this->db21_datalimite;
    }

    /**
     * @param date $db21_criacao
     * @return this
     */
    public function setCriacao($db21_criacao)
    {
        $this->db21_criacao = $db21_criacao;
        return $this;
    }

    /**
     * @return date
     */
    public function getCriacao()
    {
        return $this->db21_criacao;
    }

    /**
     * @param string $db21_compl
     * @return this
     */
    public function setComplemento($db21_compl)
    {
        $this->db21_compl = $db21_compl;
        return $this;
    }

    /**
     * @return string
     */
    public function getComplemento()
    {
        return $this->db21_compl;
    }

    /**
     * @param oid $db21_imgmarcadagua
     * @return this
     */
    public function setImgMarcaAgua($db21_imgmarcadagua)
    {
        $this->db21_imgmarcadagua = $db21_imgmarcadagua;
        return $this;
    }

    /**
     * @return oid
     */
    public function getImgMarcaAgua()
    {
        return $this->db21_imgmarcadagua;
    }

    /**
     * @param integer $db21_esfera
     * @return this
     */
    public function setEsfera($db21_esfera)
    {
        $this->db21_esfera = $db21_esfera;
        return $this;
    }

    /**
     * @return integer
     */
    public function getEsfera()
    {
        return $this->db21_esfera;
    }

    /**
     * @param integer $db21_tipopoder
     * @return this
     */
    public function setTipoPoder($db21_tipopoder)
    {
        $this->db21_tipopoder = $db21_tipopoder;
        return $this;
    }

    /**
     * @return integer
     */
    public function getTipoPoder()
    {
        return $this->db21_tipopoder;
    }

    /**
     * @param integer $db21_codtj
     * @return this
     */
    public function setCodTJ($db21_codtj)
    {
        $this->db21_codtj = $db21_codtj;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCodTJ()
    {
        return $this->db21_codtj;
    }

    /**
     * @param string $db21_codsiconfi
     * @return this
     */
    public function setCodSiconfi($db21_codsiconfi)
    {
        $this->db21_codsiconfi = $db21_codsiconfi;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodSiconfi()
    {
        return $this->db21_codsiconfi;
    }

    /**
     * @param boolean $db21_unidade_gestora_rpps
     * @return this
     */
    public function setUnidadeGestoraRpps($db21_unidade_gestora_rpps)
    {
        $this->db21_unidade_gestora_rpps = $db21_unidade_gestora_rpps;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getUnidadeGestoraRpps()
    {
        return $this->db21_unidade_gestora_rpps;
    }

    /**
     * @param integer $db21_esfera_op
     * @return this
     */
    public function setEsferaOp($db21_esfera_op)
    {
        $this->db21_esfera_op = $db21_esfera_op;
        return $this;
    }

    /**
     * @return integer
     */
    public function getEsferaOp()
    {
        return $this->db21_esfera_op;
    }

    /**
     * @param float $db21_valor_teto_remuneratorio
     * @return this
     */
    public function setValorTetoRemuneratorio($db21_valor_teto_remuneratorio)
    {
        $this->db21_valor_teto_remuneratorio = $db21_valor_teto_remuneratorio;
        return $this;
    }

    /**
     * @return float
     */
    public function getValorTetoRemuneratorio()
    {
        return $this->db21_valor_teto_remuneratorio;
    }

    /**
     * @param boolean $db21_ente_federativo_resp
     * @return this
     */
    public function setEnteFederativoResp($db21_ente_federativo_resp)
    {
        $this->db21_ente_federativo_resp = $db21_ente_federativo_resp;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEnteFederativoResp()
    {
        return $this->db21_ente_federativo_resp;
    }

    public function scopeLikeNome($query, $nome = '')
    {
        return $query->where("nomeinst", "ilike", "%{$nome}%");
    }

    public function scopeInCodigo($query, array $codigo = [])
    {
        return $query->whereIn("codigo", $codigo);
    }

    public function scopeLikeCnpj($query, $cnpj)
    {
        return $query->where("cgc", "ilike", "%{$cnpj}%");
    }

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, "instit");
    }
}
