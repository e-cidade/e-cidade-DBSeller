<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pc11_codigo
 * @property int $pc11_numero
 * @property int $pc11_seq
 * @property double $pc11_quant
 * @property double $pc11_vlrun
 * @property string $pc11_prazo
 * @property string $pc11_pgto
 * @property string $pc11_resum
 * @property string $pc11_just
 * @property bool $pc11_liberado
 * @property bool $pc11_servicoquantidade
 * @property ItemProcessoMaterial $itemProcessoMaterial
 * @property ItemUnidade $itemUnidade
 */
class SolicitacaoItem extends Model
{
    public $timestamps = false;
    protected $table = 'compras.solicitem';
    protected $primaryKey = 'pc11_codigo';
    protected $appends = ['valor_total'];

    public function itemProcessoMaterial()
    {
        return $this->hasOne(ItemProcessoMaterial::class, 'pc16_solicitem', 'pc11_codigo');
    }

    public function itemUnidade()
    {
        return $this->hasOne(ItemUnidade::class, 'pc17_codigo', 'pc11_codigo');
    }

    public function solicitacaoProcessoCompraMaterial()
    {
        return $this->hasOne(SolicitacaoProcessoCompraMaterial::class, 'pc16_solicitem', 'pc11_codigo');
    }

    public function orcamentoItemSolicitacao()
    {
        return $this->hasOne(
            OrcamentoItemSolicitacao::class,
            'pc29_solicitem',
            'pc11_codigo'
        );
    }

    public function processoCompraItem()
    {
        return $this->hasOne(
            ProcessoCompraItem::class,
            'pc81_solicitem',
            'pc11_codigo'
        );
    }

    public function getValorTotalAttribute()
    {
        if (empty($this->pc11_vlrun) || empty($this->pc11_quant)) {
            return 0;
        }

        return round($this->pc11_vlrun * $this->pc11_quant, 4);
    }
}
