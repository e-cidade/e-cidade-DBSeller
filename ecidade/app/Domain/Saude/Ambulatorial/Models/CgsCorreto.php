<?php
namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property s127_i_codigo
 * @property s127_i_numcgs
 * @property s127_d_data
 * @property s127_c_hora
 * @property s127_i_login
 * @property s127_b_proc
 * @property s127_i_instit
 * @property s127_data_proc
 * @property s127_hora_proc
 * @property s127_login_proc
 */

class CgsCorreto extends Model
{
    protected $table = 'ambulatorial.sau_cgscorreto';
    protected $primaryKey = 's127_i_codigo';
    public $timestamps = false;

    public function scopeCgsCorreto(Builder $query, $cgsCorreto)
    {
        return $query->where('s127_i_numcgs', '=', $cgsCorreto);
    }

    public function scopeUnificado(Builder $query, $unificado)
    {
        return $query->where('s127_b_proc', '=', $unificado);
    }
}
