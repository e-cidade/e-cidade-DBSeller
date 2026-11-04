<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RhDepend extends Model
{
    protected $table = 'pessoal.rhdepend';
    protected $primaryKey = 'rh31_codigo';

    public $timestamps = false;

    public function dependeplug()
    {
        return $this->hasOne(RhDependePlug::class, 'dp01_rhdepend', 'rh31_codigo');
    }

    public function ajuda()
    {
        return $this->hasMany(AjudaCusto::class, 'rh312_dependente', 'rh31_codigo');
    }

    public function idade()
    {
        return Carbon::parse($this->attributes['rh31_dtnasc'])->age;
    }
}
