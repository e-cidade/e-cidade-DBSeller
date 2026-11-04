<?php

namespace App\Domain\Patrimonial\Material\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $m61_codmatunid
 * @property $m61_descr
 * @property $m61_usaquant
 * @property $m61_abrev
 * @property $m61_usadec
 * @property $m61_codigotribunal
 *
 */
class MaterialUnidade extends Model
{
    protected $table = 'matunid';
    protected $primaryKey = 'm61_codmatunid';
    public $timestamps = false;
}
