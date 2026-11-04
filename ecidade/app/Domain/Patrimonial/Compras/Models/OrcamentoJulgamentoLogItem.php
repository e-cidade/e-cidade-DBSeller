<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pc93_sequencial,
 * @property $pc93_pcorcamjulgamentolog,
 * @property $pc93_pcorcamitem,
 * @property $pc93_pcorcamforne,
 * @property $pc93_valorunitario,
 * @property $pc93_pontuacao,
 */
class OrcamentoJulgamentoLogItem extends Model
{
    public $timestamps = false;
    protected $table = 'compras.pcorcamjulgamentologitem';
}
