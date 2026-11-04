<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    protected $table = 'escola.historico';
    protected $primaryKey = 'ed61_i_codigo';

    public function curso()
    {
        return $this->belongsTo(CursoEdu::class, 'ed61_i_curso', 'ed29_i_codigo');
    }
}
