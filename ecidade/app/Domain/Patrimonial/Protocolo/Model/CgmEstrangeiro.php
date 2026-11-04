<?php

namespace App\Domain\Patrimonial\Protocolo\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $z09_sequencial
 * @property $z09_numcgm
 * @property $z09_documento
 * @property $z09_pais
 * @property $z09_cidade
 */
class CgmEstrangeiro extends Model
{
    public $timestamps = false;
    protected $table = 'protocolo.cgmestrangeiro';
    protected $primaryKey = 'z09_sequencial';
    protected $fillable = [
        'z09_numcgm',
        'z09_documento',
        'z09_pais',
        'z09_cidade'
    ];
}
