<?php

namespace App\Domain\Financeiro\Tesouraria\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @class Decendio
 * @namespace App\Domain\Financeiro\Tesouraria\Models
 *
 * @property int $k230_codigo
 * @property int $k230_slip
 */
class Decendio extends Model
{
    protected $table = 'caixa.decendio';
    protected $primaryKey = 'k230_codigo';

    public function lancamentos()
    {
        return $this->hasMany(DecendioLancamento::class, 'k231_decendio', 'k230_codigo');
    }

    public function slip()
    {
        return $this->hasOne(Slip::class, 'k17_codigo', 'k230_slip');
    }
}
