<?php

namespace App\Domain\Patrimonial\Compras\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pc10_numero
 * @property $pc10_data,
 * @property string pc10_resumo,
 * @property int pc10_depto,
 * @property int pc10_log,
 * @property int pc10_instit,
 * @property bool pc10_correto,
 * @property int pc10_login,
 * @property int pc10_solicitacaotipo
 * @property SolicitacaoItem $itens
 */
class SolicitacaoProcesso extends Model
{
    public $timestamps = false;
    protected $table = 'compras.solicitaprotprocesso';
    protected $primaryKey = 'pc90_sequencial';
}
