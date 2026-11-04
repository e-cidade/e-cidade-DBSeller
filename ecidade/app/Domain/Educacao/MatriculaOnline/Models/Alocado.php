<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class Alocado extends Model
{
    protected $table = 'plugins.alocados';
    public $timestamps = false;
    protected $primaryKey = 'mo13_codigo';
    protected $dates = ['mo13_data'];

    public function situacao()
    {
        return $this->belongsTo(
            AlocadoSituacao::class,
            'mo13_codigo',
            'mo28_alocado'
        );
    }

    public function opcaoEscolaTurno()
    {
        return $this->belongsTo(OpcaoEscolaTurno::class, 'mo13_baseescturno', 'mo03_codigo');
    }
}
