<?php

namespace App\Domain\Educacao\Escola\Models\AvaliacoesParciais;

use App\Domain\Educacao\Escola\Models\ProcedimentoAvaliacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $ed340_codigo
 * @property integer $ed340_regencia
 * @property integer $ed340_procavaliacao
 * @property integer $ed340_ordem
 * @property AproveitamentoAvaliacaoParcial[] $aproveitamentos
 * @property ProcedimentoAvaliacao[] $procedimentoAvaliacao
 */
class AvaliacaoParcial extends Model
{
    protected $table = 'escola.procavaliacaoparcial';
    protected $primaryKey = 'ed340_codigo';
    protected $fillable = ['ed340_regencia', 'ed340_procavaliacao', 'ed340_ordem'];
    protected $appends = ['field'];
    protected $hidden = ['aproveitamentos'];
    public $timestamps = false;

    public function getFieldAttribute()
    {
        return "avaliacao_{$this->attributes['ed340_codigo']}";
    }

    public function aproveitamentos()
    {
        return $this->hasMany(AproveitamentoAvaliacaoParcial::class, 'ed341_procavaliacaoparcial', 'ed340_codigo');
    }

    public function procedimentoAvaliacao()
    {
        return $this->belongsTo(ProcedimentoAvaliacao::class, 'ed340_procavaliacao', 'ed41_i_codigo');
    }

    public function scopeRegencia(Builder $query, \Regencia $regencia)
    {
        return $query->where('ed340_regencia', $regencia->getCodigo());
    }

    public function scopeAvaliacaoPeriodica(Builder $query, \AvaliacaoPeriodica $avaliacaoPeriodica)
    {
        return $query->where('ed340_procavaliacao', $avaliacaoPeriodica->getCodigo());
    }
}
