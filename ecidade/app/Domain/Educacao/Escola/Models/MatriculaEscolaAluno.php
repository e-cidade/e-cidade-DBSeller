<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Domain\Educacao\CentralMatriculas\Models
 * @property integer $codigo
 * @property string $matricula
 * @property integer $aluno
 */
class MatriculaEscolaAluno extends Model
{
    protected $table = 'escola.matriculaescolaaluno';
    protected $keyType = 'string';
    protected $primaryKey = 'ed365_codigo';
    protected $fillable = [
        'ed365_codigo',
        'ed365_matricula',
        'ed365_datamatriculainicial',
        'ed365_aluno',
        'ed365_escola',
    ];

    public $timestamps = false;
    public $incrementing = false;
}
