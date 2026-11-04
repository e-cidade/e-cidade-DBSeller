<?php

namespace App\Domain\Patrimonial\Protocolo\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MensageriaProtocolo
 * @package App\Domain\Patrimonial\Ouvidoria\Model
 *
 * @property int p124_id
 * @property int p124_codigo_storage
 * @property string p124_nome_documento
 * @property string p124_cpfcnpj
 * @property int p124_mensagem
 */

class MensageriaDocumento extends Model
{
    protected $table = 'protocolo.mensageria_documento';
    protected $primaryKey = 'p124_id';
    public $timestamps = false;
    protected $fillable = [
        'p124_codigo_storage',
        'p124_nome_documento',
        'p124_cpfcnpj',
        'p124_mensagem',
    ];

    public function mensagem()
    {
        return $this->belongsTo(Mensageria::class, 'p124_mensagem', 'p123_id');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->p124_id;
    }

    /**
     * @param int $p124_id
     */
    public function setId($p124_id)
    {
        $this->p124_id = $p124_id;
    }

    /**
     * @return int
     */
    public function getCodigoStorage()
    {
        return $this->p124_codigo_storage;
    }

    /**
     * @param int $p124_codigo_storage
     */
    public function setCodigoStorage($p124_codigo_storage)
    {
        $this->p124_codigo_storage = $p124_codigo_storage;
    }

    /**
     * @return string
     */
    public function getNomeDocumento()
    {
        return $this->p124_nome_documento;
    }

    /**
     * @param string $p124_nome_documento
     */
    public function setNomeDocumento($p124_nome_documento)
    {
        $this->p124_nome_documento = $p124_nome_documento;
    }

    /**
     * @return string
     */
    public function getCpfCnpj()
    {
        return $this->p124_cpfcnpj;
    }

    /**
     * @param string $p124_cpfcnpj
     */
    public function setCpf($p124_cpfcnpj)
    {
        $this->p124_cpfcnpj = $p124_cpfcnpj;
    }

    /**
     * @return int
     */
    public function getMensage()
    {
        return $this->p124_mensagem;
    }

    /**
     * @param int $p124_mensagem
     */
    public function setMensagem($p124_mensagem)
    {
        $this->p124_mensagem = $p124_mensagem;
    }
}
