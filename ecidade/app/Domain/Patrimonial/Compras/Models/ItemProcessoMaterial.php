<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pc16_codmater
 * @property int $pc16_solicitem
 * @property ProcessoCompraMaterial $processoCompraMaterial
 */
class ItemProcessoMaterial extends Model
{
    public $timestamps = false;
    protected $table = 'compras.solicitempcmater';
    protected $primaryKey = 'pc16_codmater';

    public function processoCompraMaterial()
    {
        return $this->hasOne(ProcessoCompraMaterial::class, 'pc01_codmater', 'pc16_codmater');
    }
}
