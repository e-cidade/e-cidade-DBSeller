<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LancamentoComplemento
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $c72_codlan
 * @property string $c72_complem
 * @property Lancamento $lancamento
 */
class LancamentoComplemento extends Model
{
    protected $table = 'contabilidade.conlancamcompl';
    protected $primaryKey = 'c72_codlan';
    public $timestamps = false;

    public function lancamento()
    {
        return $this->belongsTo(Lancamento::class, 'c72_codlan', 'c70_codlan');
    }
}
