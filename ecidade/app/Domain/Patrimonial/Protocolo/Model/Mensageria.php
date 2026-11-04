<?php

namespace App\Domain\Patrimonial\Protocolo\Model;

use App\Domain\Patrimonial\Protocolo\Model\Processo\AndamentoProcessoInterno;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MensageriaProtocolo
 * @package App\Domain\Patrimonial\Ouvidoria\Model
 *
 * @property int p123_id
 * @property string p123_mensagem
 * @property timestamp p123_data_criacao
 * @property timestamp p123_ultima_atualizacao
 * @property timestamp p123_data_visualizada
 * @property int p123_resposta_mensagem
 * @property boolean p123_interna
 * @property int p123_processo
 * @property int p123_despacho
 * @property string p123_cpfcnpj
 * @property string p123_nome
 */

class Mensageria extends Model
{
    protected $table = 'protocolo.mensageria';
    protected $primaryKey = 'p123_id';
    public $timestamps = false;
    protected $fillable = [
        'p123_mensagem',
        'p123_data_criacao',
        'p123_ultima_atualizacao',
        'p123_resposta_mensagem',
        'p123_data_visualizada',
        'p123_interna',
        'p123_processo',
        'p123_despacho',
        'p123_cpfcnpj',
        'p123_nome',
    ];


    public function documentos()
    {
        return $this->hasMany(MensageriaDocumento::class, 'p124_mensagem', 'p123_id');
    }

    public function referencia()
    {
        return $this->belongsTo(Mensageria::class, 'p123_resposta_mensagem', 'p123_id');
    }

    public function despacho()
    {
        return $this->belongsTo(AndamentoProcessoInterno::class, 'p123_despacho', 'p78_sequencial');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->p123_id;
    }

    /**
     * @param int $p123_id
     */
    public function setId($p123_id)
    {
        $this->p123_id = $p123_id;
    }

    /**
     * @return string
     */
    public function getMensagem()
    {
        return $this->p123_mensagem;
    }

    /**
     * @param string $p123_mensagem
     */
    public function setMensagem($p123_mensagem)
    {
        $this->p123_mensagem = $p123_mensagem;
    }

    /**
     * @return timestamp
     */
    public function getDataCriacao()
    {
        return $this->p123_data_criacao;
    }

    /**
     * @param timestamp $p123_data_criacao
     */
    public function setDataCriacao($p123_data_criacao)
    {
        $this->p123_data_criacao = $p123_data_criacao;
    }

    /**
     * @return timestamp
     */
    public function getUltimaAtualizacao()
    {
        return $this->p123_ultima_atualizacao;
    }

    /**
     * @param timestamp $p123_ultima_atualizacao
     */
    public function setUltimaAtualizacao($p123_ultima_atualizacao)
    {
        $this->p123_ultima_atualizacao = $p123_ultima_atualizacao;
    }

    /**
     * @return timestamp
     */
    public function getDataVisualizada()
    {
        return $this->p123_data_visualizada;
    }

    /**
     * @param timestamp $p123_data_visualizada
     */
    public function setDataVisualizada($p123_data_visualizada)
    {
        $this->p123_data_visualizada = $p123_data_visualizada;
    }

    /**
     * @return int
     */
    public function getRespostaMensagem()
    {
        return $this->p123_resposta_mensagem;
    }

    /**
     * @param int $p123_resposta_mensagem
     */
    public function setRespostaMensagem($p123_resposta_mensagem)
    {
        $this->p123_resposta_mensagem = $p123_resposta_mensagem;
    }

    /**
     * @return int
     */
    public function getProcesso()
    {
        return $this->p123_processo;
    }

    /**
     * @param int $p123_processo
     */
    public function setProcesso($p123_processo)
    {
        $this->p123_processo = $p123_processo;
    }

    /**
     * @return boolean
     */
    public function getInterna()
    {
        return $this->p123_interna;
    }

    /**
     * @param boolean $p123_interna
     */
    public function setInterna($p123_interna)
    {
        $this->p123_interna = $p123_interna;
    }

    /**
     * @return int
     */
    public function getDespacho()
    {
        return $this->p123_despacho;
    }

    /**
     * @param int $p123_despacho
     */
    public function setDespacho($p123_despacho)
    {
        $this->p123_despacho = $p123_despacho;
    }

    /**
     * @return string
     */
    public function getCpfCnpj()
    {
        return $this->p123_cpfcnpj;
    }

    /**
     * @param string $p123_cpfcnpj
     */
    public function setCpfCnpj($p123_cpfcnpj)
    {
        $this->p123_cpfcnpj = $p123_cpfcnpj;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->p123_nome;
    }

    /**
     * @param string $p123_nome
     */
    public function setNome($p123_nome)
    {
        $this->p123_nome = $p123_nome;
    }
}
