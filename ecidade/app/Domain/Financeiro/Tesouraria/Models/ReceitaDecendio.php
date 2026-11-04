<?php

namespace App\Domain\Financeiro\Tesouraria\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @class ReceitaDecendio
 * @namespace App\Domain\Financeiro\Tesouraria\Models
 * @property int $k224_codigo
 * @property int $k224_tipodesdobramento
 * @property int $k224_reduz
 * @property int $k224_anousu
 */
class ReceitaDecendio extends Model
{
    protected $table = 'caixa.receitadescendio';
    protected $primaryKey = 'k222_codigo';

    public $timestamps = false;

    protected $fillable = [
        'k224_tipodesdobramento',
        'k224_reduz',
        'k224_anousu'
    ];
}
