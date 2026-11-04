<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class RegimeMatriculaDivisao extends Model
{
    protected $table = 'escola.regimematdiv';
    protected $primaryKey = 'ed219_i_codigo';
    public $timestamps = false;
    protected $fillable = [
        'ed219_i_codigo',
        'ed219_i_regimemat',
        'ed219_c_nome',
        'ed219_c_abrev',
        'ed219_i_ordenacao'
    ];
}
