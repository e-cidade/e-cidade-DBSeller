<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class RendaFamiliar extends Model
{
    protected $table = 'matriculaonline.renda_familiar';
    public $timestamps = false;
    protected $primaryKey = 'mo25_id';
    public $incrementing = true;
}
