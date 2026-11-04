<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class HabilidadeDesenvolvida extends Model
{
    protected $table = "escola.diario_classe_bncc_habilidade";
    protected $primaryKey = 'ed156_codigo';
    public $timestamps = false;
    protected $fillable = [
        'ed156_codigo',
        'ed156_diario_classe_bncc',
        'ed156_bnccdisciplinas',
        'ed156_habilidade'
    ];

    public function conteudo()
    {
        return $this->belongsTo(
            ConteudoDesenvolvido::class,
            'ed156_diario_classe_bncc',
            'ed155_codigo'
        );
    }
}
