<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property s128_i_codigo
 * @property s128_i_numcgs
 * @property s128_v_nome
 */
class CgsErrado extends Model
{
    protected $table = 'ambulatorial.sau_cgserrado';
    protected $primaryKey = 's128_i_codigo';
    public $timestamps = false;
}
