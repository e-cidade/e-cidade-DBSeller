<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Fundeb;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RhCalculoFundeb
 * @property int rh285_ano
 * @property int rh285_mes
 * @property double rh285_valor
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Fundeb
 */
class RhCalculoFundeb extends Model
{
    protected $table = 'pessoal.rhcalculofundeb';
    protected $primaryKey = ['rh285_ano', 'rh285_mes'];
    public $incrementing = false;
    public $timestamps = false;
}
