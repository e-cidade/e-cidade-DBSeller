<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lote
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $c160_codigo
 * @property integer $c160_exercicio
 * @property integer $c160_instituicao
 * @property string $c160_lote
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Lote extends Model
{

    protected $table = 'contabilidade.lotelancamentos';
    protected $primaryKey = 'c160_codigo';

    public function lancamentos()
    {
        return $this->belongsToMany(
            Lancamento::class,
            'lotelancamentoconlancam',
            'c161_lotelancamento',
            'c161_conlancam'
        );
    }
}
