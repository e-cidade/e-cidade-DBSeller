<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VagasTransposicao
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed350_codigo
 * @property integer $ed350_ano
 * @property integer $ed350_escola
 * @property integer $ed350_etapa
 * @property integer $ed350_vagas
 * @property integer $ed350_saldo
 * @property integer $ed350_vagaspcd
 * @property integer $ed350_saldopcd
 * @property Escola $escola
 * @property Etapa $etapa
 */
class VagasTransposicao extends Model
{
    protected $table = "escola.vagastransposicao";
    protected $primaryKey = 'ed350_codigo';
    public $timestamps = false;

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'ed350_escola', 'ed18_i_codigo');
    }
    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'ed350_etapa', 'ed11_i_codigo');
    }

    public function scopeAno($query, $ano)
    {
        return $query->where('ed350_ano', $ano);
    }

    public function scopeEscola($query, $codigoEscola)
    {
        return $query->where('ed350_escola', $codigoEscola);
    }

    public function scopeEtapa($query, $codigoEtapa)
    {
        return $query->where('ed350_etapa', $codigoEtapa);
    }
}
