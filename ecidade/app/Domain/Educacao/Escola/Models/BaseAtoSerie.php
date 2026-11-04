<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class BaseAtoSerie extends Model
{
    protected $table = 'escola.baseatoserie';
    protected $primaryKey = 'ed279_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ed279_i_codigo', 'ed279_i_baseato', 'ed279_i_serie'
    ];
}
