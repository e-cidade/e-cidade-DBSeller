<?php

namespace App\Domain\Saude\Farmacia\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static byMovimentacao(integer $movimentacao)
 */
class TipoMovimentacaoBnafar extends Model
{
    public $timestamps = false;
    protected $table = 'farmacia.tipomovimentacaobnafar';
    protected $primaryKey = 'fa68_codigo';

    public function scopeByMovimentacao(Builder $query, $movimentacao)
    {
        $tipo = [
            1 => 'S',
            2 => 'E',
            3 => 'E'
        ];

        $query->where('fa68_tipo', '!=', $tipo[$movimentacao]);
    }
}
