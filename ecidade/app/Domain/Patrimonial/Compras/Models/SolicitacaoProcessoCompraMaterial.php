<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pc16_codmater
 * @property int $pc16_solicitem
 */
class SolicitacaoProcessoCompraMaterial extends Model
{
    public $timestamps = false;
    protected $table = 'compras.solicitempcmater';

    public function processoCompraMaterial()
    {
        return $this->hasOne(ProcessoCompraMaterial::class, 'pc01_codmater', 'pc16_codmater');
    }
}
