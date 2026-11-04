<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class Parametros extends Model
{
    protected $table = "escola.edu_parametros";
    protected $primaryKey = 'ed233_i_codigo';
    public $timestamps = false;
    public $incrementing = false;
}
