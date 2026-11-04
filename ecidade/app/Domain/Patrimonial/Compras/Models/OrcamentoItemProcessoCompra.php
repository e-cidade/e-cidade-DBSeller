<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pc31_orcamitem
 * @property int $pc31_pcprocitem
 */
class OrcamentoItemProcessoCompra extends Model
{
    public $timestamps = false;
    protected $table = 'compras.pcorcamitemproc';

    public function orcamentoItem()
    {
        return $this->hasOne(OrcamentoItem::class, 'pc22_orcamitem', 'pc31_orcamitem');
    }
}
