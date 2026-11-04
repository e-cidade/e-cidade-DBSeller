<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class HabilidadeDsenvolvidaReferencial extends Model
{
    protected $table = "escola.diario_classe_bncc_habilidade_referencial";
    protected $primaryKey = 'ed169_codigo';
    public $timestamps = false;
    protected $fillable = [
        'ed169_codigo',
        'ed169_diario_classe_bncc_habilidade',
        'ed169_bnccreferencial'
    ];
}
