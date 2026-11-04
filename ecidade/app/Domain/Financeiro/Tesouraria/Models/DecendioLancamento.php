<?php

namespace App\Domain\Financeiro\Tesouraria\Models;

use App\Domain\Financeiro\Contabilidade\Models\Lancamento;
use Illuminate\Database\Eloquent\Model;

/**
 * @class DecendioLancamento
 * @namespace App\Domain\Financeiro\Tesouraria\Models
 *
 * @property int $k231_codigo;
 * @property int $k231_decendio;
 * @property int $k231_lancamento;
 * @property int $k231_desdobramento;
 */
class DecendioLancamento extends Model
{
    protected $table = 'caixa.decendiolancamento';
    protected $primaryKey = 'k231_codigo';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function decendio()
    {
        return $this->hasOne(Decendio::class, 'k230_codigo', 'k231_decendio');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tipoDesdobramento()
    {
        return $this->hasOne(TipoDesdobramento::class, 'k222_codigo', 'k231_desdobramento');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lancamento()
    {
        return $this->hasOne(Lancamento::class, 'c70_codlan', 'k231_lancamento');
    }
}
