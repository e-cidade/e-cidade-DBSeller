<?php

namespace App\Domain\Educacao\Escola\Models\AvaliacoesParciais;

use App\Domain\Educacao\Escola\Contracts\AvaliacoesParciais\ResultadoParcialInterface;
use App\Domain\Educacao\Escola\Models\Diario;
use ArredondamentoNota;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $ed343_codigo
 * @property integer $ed343_diario
 * @property integer $ed343_procresultadoparcial
 * @property float $ed343_valornota
 * @property string $ed343_valornivel
 * @property string $ed343_observacao
 * @property float $ed343_recuperacaonota
 * @property string $ed343_recuperacaonivel
 * @property boolean $ed343_encerrado
 * @property Diario $diario
 * @property ProcedimentoResultadoParcial $procedimentoResultadoParcial
 */
class ResultadoParcial extends Model implements ResultadoParcialInterface
{
    protected $table = 'escola.resultadoparcial';
    protected $primaryKey = 'ed343_codigo';
    protected $fillable = [
        'ed343_diario',
        'ed343_procresultadoparcial',
        'ed343_valornota',
        'ed343_valornivel',
        'ed343_observacao',
        'ed343_recuperacaonota',
        'ed343_recuperacaonivel'
    ];
    
    public $timestamps = false;

    public function getEd343ValornotaAttribute()
    {
        $ano = $this->diario->calendario->getAno();
        return ArredondamentoNota::formatar($this->attributes['ed343_valornota'], $ano);
    }

    public function getEd343RecuperacaonotaAttribute()
    {
        $ano = $this->diario->calendario->getAno();
        return ArredondamentoNota::formatar($this->attributes['ed343_recuperacaonota'], $ano);
    }

    public function diario()
    {
        return $this->belongsTo(Diario::class, 'ed343_diario', 'ed95_i_codigo');
    }

    public function procedimentoResultadoParcial()
    {
        return $this->belongsTo(ProcedimentoResultadoParcial::class, 'ed343_procresultadoparcial', 'ed342_codigo');
    }
}
