<?php

namespace App\Domain\Educacao\Secretaria\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadeCurricular extends Model
{
    protected $table = "secretariadeeducacao.unidadescurriculares";
    protected $primaryKey = "ed199_id";
    public $incrementing = true;

    protected $fillable = [
        'ed199_id',
        'ed199_descricao'
    ];

    public $timestamps = false;
}
