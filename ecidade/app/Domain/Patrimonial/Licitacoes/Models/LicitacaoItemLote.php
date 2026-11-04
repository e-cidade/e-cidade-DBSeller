<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $l04_codigo
 * @property int $l04_liclicitem
 * @property string $l04_descricao
 */
class LicitacaoItemLote extends Model
{
    public $timestamps = false;
    protected $table = 'licitacao.liclicitemlote';
    protected $primaryKey = 'l04_codigo';
}
