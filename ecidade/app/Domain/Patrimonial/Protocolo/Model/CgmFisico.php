<?php

namespace App\Domain\Patrimonial\Protocolo\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $z04_sequencial
 * @property $z04_numcgm
 * @property $z04_rhcbo
 * @property $z04_nomesocial
 * @property $z04_paisnascimento
 * @property $z04_paisnacionalidade
 */
class CgmFisico extends Model
{
    public $timestamps = false;
    protected $table = 'protocolo.cgmfisico';
    protected $primaryKey = 'z04_sequencial';
    protected $fillable = [
        'z04_numcgm',
        'z04_rhcbo',
        'z04_nomesocial',
        'z04_paisnascimento',
        'z04_paisnacionalidade'
    ];
}
