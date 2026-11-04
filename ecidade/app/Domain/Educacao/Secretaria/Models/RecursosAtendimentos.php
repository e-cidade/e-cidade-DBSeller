<?php


namespace App\Domain\Educacao\Secretaria\Models;

use Illuminate\Database\Eloquent\Model;

class RecursosAtendimentos extends Model
{
    protected $table = 'escola.recursosatendimentos';
    protected $primaryKey = 'ed199_codigo';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable  = [
        "ed199_codigo",
        "ed199_alunoatendimentos",
        "ed199_recursosutilizados"
    ];
}
