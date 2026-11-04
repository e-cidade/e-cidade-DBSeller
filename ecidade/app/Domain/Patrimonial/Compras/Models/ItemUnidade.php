<?php

namespace App\Domain\Patrimonial\Compras\Models;

use App\Domain\Patrimonial\Material\Models\UnidadeMaterial;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pc17_unid
 * @property int $pc17_quant
 * @property int $pc17_codigo
 * @property UnidadeMaterial $materialUnidade
 */
class ItemUnidade extends Model
{
    public $timestamps = false;
    protected $table = 'compras.solicitemunid';
    protected $primaryKey = 'pc17_codigo';

    public function materialUnidade()
    {
        return $this->belongsTo(UnidadeMaterial::class, 'pc17_unid', 'm61_codmatunid');
    }
}
