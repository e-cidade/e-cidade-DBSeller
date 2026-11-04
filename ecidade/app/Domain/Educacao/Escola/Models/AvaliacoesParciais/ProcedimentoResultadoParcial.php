<?php

namespace App\Domain\Educacao\Escola\Models\AvaliacoesParciais;

use App\Domain\Educacao\Escola\Models\ProcedimentoAvaliacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $ed342_codigo
 * @property integer $ed342_regencia
 * @property integer $ed342_procavaliacao
 * @property integer $ed342_formaobtencao
 * @property integer $ed342_formaobtencaofinal
 * @property ProcedimentoAvaliacao $procedimentoAvaliacao
 */
class ProcedimentoResultadoParcial extends Model
{
    protected $table = 'escola.procresultadoparcial';
    protected $primaryKey = 'ed342_codigo';
    protected $fillable = ['ed342_regencia', 'ed342_procavaliacao', 'ed342_formaobtencao', 'ed342_formaobtencaofinal'];
    public $timestamps = false;

    public function resultados()
    {
        return $this->hasMany(ResultadoParcial::class, 'ed343_procresultadoparcial', 'ed342_codigo');
    }

    public function procedimentoAvaliacao()
    {
        return $this->belongsTo(ProcedimentoAvaliacao::class, 'ed342_procavaliacao', 'ed41_i_codigo');
    }

    public function scopeRegencia(Builder $query, \Regencia $regencia)
    {
        return $query->where('ed342_regencia', $regencia->getCodigo());
    }

    public function scopeAvaliacaoPeriodica(Builder $query, \AvaliacaoPeriodica $avaliacaoPeriodica)
    {
        return $query->where('ed342_procavaliacao', $avaliacaoPeriodica->getCodigo());
    }
}
