<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class RegenciaPeriodo extends Model
{
    protected $table = 'escola.regenciaperiodo';
    protected $primaryKey = 'ed78_i_codigo';
    public $timestamps = false;

    protected $fillable = [
        'ed78_i_regencia',
        'ed78_i_procavaliacao',
        'ed78_i_aulasdadas'
    ];

    public function regencia()
    {
        return $this->belongsTo(Regencia::class, 'ed78_i_regencia', 'ed59_i_codigo');
    }

    public function procedimentoAvaliacao()
    {
        return $this->belongsTo(ProcedimentoAvaliacao::class, 'ed78_i_procavaliacao', 'ed41_i_codigo');
    }
}
