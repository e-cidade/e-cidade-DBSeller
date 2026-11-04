<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Model;

class LiclicitaEncerramentoLicitacon extends Model
{
    protected $table = 'licitacao.liclicitaencerramentolicitacon';
    protected $primaryKey = 'l18_sequencial';
    public $timestamps = false;
}
