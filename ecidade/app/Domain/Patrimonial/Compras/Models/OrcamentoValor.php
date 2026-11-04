<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pc23_orcamforne,
 * @property $pc23_orcamitem,
 * @property $pc23_valor,
 * @property $pc23_quant,
 * @property $pc23_obs,
 * @property $pc23_vlrun,
 * @property $pc23_validmin,
 * @property $pc23_percentualdesconto,
 * @property $pc23_bdi,
 * @property $pc23_encargossociais,
 * @property $pc23_data,
 * @property $pc23_notatecnica,
 * @property $pc23_taxaestimada,
 * @property $pc23_taxahomologada
 */
class OrcamentoValor extends Model
{
    public $timestamps = false;
    protected $table = 'compras.pcorcamval';
}
