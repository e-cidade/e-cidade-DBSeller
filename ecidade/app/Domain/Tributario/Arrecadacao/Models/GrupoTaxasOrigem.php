<?php

namespace App\Domain\Tributario\Arrecadacao\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoTaxasOrigem extends Model
{
    public $timestamps = false;
    protected $table = 'arrecadacao.grupotaxasorigem';
    protected $primaryKey = 'ar56_sequencial';
    protected $fillable = [
        "ar56_descricao"
    ];
}
