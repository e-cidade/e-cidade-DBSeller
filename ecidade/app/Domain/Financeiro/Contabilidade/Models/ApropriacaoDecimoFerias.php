<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ApropriacaoDecimoFerias
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $id
 * @property integer $c145_instituicao
 * @property integer $c145_exercicio
 * @property integer $c145_mes
 * @property boolean $c145_processado
 * @property ApropriacaoDecimoFeriasLancamentos[]|Collection $lancamentos
 */
class ApropriacaoDecimoFerias extends Model
{
    protected $table = 'contabilidade.apropriacaodecimoferias';

    protected $fillable = [
        "c145_instituicao",
        "c145_exercicio",
        "c145_mes",
        "c145_processado",
    ];

    protected $guarded = ['id'];

    protected $dates = [
        'pl2_created_at',
        'pl2_updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lancamentos()
    {
        return $this->hasMany(ApropriacaoDecimoFeriasLancamentos::class, 'c146_apropriacaodecimoferias_id', 'id');
    }
}
