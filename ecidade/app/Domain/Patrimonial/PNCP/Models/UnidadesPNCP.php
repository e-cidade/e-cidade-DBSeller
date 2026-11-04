<?php

namespace App\Domain\Patrimonial\PNCP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pn02_codigo
 * @property $pn02_unidade
 * @property $pn02_nome
 * @property $pn02_ativo
 * @property $pn02_instit
 * @property $pn02_data
 */
class UnidadesPNCP extends Model
{
    protected $primaryKey = 'pn02_codigo';
    protected $table = 'unidadespncp';
    protected $fillable = [
        'pn02_unidade',
        'pn02_nome',
        'pn02_ativo',
        'pn02_instit',
        'pn02_data'
    ];
    public $timestamps = false;
}
