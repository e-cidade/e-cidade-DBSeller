<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TurnoReferente
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed336_codigo
 * @property integer $ed336_turma
 * @property integer $ed336_turnoreferente
 * @property integer $ed336_vagas
 */
class TurmaTurnoReferente extends Model
{
    protected $table = 'escola.turmaturnoreferente';
    protected $primaryKey = 'ed336_codigo';
    public $timestamps = false;
    public $incrementing = false;
    protected $appends = ['matriculas_ativas'];

    public function matriculas()
    {
        return $this->hasMany(MatriculaTurnoReferente::class, 'ed337_turmaturnoreferente', 'ed336_codigo');
    }

    public function getMatriculasAtivasAttribute()
    {
        return $this->matriculas->filter(
            function ($mat) {
                $matricula = Matricula::find($mat->ed337_matricula);
                return $matricula->ed60_c_situacao === 'MATRICULADO';
            }
        )->count();
    }

    public function turnoReferente()
    {
        return $this
            ->belongsTo(TurnoReferente::class, 'ed336_turnoreferente', 'ed231_i_referencia');
    }

    public function turma()
    {
        return $this
            ->belongsTo(Turma::class, 'ed336_turma', 'ed57_i_codigo');
    }
}
