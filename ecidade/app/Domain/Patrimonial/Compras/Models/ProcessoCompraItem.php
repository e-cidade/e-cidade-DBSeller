<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pc81_codproc
 * @property int $pc81_codprocitem
 * @property int $pc81_solicitem
 * @property SolicitacaoItem $itemSolicitacao
 */
class ProcessoCompraItem extends Model
{
    public $timestamps = false;
    protected $table = 'compras.pcprocitem';
    protected $primaryKey = 'pc81_codprocitem';

    public function itemSolicitacao()
    {
        return $this->belongsTo(SolicitacaoItem::class, 'pc81_solicitem', 'pc11_codigo');
    }

    public function orcamentoItemProcesso()
    {
        return $this->hasOne(OrcamentoItemProcessoCompra::class, 'pc31_pcprocitem', 'pc81_codprocitem');
    }
}
