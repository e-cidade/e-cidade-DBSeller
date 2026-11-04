<?php

namespace App\Domain\Patrimonial\Protocolo\Model\Processo;

use ECidade\Patrimonial\Protocolo\Modelo\AndamentoProcesso;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Transferencia
 *
 * @package App\Domain\Patrimonial\Protocolo\Model\Processo
 * @property int p62_codtran
 * @property Date p62_dttran
 * @property int p62_id_usuario
 * @property int p62_coddepto
 * @property int p62_id_usorec
 * @property int p62_coddeptorec
 * @property string p62_hora
 */
class Transferencia extends Model
{
    protected $table = 'protocolo.proctransfer';
    protected $primaryKey = 'p62_codtran';
    public $timestamps = false;

    /**
     * @param  int  $p62_codtran
     *
     * @return this
     */
    public function setCodigo($p62_codtran)
    {
        $this->p62_codtran = $p62_codtran;

        return $this;
    }

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->p62_codtran;
    }

    /**
     * @param  int  $p62_dttran
     *
     * @return this
     */
    public function setData($p62_dttran)
    {
        $this->p62_dttran = $p62_dttran;

        return $this;
    }

    /**
     * @return int
     */
    public function getData()
    {
        return $this->p62_dttran;
    }

    /**
     * @param  int  $p62_id_usuario
     *
     * @return this
     */
    public function setUsuario($p62_id_usuario)
    {
        $this->p62_id_usuario = $p62_id_usuario;

        return $this;
    }

    /**
     * @return int
     */
    public function getUsuario()
    {
        return $this->p62_id_usuario;
    }

    /**
     * @param  int  $p62_coddepto
     *
     * @return this
     */
    public function setDepartamento($p62_coddepto)
    {
        $this->p62_coddepto = $p62_coddepto;

        return $this;
    }

    /**
     * @return int
     */
    public function getDepartamento()
    {
        return $this->p62_coddepto;
    }

    /**
     * @param  int  $p62_id_usorec
     *
     * @return this
     */
    public function setUsuarioRecebimento($p62_id_usorec)
    {
        $this->p62_id_usorec = $p62_id_usorec;

        return $this;
    }

    /**
     * @return int
     */
    public function getUsuarioRecebimento()
    {
        return $this->p62_id_usorec;
    }

    /**
     * @param  int  $p62_coddeptorec
     *
     * @return this
     */
    public function setDepartamentoRecebimento($p62_coddeptorec)
    {
        $this->p62_coddeptorec = $p62_coddeptorec;

        return $this;
    }

    /**
     * @return int
     */
    public function getDepartamentoRecebimento()
    {
        return $this->p62_coddeptorec;
    }

    /**
     * @param  int  $p62_hora
     *
     * @return this
     */
    public function setHora($p62_hora)
    {
        $this->p62_hora = $p62_hora;

        return $this;
    }

    /**
     * @return int
     */
    public function getHora()
    {
        return $this->p62_hora;
    }

    /**
     * @return BelongsToMany
     */
    public function recebimentos()
    {
        return $this->belongsToMany(
            ProcessoAndamento::class,
            "protocolo.proctransand",
            "p64_codtran",
            "p64_codandam"
        );
    }


    /**
     * @return ProcessoAndamento|null
     */
    public function recebimento()
    {
        return $this->recebimentos()->first();
    }
}
