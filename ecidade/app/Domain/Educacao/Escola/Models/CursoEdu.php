<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class CursoEdu extends Model
{
    protected $table = 'escola.cursoedu';
    protected $primaryKey = 'ed29_i_codigo';
    public $timestamps = false;
    protected $fillable = [
        'ed29_i_codigo',
        'ed29_i_ensino',
        'ed29_c_descr',
        'ed29_c_historico',
        'ed29_i_avalparcial',
        'ed29_ativo',
        'ed29_censocursoprofiss'
    ];

    public function ensino()
    {
        return $this->belongsTo(Ensino::class, 'ed29_i_ensino', 'ed10_i_codigo');
    }
}
