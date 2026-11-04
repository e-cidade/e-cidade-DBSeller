<?php

namespace App\Domain\Educacao\Escola\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExcecaoBloqueioNota
 * @package App\Domain\Educacao\Escola\Models
 * @property int $ed363_codigo
 * @property int $ed363_turma
 * @property int $ed363_regencia
 * @property int $ed363_periodoavaliacao
 * @property Carbon $ed363_datalimite
 * @property bool $ed363_ativo
 * @property int $ed363_usuario
 * @property Turma $turma
 * @property Regencia $regencia
 * @property PeriodoAvaliacao $periodoAvaliacao
 */
class ExcecaoBloqueioNota extends Model
{
    protected $table = 'escola.excecoesbloqueionota';
    protected $primaryKey = 'ed363_codigo';
    protected $dates = ['ed363_datalimite'];

    public function turma()
    {
        return $this->belongsTo(Turma::class, 'ed363_turma', 'ed57_i_codigo');
    }

    public function regencia()
    {
        return $this->belongsTo(Regencia::class, 'ed363_regencia', 'ed59_i_codigo');
    }

    public function periodoAvaliacao()
    {
        return $this->belongsTo(PeriodoAvaliacao::class, 'ed363_periodoavaliacao', 'ed09_i_codigo');
    }
}
