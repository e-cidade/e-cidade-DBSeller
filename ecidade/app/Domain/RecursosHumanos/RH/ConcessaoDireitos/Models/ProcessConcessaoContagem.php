<?php

namespace App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessConcessaoContagem extends Model
{
    public $timestamps = false;
    protected $table    = "recursoshumanos.process_concessao_count";
    protected $primaryKey = "rh510_sequencial";
    protected $fillable = [
        'rh510_total',
        'rh510_quantidade'
    ];
}
