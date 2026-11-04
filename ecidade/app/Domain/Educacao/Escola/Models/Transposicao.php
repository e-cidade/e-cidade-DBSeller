<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VagasTransposicao
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed353_codigo
 * @property integer $ed353_ano
 * @property boolean $ed353_ativa
 * @property boolean $ed353_processada
 */
class Transposicao extends Model
{
    protected $table = "escola.transposicao";
    protected $primaryKey = "ed353_codigo";
    public $timestamps = false;
    protected $appends = [
        "status",
        "color",
        "labelBotao",
    ];

    public static function getTransposicaoAtual()
    {
        return Transposicao::query()->orderByDesc('ed353_ano')->first();
    }

    public function scopeAno($query, $ano)
    {
        return $query->where('ed353_ano', $ano);
    }

    public function getStatusAttribute()
    {
        return $this->getAttribute('ed353_ativa') ? 'Abertas' : 'Encerradas';
    }

    public function getColorAttribute()
    {
        return $this->getAttribute('ed353_ativa') ? 'alert-success' : 'alert-danger';
    }

    public function getLabelBotaoAttribute()
    {
        return $this->getAttribute('ed353_ativa') ? 'Encerrar inscrições' : 'Reabrir inscrições';
    }

    public function designados()
    {
        return $this->hasMany(DesignadoTransposicao::class, 'ed354_transposicao', 'ed353_codigo');
    }
}
