<?php

namespace App\Domain\Tributario\Arrecadacao\Models;

use Illuminate\Database\Eloquent\Model;

class TaxasLancadas extends Model
{
    public $timestamps = false;
    protected $table = 'arrecadacao.taxaslancadas';
    protected $primaryKey = 'ar44_sequencial';
    protected $fillable = [
        "ar44_descricao",
        "ar44_valorinflator",
        "ar44_inflator",
        "ar44_diasvencimento",
        "ar44_tipo",
        "ar44_receitaxaexpediente",
        "ar44_valortaxaexpediente",
        "ar44_datavigencia",
        "ar44_procedencia",
        "ar44_receita",
        "ar44_emissaoweb",
        "ar44_recursoadm",
        "ar44_origem",
        "ar44_permitesubtaxas"
    ];
}
