<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class HabilidadesBNCCReferencial extends Model
{
    protected $table = "escola.bnccreferencial";
    protected $primaryKey = 'ed168_codigo';
}
