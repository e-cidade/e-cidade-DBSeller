<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use App\Domain\Patrimonial\Compras\Models\ProcessoCompraItem;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $l21_codigo
 * @property int $l21_codliclicita
 * @property int $l21_codpcprocitem
 * @property int $l21_situacao
 * @property int $l21_ordem
 * @property Licitacao $licitacao
 * @property LicitacaoItemLote $lote
 * @property ProcessoCompraItem $itemProcessoCompra
 */
class Item extends Model
{
    protected $table = 'licitacao.liclicitem';
    protected $primaryKey = 'l21_codigo';
    public $timestamps = false;

    public function licitacao()
    {
        return $this->hasOne(Licitacao::class, 'l21_codliclicita', 'l20_codigo');
    }

    public function lote()
    {
        return $this->hasOne(LicitacaoItemLote::class, 'l04_liclicitem', 'l21_codigo');
    }

    public function itemProcessoCompra()
    {
        return $this->belongsTo(ProcessoCompraItem::class, 'l21_codpcprocitem', 'pc81_codprocitem');
    }
}
