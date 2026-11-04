<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @class ConplanoOrcamentoAnalitica
 * @namespace App\Domain\Financeiro\Contabilidade\Models;
 * @property int $c61_codcon
 * @property int $c61_anousu
 * @property int $c61_reduz
 * @property int $c61_instit
 * @property int $c61_codigo
 * @property int $c61_contrapartida
 */
class ConplanoOrcamentoAnalitica extends Model
{
    protected $table = 'contabilidade.conplanoorcamentoanalitica';
    public $timestamps = false;
}
