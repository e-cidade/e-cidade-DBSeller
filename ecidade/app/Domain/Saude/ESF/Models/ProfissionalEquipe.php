<?php

namespace App\Domain\Saude\ESF\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Domain\Saude\ESF\Models
 * @property integer $psf2_id
 * @property integer $psf2_cod_equipe
 * @property integer $psf2_cod_profissional
 * @property integer $psf2_microarea
 * @property boolean $psf2_ativo
 * @property \DateTime $psf2_data_ativacao
 * @property \DateTime $psf2_data_desativacao
 */
class ProfissionalEquipe extends Model
{
    public $timestamps = false;
    protected $table = 'plugins.psf_equipe_profissionais';
    protected $primaryKey = 'psf2_id';

    public $casts = [
        'psf2_data_ativacao' => 'datetime',
        'psf2_data_desativacao' => 'datetime'
    ];
}
