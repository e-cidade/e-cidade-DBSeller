<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CriterioOrdenacaoTransposicao
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed352_ano
 * @property integer $ed352_etapa
 * @property integer $ed352_criterio
 * @property integer $ed352_peso
 * @property integer $ed352_ordem
 * @property Etapa $etapa
 * @property CriterioTransposicao $criterio
 */
class CriterioOrdenacaoTransposicao extends Model
{
    protected $table = "escola.criteriostransposicaoordenacao";
    protected $primaryKey = 'ed352_codigo';
    public $timestamps = false;

    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'ed352_etapa', 'ed11_i_codigo');
    }
    public function criterio()
    {
        return $this->belongsTo(CriterioTransposicao::class, 'ed352_criterio', 'ed351_codigo');
    }

    public function scopeAno($query, $ano)
    {
        return $query->where('ed352_ano', $ano);
    }
}
