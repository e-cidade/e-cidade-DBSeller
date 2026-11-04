<?php

namespace App\Domain\Patrimonial\Compras\Models;

use App\Domain\Patrimonial\PNCP\Models\ComprasPncp;
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
class Solicitacao extends Model
{
    public $timestamps = false;
    protected $table = 'compras.solicita';
    protected $primaryKey = 'pc10_numero';

    public function itens()
    {
        return $this
            ->hasMany(SolicitacaoItem::class, 'pc11_numero', 'pc10_numero')
            ->orderBy('pc11_seq', 'ASC');
    }

    public function processoAdministrativo()
    {
        return $this->hasOne(SolicitacaoProcesso::class, 'pc90_solicita', 'pc10_numero');
    }

    public function compraPncp()
    {
        return $this->hasOne(ComprasPncp::class, 'pn03_solicita', 'pc10_numero');
    }
}
