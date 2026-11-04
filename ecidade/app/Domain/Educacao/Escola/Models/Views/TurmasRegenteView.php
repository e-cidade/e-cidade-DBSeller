<?php

namespace App\Domain\Educacao\Escola\Models\Views;

use App\Domain\Educacao\Escola\Models\Calendario;
use App\Domain\Educacao\Escola\Models\Etapa;
use App\Domain\Educacao\Escola\Models\Regencia;
use App\Domain\Educacao\Escola\Models\Turma;
use Illuminate\Database\Eloquent\Model;

class TurmasRegenteView extends Model
{
    protected $table = 'escola.turmas_regentes';

    public function turma()
    {
        return $this->belongsTo(Turma::class, 'codigo_turma', 'ed57_i_codigo');
    }

    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'codigo_etapa', 'ed11_i_codigo');
    }

    public function regencia()
    {
        return $this->belongsTo(Regencia::class, 'codigo_regencia', 'ed59_i_codigo');
    }

    public function calendarioTurma()
    {
        return $this->belongsTo(Calendario::class, 'calendario', 'ed52_i_codigo');
    }
}
