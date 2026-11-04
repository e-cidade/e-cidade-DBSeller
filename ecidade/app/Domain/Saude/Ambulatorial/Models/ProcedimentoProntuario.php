<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property integer $sd29_i_codigo
 * @property integer $sd29_i_prontuario
 * @property integer $sd29_i_procedimento
 * @property \DateTime $sd29_d_data
 * @property string $sd29_c_hora
 * @property string $sd29_t_tratamento
 * @property integer $sd29_i_usuario
 * @property \DateTime $sd29_d_cadastro
 * @property string $sd29_c_cadastro
 * @property integer $sd29_i_profissional
 * @property string $sd29_t_diagnostico
 * @property boolean $sd29_sigilosa
 * @property string $sd29_t_achado
 *
 * @property ProcedimentoProntuarioCid $procedimentoCid
 */
class ProcedimentoProntuario extends Model
{
    public $timestamps = false;
    protected $table = 'ambulatorial.prontproced';
    protected $primaryKey = 'sd29_i_codigo';

    public $casts = [
        'sd29_d_data' => 'datetime',
        'sd29_d_cadastro' => 'datetime'
    ];

    public function procedimentoCid()
    {
        return $this->hasOne(ProcedimentoProntuarioCid::class, 's135_i_prontproced', 'sd29_i_codigo');
    }
}
