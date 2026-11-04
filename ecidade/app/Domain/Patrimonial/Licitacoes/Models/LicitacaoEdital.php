<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $l27_sequencial
 * @property $l27_arquivo
 * @property $l27_arqnome
 * @property $l27_liclicita
 */
class LicitacaoEdital extends Model
{
    protected $table = 'licitacao.liclicitaedital';
    protected $primaryKey = 'l27_sequencial';
    public $timestamps = false;
}
