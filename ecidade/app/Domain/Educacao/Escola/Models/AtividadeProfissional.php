<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class AtividadeProfissional extends Model
{
    protected $table = 'escola.atividaderh';
    protected $primaryKey = 'ed01_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    public function atividadesProfissionaisEscola()
    {
        return $this->hasMany(AtividadeProfissionalEscola::class, 'ed22_i_atividade', 'ed01_i_codigo');
    }
}
