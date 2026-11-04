<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property integer $sd04_i_codigo
 * @property integer $sd04_i_unidade
 * @property integer $sd04_i_medico
 * @property integer $sd04_i_cbo
 * @property integer $sd04_i_vinculo
 * @property integer $sd04_i_tipovinc
 * @property integer $sd04_i_subtipovinc
 * @property string $sd04_i_horaamb
 * @property string $sd04_i_horahosp
 * @property string $sd04_i_horaoutros
 * @property integer $sd04_i_orgaoemissor
 * @property string $sd04_c_situacao
 * @property string $sd04_v_registroconselho
 * @property string $sd04_c_sus
 * @property integer $sd04_i_numerodias
 * @property \DateTime $sd04_d_folgaini
 * @property \DateTime $sd04_d_folgafim
 *
 * @property Profissional $profissional
 */
class ProfissionalUnidade extends Model
{
    public $timestamps = false;
    protected $table = 'ambulatorial.unidademedicos';
    protected $primaryKey = 'sd04_i_codigo';

    public $casts = [
        'sd04_d_folgaini' => 'datetime',
        'sd04_d_folgafim' => 'datetime'
    ];

    public function profissional()
    {
        return $this->belongsTo(Profissional::class, 'sd04_i_medico', 'sd03_i_codigo');
    }
}
