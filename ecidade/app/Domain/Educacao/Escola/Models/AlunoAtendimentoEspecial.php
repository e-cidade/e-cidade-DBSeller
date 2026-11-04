<?php


namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class AlunoAtendimentoEspecial extends Model
{
    protected $table = 'escola.alunoatendimentoespecial';
    protected $primaryKey = 'ed197_codigo';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable  = [
        "ed197_codigo",
        "ed197_aluno",
        "ed197_atendimentos",
        "ed197_data_emissao",
        "ed197_parecer"
    ];
}
