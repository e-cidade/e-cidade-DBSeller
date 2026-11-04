<?php

namespace App\Domain\Patrimonial\PNCP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pn03_codigo
 * @property $pn03_liclicita
 * @property $pn03_solicita
 * @property $pn03_numero
 * @property $pn03_ano
 * @property $pn03_instituicao
 * @property $pn03_usuario
 * @property $pn03_unidade
 * @property $pn03_datapublicacao
 * @property $pn03_cnpj
 */
class ComprasPncp extends Model
{
    protected $primaryKey = 'pn03_codigo';
    protected $table = 'compraspncp';
    public $timestamps = false;
    protected $fillable = [
        'pn03_codigo',
        'pn03_liclicita',
        'pn03_solicita',
        'pn03_numero',
        'pn03_ano',
        'pn03_instituicao',
        'pn03_usuario',
        'pn03_unidade',
        'pn03_datapublicacao',
        'pn03_cnpj',
    ];
}
