<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Turno
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed15_i_codigo
 * @property integer $ed15_i_sequencia
 * @property string $ed15_c_nome
 */
class Turno extends Model
{
    protected $table = 'escola.turno';
    protected $primaryKey = 'ed15_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    public function getEd15CNomeAttribute()
    {
        return trim($this->attributes['ed15_c_nome']);
    }
    /**
     * @return integer
     */
    public function getCodigo()
    {
        return $this->ed15_i_codigo;
    }

    /**
     * @param integer $ed15_i_codigo
     * @return Turno
     */
    public function setCodigo($ed15_i_codigo)
    {
        $this->ed15_i_codigo = $ed15_i_codigo;
        return $this;
    }

    /**
     * @return integer
     */
    public function getOrdem()
    {
        return $this->ed15_i_sequencia;
    }

    /**
     * @param integer $ed15_i_sequencia
     * @return $this
     */
    public function setOrdem($ed15_i_sequencia)
    {
        $this->ed15_i_sequencia = $ed15_i_sequencia;
        return $this;
    }
    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->ed15_c_nome;
    }

    /**
     * @param string $ed15_c_nome
     * @return string
     */
    public function setDescricao($ed15_c_nome)
    {
        $this->ed15_c_nome = $ed15_c_nome;
        return $ed15_c_nome;
    }

    public function turnosReferentes()
    {
        return $this->hasMany(TurnoReferente::class, 'ed231_i_turno', 'ed15_i_codigo');
    }
}
