<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use App\Domain\Educacao\CentralMatriculas\Models\Escola;
use Illuminate\Database\Eloquent\Model;

class OpcaoEscola extends Model
{
    protected $table = 'plugins.baseescola';
    public $timestamps = false;
    protected $primaryKey = 'mo02_codigo';
    public function opcaoTurno()
    {
        return $this->belongsTo(OpcaoEscolaTurno::class, 'mo02_codigo', 'mo03_baseescola');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'mo02_escola', 'mo53_codigo');
    }
}
