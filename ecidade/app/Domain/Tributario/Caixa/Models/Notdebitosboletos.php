<?php

namespace App\Domain\Tributario\Caixa\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int k218_numpre
 * @property int k218_notifica
 * @property string k218_dtemissao
 * @property string k218_dtvenc
 * @property bool k218_processa_desconto
 */
class Notdebitosboletos extends Model
{
    protected $table = 'caixa.notdebitosboletos';

    public $fillable = [
        'k218_numpre',
        'k218_notifica',
        'k218_dtvenc',
        'k218_dtemissao',
        'k218_processa_desconto'
    ];
}
