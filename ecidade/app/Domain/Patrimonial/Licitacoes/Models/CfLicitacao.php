<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $l03_codigo
 * @property $l03_descr
 * @property $l03_tipo
 * @property $l03_codcom
 * @property $l03_usaregistropreco
 * @property $l03_pctipocompratribunal
 * @property $l03_instit
 */
class CfLicitacao extends Model
{
    public $timestamps = false;
    public $fillable = [
        'l03_descr',
        'l03_tipo',
        'l03_codcom',
        'l03_usaregistropreco',
        'l03_pctipocompratribunal',
        'l03_instit',
    ];
    protected $table = 'licitacao.cflicita';
    protected $primaryKey = 'l03_codigo';
}
