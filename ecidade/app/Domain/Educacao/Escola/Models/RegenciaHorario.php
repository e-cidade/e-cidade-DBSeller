<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class RegenciaHorario extends Model
{
    protected $table = 'escola.regenciahorario';
    protected $primaryKey = 'ed58_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    public function regencia()
    {
        return $this->belongsTo(Regencia::class, 'ed58_i_regencia', 'ed59_i_codigo');
    }
}
