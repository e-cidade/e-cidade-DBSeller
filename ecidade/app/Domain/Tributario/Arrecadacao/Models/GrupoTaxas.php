<?php

namespace App\Domain\Tributario\Arrecadacao\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Tributario\Diversos\Models\Procedencia;

class GrupoTaxas extends Model
{
    public $timestamps = false;
    protected $table = 'arrecadacao.grupotaxas';
    protected $primaryKey = 'ar55_sequencial';
    protected $fillable = [
        "ar55_descricao",
        "ar55_procedenciaprinc",
        "ar55_origem",
        "ar55_datalimite"
    ];

    public function procedencia()
    {
        return $this->hasOne(
            Procedencia::class,
            'dv09_procdiver',
            'ar55_procedenciaprinc'
        );
    }

    /**
     * @param Integer $sequencialGrupo
     */
    public function getTaxas($sequencialGrupo)
    {
        return TaxasLancadas::select('taxaslancadas.*')
        ->join('taxagrupotaxas', 'taxagrupotaxas.ar57_taxa', '=', 'taxaslancadas.ar44_sequencial')
        ->join('grupotaxas', 'grupotaxas.ar55_sequencial', '=', 'taxagrupotaxas.ar57_grupotaxas')
        ->where('ar55_sequencial', $sequencialGrupo)
        ->get();
    }

    public function taxas()
    {
        return $this->belongsToMany(TaxasLancadas::class, 'taxagrupotaxas', 'ar57_grupotaxas', 'ar57_taxa');
    }

    public function origem()
    {
        return $this->hasOne(GrupoTaxasOrigem::class, 'ar56_sequencial', 'ar55_origem');
    }
}
