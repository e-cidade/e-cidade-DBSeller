<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $l16_sequencial
 * @property $l16_cadattdinamicovalorgrupo
 * @property $l16_liclicita
 */
class LiclicitaCadAttDinamicoValorGrupo extends Model
{
    protected $table = 'licitacao.liclicitacadattdinamicovalorgrupo';
    protected $primaryKey = 'l16_sequencial';
    public $timestamps = false;
}
