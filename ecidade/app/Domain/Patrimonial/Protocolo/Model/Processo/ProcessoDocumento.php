<?php

namespace App\Domain\Patrimonial\Protocolo\Model\Processo;

use App\Domain\Patrimonial\Protocolo\Model\DocumentoSolicitacaoAssinatura;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class ProcessoDocumento
 * @package App\Domain\Patrimonial\Protocolo\Model\Processo
 *
 * @property int $p01_sequencial
 * @property int $p01_protprocesso
 * @property string $p01_descricao
 * @property oid $p01_documento
 * @property string $p01_nomedocumento
 * @property Date $p01_data
 * @property int $p01_procandamint
 * @property int $p01_usuario
 * @property boolean $p01_estorage
 * @property integer $p01_ordem
 * @property integer $p01_assinado_por
 * @property boolean $p01_assinado
 * @property string $p01_documento_hash
 * @property int $p01_c70codlan
 * @property string $p01_upload_name
 * @method hash(string $hash)
 */
class ProcessoDocumento extends Model
{
    protected $table = 'protocolo.protprocessodocumento';
    protected $primaryKey = 'p01_sequencial';
    public $timestamps = false;

    /**
     * @param int $p01_sequencial
     * @return $this
     */
    public function setCodigo($p01_sequencial)
    {
        $this->p01_sequencial = $p01_sequencial;
        return $this;
    }

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->p01_sequencial;
    }

    /**
     * @param int $p01_protprocesso
     * @return $this
     */
    public function setProcesso($p01_protprocesso)
    {
        $this->p01_protprocesso = $p01_protprocesso;
        return $this;
    }

    /**
     * @return int
     */
    public function getProcesso()
    {
        return $this->p01_protprocesso;
    }

    /**
     * @param String $p01_descricao
     * @return $this
     */
    public function setDescricao($p01_descricao)
    {
        $this->p01_descricao = $p01_descricao;
        return $this;
    }

    /**
     * @return String
     */
    public function getDescricao()
    {
        return $this->p01_descricao;
    }

    /**
     * @param Oid $p01_documento
     * @return $this
     */
    public function setDocumento($p01_documento)
    {
        $this->p01_documento = $p01_documento;
        return $this;
    }

    /**
     * @return Oid
     */
    public function getDocumento()
    {
        return $this->p01_documento;
    }

    /**
     * @param String $p01_nomedocumento
     * @return $this
     */
    public function setNomeDocumento($p01_nomedocumento)
    {
        $this->p01_nomedocumento = $p01_nomedocumento;
        return $this;
    }

    /**
     * @return String
     */
    public function getNomeDocumento()
    {
        return $this->p01_nomedocumento;
    }

    /**
     * @param Date $p01_data
     * @return $this
     */
    public function setData($p01_data)
    {
        $this->p01_data = $p01_data;
        return $this;
    }

    /**
     * @return Date
     */
    public function getData()
    {
        return $this->p01_data;
    }

    /**
     * @param int $p01_procandamint
     * @return $this
     */
    public function setAndamento($p01_procandamint)
    {
        $this->p01_procandamint = $p01_procandamint;
        return $this;
    }

    /**
     * @param int $p01_c70codlan
     * @return $this
     */
    public function setC70codlan($p01_c70codlan)
    {
        $this->p01_c70codlan = $p01_c70codlan;
        return $this;
    }

    /**
     * @return int
     */
    public function getC70codlan()
    {
        return $this->p01_c70codlan;
    }

    /**
     * @param string $p01_upload_name
     * @return $this
     */
    public function setNomeUpload($p01_upload_name)
    {
        $this->p01_upload_name = $p01_upload_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeUpload()
    {
        return $this->p01_upload_name;
    }

    /**
     * @return int
     */
    public function getAndamento()
    {
        return $this->p01_procandamint;
    }

    /**
     * @param int $p01_usuario
     * @return $this
     */
    public function setUsuario($p01_usuario)
    {
        $this->p01_usuario = $p01_usuario;
        return $this;
    }

    /**
     * @return int
     */
    public function getUsuario()
    {
        return $this->p01_usuario;
    }

    /**
     * @param boolean $p01_estorage
     * @return $this
     */
    public function setStorage($p01_estorage)
    {
        $this->p01_estorage = $p01_estorage;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getStorage()
    {
        return $this->p01_estorage;
    }

    /**
     * @param integer $p01_ordem
     * @return $this
     */
    public function setOrdem($p01_ordem)
    {
        $this->p01_ordem = $p01_ordem;
        return $this;
    }

    /**
     * @return integer
     */
    public function getOrdem()
    {
        return $this->p01_ordem;
    }

    public function getAssinado()
    {
        return $this->p01_assinado;
    }

    public function setAssinado($p01_assinado)
    {
        $this->p01_assinado = $p01_assinado;
        return $this;
    }

    public function getAssinadoPor()
    {
        return $this->p01_assinado_por;
    }

    public function setAssinadoPor($p01_assinado_por)
    {
        $this->p01_assinado_por = $p01_assinado_por;
        return $this;
    }

    public function scopeHash($query, $hash)
    {
        return $query->where('p01_documento_hash', $hash);
    }

    public function solicitacaoAssinatura()
    {
        return $this->hasMany(DocumentoSolicitacaoAssinatura::class, "documento_id", "p01_sequencial");
    }

    public function processo()
    {
        return $this->belongsTo(Processo::class, "p01_protprocesso", "p58_codproc");
    }
}
