<?php

namespace App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models;

use Illuminate\Database\Eloquent\Model;

class ConcessaoErros extends Model
{
    public $timestamps = false;
    protected $table    = "recursoshumanos.concessao_direitos_erros";
    protected $primaryKey = "rh509_sequencial";
    protected $fillable = [
        'rh509_matricula',
        'rh509_erro'
    ];
}
