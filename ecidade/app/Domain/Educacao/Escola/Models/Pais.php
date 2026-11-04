<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = "escola.pais";
    protected $primaryKey = "ed228_i_codigo";

    protected $fillable = [
        'ed228_c_descr',
        'ed228_i_paisonu',
        'ed228_c_abrev'
    ];
    public $timestamps = false;
}
