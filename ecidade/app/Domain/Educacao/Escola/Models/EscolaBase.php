<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class EscolaBase extends Model
{
    protected $table = 'escola.escolabase';
    protected $primaryKey = 'ed77_i_codigo';
    public $timestamps = false;
    protected $fillable = [
        'ed77_i_codigo',
        'ed77_i_base',
        'ed77_i_escola',
        'ed77_i_basecont'
    ];

    public function atos()
    {
        return $this->hasMany(BaseAto::class, 'ed278_i_escolabase', 'ed77_i_codigo');
    }
}
