<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pc24_orcamitem,
 * @property $pc24_pontuacao,
 * @property $pc24_orcamforne,
 *
 */
class OrcamentoJulgamento extends Model
{
    public $timestamps = false;
    protected $table = 'compras.pcorcamjulg';
}
