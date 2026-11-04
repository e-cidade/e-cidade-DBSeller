<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Matricula
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed60_i_codigo
 * @property integer $ed60_i_aluno
 * @property integer $ed60_i_turma
 * @property integer $ed60_i_numaluno
 * @property string $ed60_c_situacao
 * @property string $ed60_c_concluida
 * @property integer $ed60_i_turmaant
 * @property string $ed60_c_rfanterior
 * @property date $ed60_d_datamatricula
 * @property date $ed60_d_datamodif
 * @property string $ed60_t_obs
 * @property string $ed60_c_ativa
 * @property string $ed60_c_tipo
 * @property string $ed60_c_parecer
 * @property date $ed60_d_datasaida
 * @property date $ed60_d_datamodifant
 * @property integer $ed60_matricula
 * @property integer $ed60_tipoingresso
 */
class Matricula extends Model
{
    protected $table = 'escola.matricula';
    protected $primaryKey = 'ed60_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    protected $appends = ['codigo'];
    protected $hidden = ['ed60_i_codigo'];

    protected $dates = [
        'ed60_d_datamatricula',
        'ed60_d_datasaida'
    ];
    /**
     * @var array
     */
    private $storage = [];

    public function getCodigoAttribute()
    {
        return $this->attributes['ed60_i_codigo'];
    }

    public function getEd60CSituacaoAttribute()
    {
        return trim($this->attributes['ed60_c_situacao']);
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'ed60_i_aluno', 'ed47_i_codigo');
    }

    /**
     * @return Etapa
     */
    public function getEtapaMatricula()
    {
        if (!array_key_exists('etapa', $this->storage)) {
            $this->storage['etapa'] = $this->etapa;
        }

        return $this->storage['etapa'];
    }

    public function etapa()
    {
        return $this->belongsToMany(Etapa::class, 'matriculaserie', 'ed221_i_matricula', 'ed221_i_serie')
            ->where('ed221_c_origem', '=', 'S');
    }

    public function scopeApenasAtivas($query)
    {
        return $query->where('ed60_c_ativa', 'S');
    }


    public function scopeApneasTransferidosFora($query)
    {
        return $query->where('ed60_c_situacao', 'TRANSFERIDO FORA');
    }
    public function turma()
    {
        return $this->belongsTo(Turma::class, 'ed60_i_turma', 'ed57_i_codigo');
    }
}
