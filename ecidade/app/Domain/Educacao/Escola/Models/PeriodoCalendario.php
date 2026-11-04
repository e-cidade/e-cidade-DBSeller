<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoCalendario extends Model
{
    protected $table = 'escola.periodocalendario';
    protected $primaryKey = 'ed53_i_codigo';

    public function periodoAvaliacao()
    {
        return $this->belongsTo(PeriodoAvaliacao::class, 'ed53_i_periodoavaliacao', 'ed09_i_codigo');
    }
}
