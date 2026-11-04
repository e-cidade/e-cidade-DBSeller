<?php

namespace App\Domain\Tributario\Caixa\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int k00_numpre
 * @property int k00_numpar
 * @property string k00_codbar
 * @property int|null k00_criacaosolicitacao
 * @property string|string k00_estadosolicitacao
 * @property string k00_conciliacaosolicitante
 * @property int|null k00_numeroversaosolicitacaopagamento
 * @property string k00_qrcode
 * @property string k00_linkqrcode
 * @property string created_at
 * @property string|null updated_at
 */
class Recibobarpix extends Model
{
    protected $table = 'caixa.recibobarpix';
    protected $primaryKey = 'k00_numpre';

    public $fillable = [
        'k00_numpre',
        'k00_numpar',
        'k00_codbar',
        'k00_criacaosolicitacao',
        'k00_estadosolicitacao',
        'k00_conciliacaosolicitante',
        'k00_numeroversaosolicitacaopagamento',
        'k00_linkqrcode',
        'k00_qrcode',
        'created_at',
        'updated_at'
    ];
}
