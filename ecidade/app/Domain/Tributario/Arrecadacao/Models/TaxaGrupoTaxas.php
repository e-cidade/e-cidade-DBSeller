<?php

namespace App\Domain\Tributario\Arrecadacao\Models;

use Illuminate\Database\Eloquent\Model;

class TaxaGrupoTaxas extends Model
{
    public $timestamps = false;
    protected $table = 'arrecadacao.taxagrupotaxas';
    protected $primaryKey = 'ar57_sequencial';
    protected $fillable = [
        "ar57_taxa",
        "ar57_grupotaxas",
    ];

    public function grupoTaxas()
    {
        return $this->hasOne(
            GrupoTaxas::class,
            'ar55_sequencial',
            'ar57_grupotaxas'
        );
    }
}
