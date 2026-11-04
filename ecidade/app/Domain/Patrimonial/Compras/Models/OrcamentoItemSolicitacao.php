<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pc29_orcamitem
 * @property int $pc29_solicitem
 */
class OrcamentoItemSolicitacao extends Model
{
    public $timestamps = false;
    protected $table = 'compras.pcorcamitemsol';

    public function orcamentoItem()
    {
        return $this->hasOne(OrcamentoItem::class, 'pc22_orcamitem', 'pc29_orcamitem');
    }
}
