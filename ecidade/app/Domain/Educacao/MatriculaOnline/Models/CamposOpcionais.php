<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class CamposOpcionais extends Model
{
    protected $table = 'plugins.camposopicionais';
    public $timestamps = false;
    protected $primaryKey = 'mo42_codigo';
}
