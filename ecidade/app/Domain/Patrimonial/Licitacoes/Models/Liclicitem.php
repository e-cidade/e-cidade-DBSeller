<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $l21_codigo
 * @property $l21_codliclicita
 * @property $l21_codprocitem
 * @property $l21_situacao
 * @property $l21_ordem
 */
class Liclicitem extends Model
{
    protected $table = 'licitacao.liclicitem';
    protected $primaryKey = 'l21_codigo';
    public $timestamps = false;
}
