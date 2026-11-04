<?php

namespace App\Domain\Educacao\Secretaria\Models;

use Illuminate\Database\Eloquent\Model;

class ZonaResidencia extends Model
{
    protected $table = "secretariadeeducacao.zonas_residencia";
    protected $primaryKey = "ed194_id";
    public $incrementing = true;
}
