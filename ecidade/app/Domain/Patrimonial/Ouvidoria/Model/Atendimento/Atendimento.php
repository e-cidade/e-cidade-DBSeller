<?php

namespace App\Domain\Patrimonial\Ouvidoria\Model\Atendimento;

use Illuminate\Database\Eloquent\Model;
use ECidade\Lib\Session\DefaultSession;

/**
 * Class Atendimento
 * @package App\Domain\Patrimonial\Ouvidoria\Model\Atendimento
 * @property int ov01_sequencial
 * @property int ov01_situacaoouvidoriaatendimento
 * @property int ov01_tipoprocesso
 * @property int ov01_formareclamacao
 * @property int ov01_tipoidentificacao
 * @property int ov01_usuario
 * @property int ov01_depart
 * @property int ov01_instit
 * @property int ov01_numero
 * @property int ov01_anousu
 * @property Date ov01_dataatend
 * @property string ov01_horaatend
 * @property string ov01_requerente
 * @property string ov01_solicitacao
 * @property string ov01_executado
 * @property Cidadao cidadao
 * @method numero($numero)
 * @method ano($ano)
 * @method instituicao($instituicao)
 */
class Atendimento extends Model
{
    const ATIVO = 1;

    protected $table = 'ouvidoria.ouvidoriaatendimento';
    protected $primaryKey = 'ov01_sequencial';
    public $timestamps = false;

    protected $cidadao = null;
    protected $appends = array('cidadao');
    // protected $with = ['situacaoOuvidoriaAtendimento'];

    /**
     * setter Cidadao
     * @return this
     */
    public function setCidadao($cidadao)
    {
        $this->cidadao = $cidadao;
        return $this;
    }

    /**
     * setter Cgm
     * @return this
     */
    public function setCgm($cgm)
    {
        $this->cgm = $cgm;
        return $this;
    }

    /**
     * Get Cidadao
     *
     * @return Cidadao
     */
    public function getCidadaoAttribute()
    {
        return $this->cidadao;
    }

    /**
     * @param int $ov01_sequencial
     * @return this
     */
    public function setCodigo($ov01_sequencial)
    {
        $this->ov01_sequencial = $ov01_sequencial;
        return $this;
    }

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->ov01_sequencial;
    }

    /**
     * @param int $ov01_situacaoouvidoriaatendimento
     * @return this
     */
    public function setSituacao($ov01_situacaoouvidoriaatendimento)
    {
        $this->ov01_situacaoouvidoriaatendimento = $ov01_situacaoouvidoriaatendimento;
        return $this;
    }

    /**
     * @return int
     */
    public function getSituacao()
    {
        return $this->ov01_situacaoouvidoriaatendimento;
    }

    /**
     * @param int $ov01_tipoprocesso
     * @return this
     */
    public function setTipoProcesso($ov01_tipoprocesso)
    {
        $this->ov01_tipoprocesso = $ov01_tipoprocesso;
        return $this;
    }

    /**
     * @return int
     */
    public function getTipoProcesso()
    {
        return $this->ov01_tipoprocesso;
    }

    /**
     * @param int $ov01_formareclamacao
     * @return this
     */
    public function setFormaReclamacao($ov01_formareclamacao)
    {
        $this->ov01_formareclamacao = $ov01_formareclamacao;
        return $this;
    }

    /**
     * @return int
     */
    public function getFormaReclamacao()
    {
        return $this->ov01_formareclamacao;
    }

    /**
     * @param int $ov01_tipoidentificacao
     * @return this
     */
    public function setTipoIdentificacao($ov01_tipoidentificacao)
    {
        $this->ov01_tipoidentificacao = $ov01_tipoidentificacao;
        return $this;
    }

    /**
     * @return int
     */
    public function getTipoIdentificacao()
    {
        return $this->ov01_tipoidentificacao;
    }

    /**
     * @param int $ov01_usuario
     * @return this
     */
    public function setUsuario($ov01_usuario)
    {
        $this->ov01_usuario = $ov01_usuario;
        return $this;
    }

    /**
     * @return int
     */
    public function getUsuario()
    {
        return $this->ov01_usuario;
    }

    /**
     * @param int $ov01_depart
     * @return this
     */
    public function setDepartamento($ov01_depart)
    {
        $this->ov01_depart = $ov01_depart;
        return $this;
    }

    /**
     * @return int
     */
    public function getDepartamento()
    {
        return $this->ov01_depart;
    }

    /**
     * @param int $ov01_instit
     * @return this
     */
    public function setInstituicao($ov01_instit)
    {
        $this->ov01_instit = $ov01_instit;
        return $this;
    }

    /**
     * @return int
     */
    public function getInstituicao()
    {
        return $this->ov01_instit;
    }

    /**
     * @param int $ov01_numero
     * @return this
     */
    public function setNumero($ov01_numero)
    {
        $this->ov01_numero = $ov01_numero;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumero()
    {
        return $this->ov01_numero;
    }

    /**
     * @param int $ov01_anousu
     * @return this
     */
    public function setAno($ov01_anousu)
    {
        $this->ov01_anousu = $ov01_anousu;
        return $this;
    }

    /**
     * @return int
     */
    public function getAno()
    {
        return $this->ov01_anousu;
    }

    /**
     * @param int $ov01_dataatend
     * @return this
     */
    public function setData($ov01_dataatend)
    {
        $this->ov01_dataatend = $ov01_dataatend;
        return $this;
    }

    /**
     * @return int
     */
    public function getData()
    {
        return $this->ov01_dataatend;
    }

    /**
     * @param int $ov01_horaatend
     * @return this
     */
    public function setHora($ov01_horaatend)
    {
        $this->ov01_horaatend = $ov01_horaatend;
        return $this;
    }

    /**
     * @return int
     */
    public function getHora()
    {
        return $this->ov01_horaatend;
    }

    /**
     * @param int $ov01_requerente
     * @return this
     */
    public function setRequerente($ov01_requerente)
    {
        $this->ov01_requerente = $ov01_requerente;
        return $this;
    }

    /**
     * @return int
     */
    public function getRequerente()
    {
        return $this->ov01_requerente;
    }

    /**
     * @param int $ov01_solicitacao
     * @return this
     */
    public function setSolicitacao($ov01_solicitacao)
    {
        $this->ov01_solicitacao = $ov01_solicitacao;
        return $this;
    }

    /**
     * @return int
     */
    public function getSolicitacao()
    {
        return $this->ov01_solicitacao;
    }

    /**
     * @param int $ov01_executado
     * @return this
     */
    public function setExecutado($ov01_executado)
    {
        $this->ov01_executado = $ov01_executado;
        return $this;
    }

    /**
     * @return int
     */
    public function getExecutado()
    {
        return $this->ov01_executado;
    }

    public function atendimentoProcessoEletronico()
    {
        return $this->hasOne(AtendimentoProcessoEletronico::class, "ov33_ouvidoriaatendimento");
    }


    public function scopeAno($query, $ano)
    {
        return $query->where("ov01_anousu", $ano);
    }

    public function scopeInstituicao($query, $instituicao)
    {
        return  $query->where("ov01_instit", $instituicao);
    }

    public function scopeNumero($query, $numero)
    {
        return $query->where("ov01_numero", $numero);
    }
}
