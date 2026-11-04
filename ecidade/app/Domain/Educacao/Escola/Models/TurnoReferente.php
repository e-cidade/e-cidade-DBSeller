<?php

namespace App\Domain\Educacao\Escola\Models;

use App\Domain\Educacao\Escola\Enums\TurnoReferenteEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TurnoReferente
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed231_i_codigo
 * @property Turno $ed231_i_turno
 * @property integer $ed231_i_referencia
 */
class TurnoReferente extends Model
{
    protected $table = 'escola.turnoreferente';
    protected $primaryKey = 'ed231_i_codigo';
    public $timestamps = false;
    public $incrementing = false;
    protected $appends = ['referencia'];

    public function getReferenciaAttribute()
    {
        return new TurnoReferenteEnum($this->ed231_i_referencia);
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'ed231_i_turno', 'ed15_i_codigo');
    }
}
