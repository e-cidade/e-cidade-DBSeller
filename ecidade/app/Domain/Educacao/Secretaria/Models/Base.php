<?php

namespace App\Domain\Educacao\Secretaria\Models;

use App\Domain\Educacao\Escola\Models\CursoEdu;
use App\Domain\Educacao\Escola\Models\RegimeMatricula;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    protected $table = 'escola.base';
    public $timestamps = false;
    protected $primaryKey = 'ed31_i_codigo';
    protected $fillable = [
        'ed31_i_codigo',
        'ed31_i_curso',
        'ed31_c_descr',
        'ed31_c_turno',
        'ed31_c_medfreq',
        'ed31_c_contrfreq',
        'ed31_t_obs',
        'ed31_c_conclusao',
        'ed31_i_regimemat',
        'ed31_c_ativo'
    ];

    public function curso()
    {
        return $this->belongsTo(CursoEdu::class, 'ed31_i_curso', 'ed29_i_codigo');
    }

    public function regimeMatricula()
    {
        return $this->belongsTo(RegimeMatricula::class, 'ed31_i_regimemat', 'ed218_i_codigo');
    }

    public function baseEtapa()
    {
        return $this->belongsTo(BaseEtapa::class, 'ed31_i_codigo', 'ed87_i_codigo');
    }

    public function divisoesRegimeMatricula()
    {
        return $this->hasMany(BaseRegimeMatriculaDivisao::class, 'ed224_i_base', 'ed31_i_codigo');
    }

    public function disciplinasBase()
    {
        return $this->hasMany(BaseDisciplina::class, 'ed34_i_base', 'ed31_i_codigo');
    }
}
