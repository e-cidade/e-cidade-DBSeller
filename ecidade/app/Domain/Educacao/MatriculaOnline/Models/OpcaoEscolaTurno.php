<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use App\Domain\Educacao\Escola\Models\Turno;
use Illuminate\Database\Eloquent\Model;

class OpcaoEscolaTurno extends Model
{
    protected $table = 'plugins.baseescturno';
    public $timestamps = false;
    protected $primaryKey = 'mo03_codigo';

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'mo03_turno', 'ed15_i_codigo');
    }

    public function opcaoEscola()
    {
        return $this->belongsTo(OpcaoEscola::class, 'mo03_baseescola', 'mo02_codigo');
    }
}
