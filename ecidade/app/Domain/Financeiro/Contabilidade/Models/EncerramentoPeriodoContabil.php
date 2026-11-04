<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $c99_anousu
 * @property $c99_instit
 * @property $c99_data
 * @property $c99_usuario
 */
class EncerramentoPeriodoContabil extends Model
{
    protected $table = 'contabilidade.condataconf';
    public $timestamps = false;

    protected $dates = ['c99_data'];
}
