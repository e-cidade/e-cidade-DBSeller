<?php

namespace App\Domain\Educacao\Escola\Models\AvaliacoesParciais;

use App\Domain\Educacao\Escola\Models\Diario;
use ArredondamentoNota;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $ed341_codigo
 * @property integer $ed341_diario
 * @property integer $ed341_procavaliacaoparcial
 * @property float $ed341_valornota
 * @property string $ed341_valornivel
 * @property Diario $diario
 * @property AvaliacaoParcial $procedimentoAvaliacaoParcial
 */
class AproveitamentoAvaliacaoParcial extends Model
{
    protected $table = 'escola.avaliacaoparcial';
    protected $primaryKey = 'ed341_codigo';
    protected $fillable = ['ed341_diario', 'ed341_procavaliacaoparcial', 'ed341_valornota', 'ed341_valornivel'];
    protected $hidden = ['diario'];
    public $timestamps = false;

    public function getEd341ValornotaAttribute()
    {
        $ano = $this->diario->calendario->getAno();
        return ArredondamentoNota::formatar($this->attributes['ed341_valornota'], $ano);
    }

    public function diario()
    {
        return $this->belongsTo(Diario::class, 'ed341_diario', 'ed95_i_codigo');
    }

    public function procedimentoAvaliacaoParcial()
    {
        return $this->belongsTo(AvaliacaoParcial::class, 'ed341_procavaliacaoparcial', 'ed340_codigo');
    }
}
