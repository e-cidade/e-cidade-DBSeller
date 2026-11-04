<?php

namespace App\Domain\Saude\ESF\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Domain\Saude\ESF\Models
 * @property integer $sd30_i_prontuario
 * @property string $sd30_v_local_atendimento
 * @property string $sd30_v_turno
 * @property \DateTime $sd30_d_data_atendimento
 * @property integer $sd30_i_profissional
 * @property integer $sd30_i_rhcbo
 * @property string $sd30_i_ciap
 *
 * @property Ciap $ciap
 */
class ProntuarioEsf extends Model
{
    public $timestamps = false;
    protected $table = 'plugins.psf_prontuario';
    protected $primaryKey = 'sd30_i_prontuario';

    public $casts = [
        'sd30_d_data_atendimento' => 'datetime'
    ];

    public function ciap()
    {
        return $this->belongsTo(Ciap::class, 'sd30_i_ciap', 'codigo');
    }
}
