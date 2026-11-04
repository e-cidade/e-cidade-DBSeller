<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use App\Domain\RecursosHumanos\Pessoal\Model\RegimePrevidencia;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApropiacaoDecimoFeriasLancamentos
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $id
 * @property integer $c146_apropriacaodecimoferias_id
 * @property integer $c146_regimeprevidencia
 * @property integer $c146_conlancam
 * @property boolean $c146_estornar identificador para saber quais lançamentos estornar
 * @property ApropriacaoDecimoFerias $apropriacao
 * @property Lancamento $lancamento
 * @property RegimePrevidencia $regimePrevidencia
 */
class ApropriacaoDecimoFeriasLancamentos extends Model
{
    protected $table = 'contabilidade.apropriacaodecimoferias_lancamentos';

    protected $dates = [
        'pl2_created_at',
        'pl2_updated_at',
    ];

    public function apropriacao()
    {
        return $this->belongsTo(ApropriacaoDecimoFerias::class, 'c146_apropriacaodecimoferias_id', 'id');
    }

    public function lancamento()
    {
        return $this->belongsTo(Lancamento::class, 'c146_conlancam', 'c70_codlan');
    }

    public function regimePrevidencia()
    {
        return $this->belongsTo(RegimePrevidencia::class, 'c146_regimeprevidencia', 'rh127_sequencial');
    }
}
