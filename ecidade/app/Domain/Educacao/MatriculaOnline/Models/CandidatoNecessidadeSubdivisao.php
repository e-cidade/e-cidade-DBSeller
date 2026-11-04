<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class CandidatoNecessidadeSubdivisao extends Model
{
    protected $table = 'matriculaonline.base_necessidade_subdivisao';
    public $timestamps = false;
    protected $primaryKey = 'mo26_id';
}
