<?php

namespace App\Domain\Saude\ESF\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Domain\Saude\ESF\Models
 * @property integer psf_id
 * @property integer psf_cod_estabelecimento
 * @property string psf_estabelecimento
 * @property string psf_cnes
 * @property string psf_ine
 * @property string psf_nome_equipe
 * @property boolean psf_ativo
 * @property \DateTime psf_data_ativacao
 * @property \DateTime psf_data_desativacao
 * @property string psf_area
 * @property integer tipoequipe
 */
class Equipe extends Model
{
    public $timestamps = false;
    protected $table = 'plugins.psf_equipe';
    protected $primaryKey = 'psf_id';

    public $casts = [
        'psf_data_ativacao' => 'datetime',
        'psf_data_desativacao' => 'datetime'
    ];

    public function scopeProfissional(Builder $query, $cgmProfissional)
    {
        $query->join('plugins.psf_equipe_profissionais', 'psf2_cod_equipe', 'psf_id');
        return $query->where('psf2_cod_profissional', $cgmProfissional);
    }

    public function scopeUnidade(Builder $query, $idUnidade)
    {
        return $query->where('psf_cod_estabelecimento', $idUnidade);
    }

    public function scopeProfissionalAtivo(Builder $query)
    {
        return $query->where('psf2_ativo', true);
    }
}
