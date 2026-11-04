<?php

namespace App\Domain\Financeiro\Empenho\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Retencao Natureza de Rendimento
 *
 * @property int $e168_sequencial
 * @property int $e168_retencaoreceitas
 * @property int $e168_naturezarendimento
 */
class RetencaoNaturezaRendimento extends Model
{
    protected $table = 'empenho.retencaonaturezarendimento';
    protected $primaryKey = 'e168_sequencial';
    public $timestamps = false;

    public function naturezaRendimento()
    {
        return $this->belongsTo(
            NaturezaRendimento::class,
            'e167_sequencial',
            'e168_naturezarendimento'
        );
    }
}
