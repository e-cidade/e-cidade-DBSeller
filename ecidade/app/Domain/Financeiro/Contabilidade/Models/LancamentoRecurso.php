<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $o201_sequencial
 * @property integer $o201_codlan
 * @property integer $o201_complemento
 * @property integer $o201_orctiporec
 * @property Lancamento lancamento
 */
class LancamentoRecurso extends Model
{
    protected $table = 'contabilidade.conlancamcomplementorecurso';
    protected $primaryKey = 'o201_sequencial';
    public $timestamps = false;

    /**
     * @return BelongsTo|Lancamento
     */
    public function lancamento()
    {
        return $this->belongsTo(Lancamento::class, 'o201_codlan', 'c70_codlan');
    }
}
