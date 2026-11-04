<?php

namespace App\Domain\Patrimonial\Contratos\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Patrimonial\Contratos\Models\MensageriaAcordoDia;

/**
 * @property string $ac51_assunto
 * @property string $ac51_mensagem
 * @property int $instituicao_id
 * @property int $ac51_sequencial
 */
class MensageriaAcordo extends Model
{
    protected $table = "mensageriaacordo";
    protected $primaryKey = "ac51_sequencial";

    protected $fillable = [
        "ac51_assunto",
        "ac51_mensagem",
        "ac51_sequencial",
        "instituicao_id"
    ];

    /**
     * @return string
     */
    public function getAssunto()
    {
        return $this->ac51_assunto;
    }

    /**
     * @param string $ac51_assunto
     */
    public function setAssunto($ac51_assunto)
    {
        $this->ac51_assunto = $ac51_assunto;
    }

    /**
     * @return string
     */
    public function getMensagem()
    {
        return $this->ac51_mensagem;
    }

    /**
     * @param string $ac51_mensagem
     */
    public function setMensagem($ac51_mensagem)
    {
        $this->ac51_mensagem = $ac51_mensagem;
    }

    /**
     * @return int
     */
    public function getSequencial()
    {
        return $this->ac51_sequencial;
    }

    /**
     * @param int $ac51_sequencial
     */
    public function setSequencial($ac51_sequencial)
    {
        $this->ac51_sequencial = $ac51_sequencial;
    }

    /**
     * @return mixed
     */
    public function getInstituicaoId()
    {
        return $this->instituicao_id;
    }

    /**
     * @param int $instituicao_id
     */
    public function setInstituicaoId($instituicao_id)
    {
        $this->instituicao_id = $instituicao_id;
    }

    public function dias()
    {
        return $this->hasMany(MensageriaAcordoDia::class, 'mensageriaacordo_id', 'ac51_sequencial');
    }
}
