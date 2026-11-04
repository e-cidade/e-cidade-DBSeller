<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $l46_sequencial
 * @property $l46_liclicita
 * @property $l46_fase
 * @property $l46_liclicitatipoevento
 * @property $l46_dataevento
 * @property $l46_datajulgamento
 * @property $l46_cgm
 * @property $l46_tipopublicacao
 * @property $l46_descricaopublicacao
 * @property $l46_tiporesultado
 */
class EventoLicitacao extends Model
{
    protected $table = 'licitacao.liclicitaevento';
    protected $primaryKey = 'l46_sequencial';
    public $timestamps = false;
}
