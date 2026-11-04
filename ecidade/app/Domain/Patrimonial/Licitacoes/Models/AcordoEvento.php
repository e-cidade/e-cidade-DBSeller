<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Model;

class AcordoEvento extends Model
{
    public $timestamps = false;
    public $fillable = [
        'ac55_tipoevento',
        'ac55_acordo',
        'ac55_data',
        'ac55_veiculocomunicacao',
        'ac55_numeroprocesso',
        'ac55_anoprocesso',
        'ac55_descricaopublicacao'
    ];
    protected $table = 'acordos.acordoevento';
    protected $primaryKey = 'ac55_sequencial';
}
