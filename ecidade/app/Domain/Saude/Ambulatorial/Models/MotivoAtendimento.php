<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa um motivo de atendimento
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property int s144_i_codigo
 * @property string s144_c_descr
 */

class MotivoAtendimento extends Model
{
    protected $table = 'ambulatorial.sau_motivoatendimento';
    protected $primaryKey = 's144_i_codigo';
    public $timestamps = false;
}
