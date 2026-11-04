<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Calculo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Gerfsal
 * @property int r14_anousu
 * @property int r14_mesusu
 * @property int r14_regist
 * @property string r14_rubric
 * @property string r14_valor
 * @property string r14_pd
 * @property string r14_quant
 * @property string r14_lotac
 * @property int r14_semest
 * @property int r14_instit
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Calculo
 */
class Gerfsal extends Model
{
    protected $table = 'pessoal.gerfsal';
    protected $primaryKey = ['r14_anousu,', 'r14_mesusu', 'r14_regist', 'r14_rubric', 'r14_pd'];
    public $incrementing = false;
    public $timestamps = false;
}
