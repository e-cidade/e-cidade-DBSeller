<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class AtestadoNecessidadeEspecial extends Model
{
    protected $table = 'plugins.atestadonecessidadeespecial';
    public $timestamps = false;
    protected $primaryKey = 'mo31_sequencial';

    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'mo31_base', 'mo01_codigo');
    }
}
