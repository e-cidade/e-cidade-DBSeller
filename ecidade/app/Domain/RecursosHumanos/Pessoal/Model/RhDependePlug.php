<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use Illuminate\Database\Eloquent\Model;

class RhDependePlug extends Model
{
    protected $table = 'pessoal.rhdependeplug';
    protected $primaryKey = 'dp01_codigo';

    public $timestamps = false;

    public function dependente()
    {
        $this->belongsTo(RhDepend::class, 'dp01_rhdepend', 'rh31_codigo');
    }
}
