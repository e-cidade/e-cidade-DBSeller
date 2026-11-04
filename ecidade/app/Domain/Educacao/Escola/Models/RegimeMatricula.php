<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class RegimeMatricula extends Model
{
    protected $table = 'escola.regimemat';
    protected $primaryKey = 'ed218_i_codigo';

    public function divisoes()
    {
        return $this
            ->hasMany(DivisoesRegimeMatricula::class, 'ed219_i_regimemat', 'ed218_i_codigo');
    }
}
