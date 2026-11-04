<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pc26_orcamitem
 * @property $pc26_liclicitem
 */
class OrcamentoItemLicitacao extends Model
{
    public $timestamps = false;
    protected $table = 'licitacao.pcorcamitemlic';
}
